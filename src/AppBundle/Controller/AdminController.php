<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type as FormType;

use AppBundle\Entity\Album;
use AppBundle\Form\Type\AlbumType;
use AppBundle\Entity\Photo;
use AppBundle\Form\Type\PhotoType;
use AppBundle\Gedmo\Uploadable\FileInfo\WebFileInfo;
/**
 * @Route("/admin")
 */
class AdminController extends Controller
{
    /**
     * @Route("/", name="admin_index")
     */
    public function indexAction(Request $request)
    {
        return $this->render('AppBundle:admin:index.html.twig',[]);
    }

    /**
     * @Route("/albums", name="admin_albums")
     */
    public function albumsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $albums = $em->getRepository('AppBundle:Album')->findBy(array(), array('created' => 'DESC'));
        return $this->render('AppBundle:admin:albums.html.twig',['albums'=>$albums]);
    }

    /**
     * @Route("/albums/add", name="admin_albums_add")
     */
    public function addAlbumsAction(Request $request)
    {
        $album = new Album();
        $form = $this->createForm(AlbumType::class, $album);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $album = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($album);
            $em->flush();
            $this->addFlash('success', 'Album created');
        }

        return $this->render('AppBundle:admin:add_album.html.twig',['form' => $form->createView()]);
    }

    /**
     * @Route("/albums/edit/{id}", requirements={"id" = "\d+"}, name="admin_albums_edit")
     */
    public function editAlbumsAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $album = $em->getRepository('AppBundle:Album')->find($id);
        if($album === null) {
            $this->addFlash('danger', 'Album with this ID doesn\'t exist!');
            $this->redirectToRoute('admin_albums');
        }
        $form = $this->createForm(AlbumType::class, $album);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $album = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($album);
            $em->flush();
            $this->addFlash('success', 'Album updated');
        }

        $photo = new Photo();
        $photoForm = $this->createForm(PhotoType::class, $photo);
        $photoForm->handleRequest($request);
        if ($photoForm->isSubmitted() && $photoForm->isValid()) {
            $photo->setAlbum($album);
            $uploadableManager = $this->get('stof_doctrine_extensions.uploadable.manager');
            $uploadableManager->markEntityToUpload($photo, $photo->getPath());
            $em->persist($photo);
            $em->flush();
        }

        return $this->render('AppBundle:admin:edit_album.html.twig',['form' => $form->createView(),
            'photoForm'=>$photoForm->createView()]);
    }
}
