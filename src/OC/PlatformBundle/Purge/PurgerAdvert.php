<?php 

namespace OC\PlatformBundle\Purge;

use Doctrine\ORM\EntityManagerInterface;

class PurgerAdvert
{
	private $em;

	public function __construct(EntityManagerInterface $em)
	{
		$this->em = $em;
	}

	public function purge($days)
	{
		$advertRepo = $this->em->getRepository('OCPlatformBundle:Advert');
		$advertSkills = $this->em->getRepository('OCPlatformBundle:AdvertSkill');

		// Liste des annonces a purger
		$listAdverts = $advertRepo->getAdvertsToPurge(new \Datetime('-'.$days.' day'));

		foreach ($listAdverts as $advert) {
			// Liste des compétences a supprimer
		 	$listAdvertSkills = $advertSkills->findByAdvert($advert);

		 	foreach($listAdvertSkills as $advertSkill)
		 	{
		 		$this->em->remove($advertSkill);
		 	}

		 	$this->em->remove($advert);
		}

		$this->em->flush();
	}
}