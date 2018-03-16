<?php

namespace OC\PlatformBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use OC\PlatformBundle\Entity\Advert;

class LoadAdvert implements FixtureInterface, OrderedFixtureInterface
{
  // Dans l'argument de la méthode load, l'objet $manager est l'EntityManager
  public function load(ObjectManager $manager)
  {
    // Liste des annonces à ajouter
    $adverts = [
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
                'image'      => $manager->getRepository('OCPlatformBundle:ImageEntity')->findOneByAlt('Job de rêve'),
                'categories' => [$manager->getRepository('OCPlatformBundle:Category')->findOneByName('Développement web')]
              ]
            ];

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
  }

  public function getOrder()
  {
    return  4;
  }
}