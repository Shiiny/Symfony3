<?php

namespace OC\PlatformBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use OC\PlatformBundle\Entity\Skill;

class LoadSkill implements FixtureInterface, OrderedFixtureInterface
{
  // Dans l'argument de la méthode load, l'objet $manager est l'EntityManager
  public function load(ObjectManager $manager)
  {
    // Liste des noms de compétence à ajouter
    $names = ['PHP', 'Symfony', 'C++', 'JavaScript', 'Photoshop', 'Blender', 'Bloc-note'];

    foreach ($names as $name) {
      // On crée la compétence
      $skill = new Skill();
      $skill->setName($name);

      // On la persiste
      $manager->persist($skill);
    }

    // On déclenche l'enregistrement de toutes les compétences
    $manager->flush();
  }

  public function getOrder()
  {
    return 3;
  }
}