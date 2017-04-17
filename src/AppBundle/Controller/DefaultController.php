<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

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
        return $this->render('@App/album.html.twig', [
            'album' => $album,
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
}
