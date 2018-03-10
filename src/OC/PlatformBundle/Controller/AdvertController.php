<?php

namespace OC\PlatformBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use OC\PlatformBundle\Entity\Advert;
use OC\PlatformBundle\Entity\ImageEntity;
use OC\PlatformBundle\Entity\Application;


class AdvertController extends Controller
{
    public function indexAction($page)
    {
    	if($page < 1) {
            throw new NotFoundHttpException('Page "'.$page.'" inexistante.');            
        }
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('OCPlatformBundle:Advert');
        $listAdverts = $repository->findAll();


        return $this->render('@OCPlatform/Advert/index.html.twig', compact('listAdverts'));
    }

    public function menuAction($limit)
    {
        $listAdverts = array(
            ['id' => 12, 'title' => "Recherche développeur Symfony"],
            ['id' => 13, 'title' => "Mission de webmaster H/F"],
            ['id' => 14, 'title' => "Offre de stage webdesigner"],
        );


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

        // dump($advert);
        // On récupère les candidatures de cette annonce
        $listApplications = $em->getRepository('OCPlatformBundle:Application')
                               ->findBy(compact('advert'));

        return $this->render('@OCPlatform/Advert/view.html.twig', compact('advert', 'listApplications'));
    }

    public function addAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        // Création de l'entité Advert

        $advert = new Advert();

        $advert->setTitle('Recherche développeur Symfony')
        ->setAuthor('Alexandre')
        ->setContent("Nous un développeur Symfony débutant pour un poste basé sur Lyon. Blabla…");
        
        // création de l'entité ImageEntity
        
        $img = new ImageEntity();
        $img->setUrl('http://sdz-upload.s3.amazonaws.com/prod/upload/job-de-reve.jpg')
        ->setAlt('Job de rêve');

        // Jointure
        $advert->setImage($img); 

        // Création d'une candid
        
        $app = new Application();
        $app->setAuthor('Marine')
        ->setContent("J'ai toutes les qualités requises.");

        $app->setAdvert($advert);

        $em->persist($advert);
        $em->persist($app);
        $em->flush();   


        $advert1 = new Advert();

        $advert1->setTitle('Mission de webmaster H/F')
        ->setAuthor('Fred')
        ->setContent("Pour une mission de 6 mois, nous recherchons un webmaster pour la maintenabilité de site. Blabla…");

        $em->persist($advert1);
        $em->flush();

        $advert2 = new Advert();

        $advert2->setTitle('Offre de stage webdesigner')
        ->setAuthor('Hugo')
        ->setContent("Nous proposons un poste pour webdesigner. Blabla…");

        // Création d'une candid
        
        $app1 = new Application();
        $app1->setAuthor('Julie')
        ->setContent("Je suis hyper motivée.");

        // Création d'une candid2 pour exemple
        $app2 = new Application();
        $app2->setAuthor('Jean-Pierre')
        ->setContent("J'aimerais bien suivre ce stage.");

        // Jointure des candids à l'annonce
        $app1->setAdvert($advert2);
        $app2->setAdvert($advert2);

        $em->persist($advert2);
        $em->persist($app1);
        $em->persist($app2);

        $em->flush();      

        /*if($request->isMethod('POST')) {
            $request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');

            return $this->redirectToRoute('oc_platform_view', array('id' => $advert->getId()));
        }*/
        $antispam = $this->container->get('oc_platform.antispam');

        $text = '...';
        if($antispam->isSpam($text)) {
            throw new \Exception("Votre message a été détecté comm spam !");
        }

        return $this->render('@OCPlatform/Advert/add.html.twig');
    }

    public function editAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $advert = $em->getRepository('OCPlatformBundle:Advert')->find($id);

        if($advert === null) {
            throw new NotFoundHttpException("L'annonce d'id".$id." n'existe pas."); 
        }

        // Récupère toutes les catégories de la DB
        $listCategories = $em->getRepository('OCPlatformBundle:Category')->findAll();

        foreach ($listCategories as $category) {
            $advert->addCategory($category);
        }

        $em->flush();

        /*if($request->isMethod('POST')) {
            $request->getSession()->getFlashBag()->add('notice', 'Annonce bien modifiée.');

            return $this->redirectToRoute('oc_platform_view', compact('id'));
        }*/
        return $this->render('@OCPlatform/Advert/edit.html.twig', compact('id'));
    }

    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $advert = $em->getRepository('OCPlatformBundle:Advert')->find($id);

        if($advert === null) {
            throw new NotFoundHttpException("L'annonce d'id".$id." n'existe pas.");
        }

        foreach ($advert->getCategories() as $category) {
            $advert->removeCategory($category);
        }

        $em->flush();

        return $this->redirectToRoute('oc_platform_home');
    }

    public function viewSlugAction($slug, $year, $_format)
    {
    	return new Response("On pourrait afficher l'annonce correspondante au slug'".$slug."', créée en ".$year." et au format ".$_format.".");
    }

    public function editImageAction($advertId)
    {
        $em = $this->getDoctrine()->getManager();
        $advert = $em->getRepository('OCPlatformBundle:Advert')->find($advertId);
        $img1 = 'http://sdz-upload.s3.amazonaws.com/prod/upload/job-de-reve.jpg';
        $img2 = 'https://img.finalfantasyxiv.com/t/9cf66392db9c07a138007aa588f1fce1db01713d.png?1519891202';

        if($advert->getImage()->getUrl() === $img1) {
            $advert->getImage()->setUrl($img2);
        }
        else {
            $advert->getImage()->setUrl($img1);
        }

        $em->flush();

        return new Response('OK');

    }
}
