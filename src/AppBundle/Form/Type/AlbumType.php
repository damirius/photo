<?php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type as FormType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

use AppBundle\Entity\Album;
use AppBundle\Form\DataTransformer\TagTransformer;
use AppBundle\Entity\Category;
use AppBundle\Entity\Tag;

class AlbumType extends AbstractType
{

    private $manager;

    public function __construct(ObjectManager $manager)
    {
        $this->manager = $manager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('title', FormType\TextType::class)
            ->add('description', FormType\TextareaType::class)
            ->add('category', EntityType::class, [
                'class' => 'AppBundle:Category',
                'choice_label' => 'name',
                'choice_value' => 'name'
            ])
            ->add('tags', EntityType::class, [
                'class' => 'AppBundle:Tag',
                'choice_label' => 'name',
                'choice_value' => 'name',
                'multiple' => true
            ])
            ->add('save', FormType\SubmitType::class, array('label' => 'Submit Album'));

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function(FormEvent $e){
            $data = $e->getData();
            if($this->manager->getRepository('AppBundle:Category')->findOneBy(array("name"=>$data['category'])) ===
                null) {
                $category = new Category();
                $category->setName($data['category']);
                $this->manager->persist($category);
                $data['category'] = $category->getId();
            }
            $newTagArray = [];
            foreach(array_unique(array_map('trim',$data['tags'])) as $tagName){
                $tag = $this->manager->getRepository('AppBundle:Tag')->findOneBy(array("name"=>$tagName));
                if($tag === null) {
                    $tag = new Tag();
                    $tag->setName($tagName);
                    $this->manager->persist($tag);
                }
                $newTagArray[]=$tagName;
            }
            $this->manager->flush();
            dump($newTagArray);
            $data['tags'] = $newTagArray;
            $e->setData($data);
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Album::class,
        ));
    }
}