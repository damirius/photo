<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use AppBundle\Form\Type\UserType;
use AppBundle\Entity\Comment;
use AppBundle\Form\Type\CommentType;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $albums = $em->getRepository('AppBundle:Album')->findAllWithPicture();
        return $this->render('@App/index.html.twig', [
            'albums' => $albums,
        ]);
    }

    /**
     * @Route("/album/{id}", name="album_view", requirements={"id" = "\d+"})
     */
    public function albumAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $album = $em->getRepository('AppBundle:Album')->findOneWithPicture($id);
        if($album === null) {
            throw $this->createNotFoundException('This album does not exist');
        }
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $comment->setUser($this->getUser());
            $comment->setAlbum($album);
            $em->persist($comment);
            $em->flush();
            $comment = new Comment();
            $form = $this->createForm(CommentType::class, $comment);
        }
        $comments = $em->getRepository('AppBundle:Comment')->findBy(['album'=>$album],['created'=>'DESC']);
        return $this->render('@App/album.html.twig', [
            'album' => $album,
            'comments' => $comments,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/tag/{tag}", name="tag_view", requirements={"tag"})
     */
    public function tagAction($tag, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $albums = $em->getRepository('AppBundle:Album')->findAllByTag($tag);
        return $this->render('@App/tag.html.twig', [
            'albums' => $albums,
            'tag' => $tag,
        ]);
    }

    /**
     * @Route("/category/{category}", name="category_view", requirements={"category"})
     */
    public function categoryAction($category, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $category = $em->getRepository('AppBundle:Category')->findOneBy(['name'=>$category]);
        if($category === null) {
            throw $this->createNotFoundException('This category does not exist');
        }
        $albums = $em->getRepository('AppBundle:Album')->findAllByCategory($category->getName());
        return $this->render('@App/category.html.twig', [
            'albums' => $albums,
            'category' => $category,
        ]);
    }

    /**
     * @Route("/search", name="search_view")
     */
    public function searchAction( Request $request)
    {
        $search = $request->query->get('q');
        if(!isset($search) || $search=="") {
            throw $this->createNotFoundException('Not Found');
        }
        $em = $this->getDoctrine()->getManager();
        $albums = $em->getRepository('AppBundle:Album')->findAllByQuery($search);
        return $this->render('@App/search.html.twig', [
            'albums' => $albums,
            'search' => $search
        ]);
    }

    /**
     * @Route("/profile", name="profile_view")
     */
    public function profileAction(Request $request)
    {
        $user = $this->getUser();
        $userForm = $this->createForm(UserType::class, $user);
        $userForm->handleRequest($request);
        if ($userForm->isSubmitted() && $userForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $imagineCacheManager = $this->get('liip_imagine.cache.manager');
            $imagineCacheManager->remove($user->getAvatarName(),'default_avatar');
            $uploadableManager = $this->get('stof_doctrine_extensions.uploadable.manager');
            $uploadableManager->markEntityToUpload($user, $userForm->get('newavatar')->getData());
            $em->persist($user);
            $em->flush();
        }

        return $this->render('@App/profile.html.twig',[
            'userForm'=>$userForm->createView()]);
    }
}
