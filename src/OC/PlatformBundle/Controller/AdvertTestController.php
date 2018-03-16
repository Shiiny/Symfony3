<?php

namespace OC\PlatformBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use OC\PlatformBundle\Entity\Advert;
use OC\PlatformBundle\Entity\ImageEntity;
use OC\PlatformBundle\Entity\Application;
use OC\PlatformBundle\Entity\Skill;
use OC\PlatformBundle\Entity\AdvertSkill;


class AdvertTestController extends Controller
{
    public function viewSlugAction($slug, $year, $_format)
    {
    	return new Response("On pourrait afficher l'annonce correspondante au slug'".$slug."', créée en ".$year." et au format ".$_format.".");
    }

    public function testAction()
    {
        $manager = $this->getDoctrine()->getManager();
        /*$advert = new Advert();
        $advert->setTitle("Recherche développeur !")
        ->setAuthor('Fref')
        ->setContent("On s'en tape du contenu du message !!!");

        $em = $this->getDoctrine()->getManager();
        $em->persist($advert);
        $em->flush();

        return new Response('Slug généré : '.$advert->getSlug());*/
            /*$adverts = [
              [
                'title' =>'Recherche développeur Symfony 4',
                'author' => 'Anthony',
                'content' => "Nous recherchons un développeur Symfony 4 expérimenté sur Paris. Blabla…",
                'image' => $manager->getRepository('OCPlatformBundle:ImageEntity')->findOneByAlt('Logo Symfony'),
                'categories' => [
                                  $manager->getRepository('OCPlatformBundle:Category')->findOneByName('Développement web'),
                                  $manager->getRepository('OCPlatformBundle:Category')->findOneByName('Intégration')
                                ]
              ],
              [
                'title'      =>'Recherche développeur PHP',
                'author'     => 'Alexandre',
                'content'    => 'Nous recherchons un développeur PHP expérimenté sur Toulouse.',
                'image'      => $manager->getRepository('OCPlatformBundle:ImageEntity')->findOneByAlt('Logo PHP'),
                'categories' => [$manager->getRepository('OCPlatformBundle:Category')->findOneByName('Développement web')]
              ],
              [
                'title'      => 'Offre de stage webdesigner',
                'author'     => 'Hugo',
                'content'    => "Nous proposons un poste pour webdesigner. Blabla…",
                'image'      => NULL,
                'categories' => []
              ],
              [
                'title'      => 'Mission de webmaster H/F',
                'author'     => 'Mathieu',
                'content'    => "Nous recherchons un webmaster capable de maintenir notre site internet. Blabla…",
                'image'      => NULL,
                'categories' => []
              ],
              [
                'title'      => 'Recherche développeur Laravel',
                'author'     => 'David',
                'content'    => 'Nous recherchons un développeur Laravel sur Paris',
                'image'      => $manager->getRepository('OCPlatformBundle:ImageEntity')->findOneByAlt('Logo Laravel'),
                'categories' => [$manager->getRepository('OCPlatformBundle:Category')->findOneByName('Développement web')]
              ]
            ];*/

        foreach ($adverts as $row) {
          // On crée l'annonce
          $advert = new Advert();
          $advert
              ->setTitle($row['title'])
              ->setAuthor($row['author'])
              ->setContent($row['content'])
              ->setImage($row['image']);
          foreach ($row['categories'] as $category) {
            $advert->addCategory($category);
          }

          // On la persiste
          $manager->persist($advert);
        }

        // On déclenche l'enregistrement de toutes les annonces
        $manager->flush();

        return new Response('Ok');
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
