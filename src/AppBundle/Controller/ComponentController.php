<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class ComponentController extends Controller
{

    public function mainNavbarAction()
    {
        $em = $this->getDoctrine()->getManager();
        $categories = $em->getRepository('AppBundle:Category')->findAll();
        $masterRequest = $this->getMasterRequest();
        $currentUri = $masterRequest->getRequestUri();
        $currentRoute = $masterRequest->get('_route');
        return $this->render(
            "@App/components/main_navbar.html.twig",
            [
                'currentUri' => $currentUri,
                'route' => $currentRoute,
                'categories' => $categories
            ]);
    }

    /**
     * Get top level Request object
     */
    private function getMasterRequest()
    {
        $stack = $this->get('request_stack');
        $masterRequest = $stack->getMasterRequest();
        return $masterRequest;
    }
}
