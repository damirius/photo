<?php
namespace AppBundle\Form\DataTransformer;

use AppBundle\Entity\Tag;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\DataTransformerInterface;
use Doctrine\Common\Collections\ArrayCollection;

class TagTransformer implements DataTransformerInterface
{
    private $manager;

    public function __construct(ObjectManager $manager)
    {
        $this->manager = $manager;
    }


    public function transform($tagCollection)
    {
        dump($tagCollection);
        if ($tagCollection->count() == 0) {
            return array();
        } else {
            $tags = array();
            foreach ($tagCollection as $tag)
            {
                $tags[] = sprintf("%s_%s", $tag->getId(), basename(str_replace('\\', '/', get_class($tag))));
            }
            return $tags;
        }

    }

    /**
     * Transforms a comma separated string to a Tag ArrayCollection
     *
     * @param  string $tags
     * @return ArrayCollection|null
     */
    public function reverseTransform($tags)
    {
        $tagCollection = new ArrayCollection();

        foreach(explode(',',$tags) as $name){
            $tag = $this->manager->getRepository('AppBundle:Tag')->findOneBy(array("name"=>trim($name)));
            $tagCollection->add($tag);

        }
        return $tagCollection;
    }
}