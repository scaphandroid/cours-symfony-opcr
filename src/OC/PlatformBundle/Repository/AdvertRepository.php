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
class AdvertRepository extends \Doctrine\ORM\EntityRepository
{
	
	public function getAdverts($page, $nbPerPage){
		
		$queryBuilder = $this
			->createQueryBuilder('a')
			->leftJoin('a.image', 'img')
			->addSelect('img')
			->leftJoin('a.categories', 'cat')
			->addSelect('cat')
			->orderBy('a.date', 'DESC')
		;
		
		 $queryBuilder 
			->setFirstResult(($page-1) * $nbPerPage)
			->setMaxResults($nbPerPage)
		;

		return new Paginator($queryBuilder, true);
		
	}
	

	
}
