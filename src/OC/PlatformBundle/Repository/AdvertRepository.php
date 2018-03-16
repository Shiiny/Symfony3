<?php

namespace OC\PlatformBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * AdvertRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class AdvertRepository extends EntityRepository
{
	/**
	 * Récupère toutes les annonces triées par date en liant l'image des annonces et leurs catégories
	 */
	public function getAdverts($page, $nbPerPage)
	{
		$query = $this->createQueryBuilder('a')
			// Jointure sur l'attribut image
			->leftJoin('a.image', 'img')
			->addSelect('img')
			// Jointure sur l'attribut catégories
			->leftJoin('a.categories', 'c')
			->addSelect('c')
			// Jointure sur l'attribut compétence
			->leftJoin('a.advertSkill', 's')
			->addSelect('s')
			->orderBy('a.date', 'DESC')
			->getQuery();

		$query->setFirstResult(($page-1) * $nbPerPage)
			->setMaxResults($nbPerPage);

		return new Paginator($query, true);
	}

	public function getAdvertsToPurge(\Datetime $date)
	{
		$query = $this->createQueryBuilder('a')
			->where('a.updatedAt <= :date')
			->orWhere('a.updatedAt IS NULL AND a.date <= :date')
			->andWhere('a.applications IS EMPTY')
			->setParameter('date', $date)
			->getQuery();

		return $query->getResult();
	}
}