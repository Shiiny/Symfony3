<?php

namespace OC\PlatformBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use OC\PlatformBundle\Entity\Advert;
use OC\PlatformBundle\Entity\Application;

class LoadApplication implements FixtureInterface, OrderedFixtureInterface
{
  // Dans l'argument de la méthode load, l'objet $manager est l'EntityManager
  public function load(ObjectManager $manager)
  {
    // Liste des candidatures à ajouter
    $apps = array(
      ['Marine', "J'ai toutes les qualités requises.", $manager->getRepository('OCPlatformBundle:Advert')->findOneByTitle('Recherche développeur Symfony 4')],
      ['Philippe', 'Je suis très motivé.', $manager->getRepository('OCPlatformBundle:Advert')->findOneByTitle('Recherche développeur Symfony 4')],
      ['Jean-Pierre', "J'aimerais bien suivre ce stage", $manager->getRepository('OCPlatformBundle:Advert')->findOneByTitle('Offre de stage webdesigner')],
      ['Carine', "J'ai une grande facilité d'apprentissage", $manager->getRepository('OCPlatformBundle:Advert')->findOneByTitle('Mission de webmaster H/F') ]
    );

    foreach ($apps as $app) {
      // On crée la candidature
      $application = new Application();
      $application
          ->setAuthor($app[0])
          ->setContent($app[1])
          ->setAdvert($app[2]);

      // On la persiste
      $manager->persist($application);
    }

    // On déclenche l'enregistrement de toutes les candidatures
    $manager->flush();
  }

  public function getOrder()
  {
    return  5;
  }
}