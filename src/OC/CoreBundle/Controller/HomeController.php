<?php

namespace OC\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;


class HomeController extends Controller
{
    public function indexAction()
    {   
        $limit = 4;
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('OCPlatformBundle:Advert');
        $listAdverts = $repository->findBy([], null, $limit);


        return $this->render('@OCCore/Home/home.html.twig', compact('listAdverts'));
    }

    public function contactAction(Request $request)
    {
        $session = $request->getSession();
        $session->getFlashBag()->add('info', "La page de contact n'est pas encore disponible, merci de revenir plus tard.");


        return $this->redirectToRoute('oc_core_homepage');
    }
}
