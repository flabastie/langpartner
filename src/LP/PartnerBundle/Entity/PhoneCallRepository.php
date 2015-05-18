<?php

namespace LP\PartnerBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

/**
 * PhoneCallRepository
 *
 */

class PhoneCallRepository extends EntityRepository
{

/* ------------------------------------------------------------------------------------------------------
 *      fonction getLastPhoneCall
 * ---------------------------------------------------------------------------------------------------- */

  public function getLastPhoneCall(Member $member)
  {                          

    $qb = $this ->createQueryBuilder('a')
                ->select('a.dateCall AS last_datecall')
                ->where('a.member = :member')
                ->setParameter('member', $member)
                ->orderBy('a.id', 'DESC')
    ;

    return $qb 	->getQuery()
				        ->getResult()
    ;

  }

}
