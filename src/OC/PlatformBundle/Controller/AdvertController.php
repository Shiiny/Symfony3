<?php

namespace OC\PlatformBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use OC\PlatformBundle\Form\AdvertType;
use OC\PlatformBundle\Form\AdvertEditType;
use OC\PlatformBundle\Entity\Advert;
use OC\PlatformBundle\Entity\ImageEntity;
use OC\PlatformBundle\Entity\Application;
use OC\PlatformBundle\Entity\Skill;
use OC\PlatformBundle\Entity\AdvertSkill;


class AdvertController extends Controller
{
    public function indexAction($page)
    {        
        if($page < 1 ) {
            throw $this->createNotFoundException("La Page ".$page." n'existe pas.");
        }
        $nbPerPage = 2;

        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('OCPlatformBundle:Advert');
        $listAdverts = $repository->getAdverts($page, $nbPerPage);

        $nbPages = ceil(count($listAdverts)/$nbPerPage);

        if($page > $nbPages) {
            throw $this->createNotFoundException("La Page ".$page." n'existe pas.");
        }
        dump($listAdverts);

        return $this->render('@OCPlatform/Advert/index.html.twig', compact('listAdverts', 'nbPages', 'page'));
    }

    public function menuAction($limit)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('OCPlatformBundle:Advert');

        $listAdverts = $repository->findBy([],['date' => 'desc'], $limit);

        return $this->render('@OCPlatform/Advert/menu.html.twig', compact('listAdverts'));
    }

    public function viewAction($id)
    {
        // On affiche l'annonce correspondante à l'id
        $em = $this->getDoctrine()->getManager();
        $advert = $em->getRepository('OCPlatformBundle:Advert')->find($id);

        if($advert === null) {
            throw new NotFoundHttpException("L'annonce d'id".$id." n'existe pas.");  
        }

        // On récupère les candidatures de cette annonce
        $listApplications = $em->getRepository('OCPlatformBundle:Application')
                               ->findBy(compact('advert'));

        $listAdvertSkills = $em->getRepository('OCPlatformBundle:AdvertSkill')
                                ->findBy(compact('advert'));

        return $this->render('@OCPlatform/Advert/view.html.twig', compact('advert', 'listApplications', 'listAdvertSkills'));
    }

    public function addAction(Request $request)
    {
        $advert = new Advert();

        $form = $this->createForm(AdvertType::class, $advert);

        if($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($advert);
            $em->flush();

            $request->getSession()->getFlashBag()->add('notice', "Annonce bien enregistrée.");

            return $this->redirectToRoute('oc_platform_view', array('id' =>$advert->getId()));
        }

        return $this->render('@OCPlatform/Advert/add.html.twig', array('form' => $form->createView()));
    }

    public function editAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $advert = $em->getRepository('OCPlatformBundle:Advert')->find($id);

        if($advert === null) {
            throw new NotFoundHttpException("L'annonce d'id".$id." n'existe pas."); 
        }

        $form = $this->createForm(AdvertEditType::class, $advert);

        if($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {

            $em->flush();
            $request->getSession()->getFlashBag()->add('notice', 'Annonce bien modifiée.');

            return $this->redirectToRoute('oc_platform_view', compact('id'));
        }
        return $this->render('@OCPlatform/Advert/edit.html.twig', array('form' => $form->createView(), 'advert' => $advert));
    }

    public function deleteAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $advert = $em->getRepository('OCPlatformBundle:Advert')->find($id);

        if($advert === null) {
            throw new NotFoundHttpException("L'annonce d'id".$id." n'existe pas.");
        }

        $form = $this->get('form.factory')->create();

        if($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {

            $em->remove($advert);
            $em->flush();
            $request->getSession()->getFlashBag()->add('notice', "L'annonce a bien été supprimée.");

            return $this->redirectToRoute('oc_platform_home');
        }
        
        return $this->render('@OCPlatform/Advert/delete.html.twig', array(
            'advert' => $advert,
            'form'   => $form->createView()
        ));
    }

    public function purgeAction($days, Request $request)
    {
        // Initialise le service de purge
        $purgeAdvert = $this->get('oc_platform.purger.advert');

        $purgeAdvert->purge($days);

        $request->getSession()->getFlashBag()->add('info', 'Annonces purgées !');

        // Redirection
        return $this->redirectToRoute('oc_core_homepage');
    }
}
