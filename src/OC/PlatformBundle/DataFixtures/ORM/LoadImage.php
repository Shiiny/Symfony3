<?php

namespace OC\PlatformBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use OC\PlatformBundle\Entity\ImageEntity;

class LoadImage implements FixtureInterface, OrderedFixtureInterface
{
  // Dans l'argument de la méthode load, l'objet $manager est l'EntityManager
  public function load(ObjectManager $manager)
  {
    // Liste des images à ajouter
    $imgs = array(
      ['http://sdz-upload.s3.amazonaws.com/prod/upload/job-de-reve.jpg', 'Job de rêve'],
      ['https://symfony.com/logos/symfony_black_02.png', 'Logo Symfony'],
      ['https://upload.wikimedia.org/wikipedia/commons/thumb/2/27/PHP-logo.svg/500px-PHP-logo.svg.png', 'Logo PHP']
    );

    foreach ($imgs as $img) {
      // On crée l'image'
      $image = new ImageEntity();
      $image->setUrl($img[0]);
      $image->setAlt($img[1]);

      // On la persiste
      $manager->persist($image);
    }

    // On déclenche l'enregistrement de toutes les images
    $manager->flush();
  }

  public function getOrder()
  {
    return 1;
  }
}