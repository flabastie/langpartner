<?php
// src/LP/PartnerBundle/InterestService/InterestService.php

namespace LP\PartnerBundle\InterestService;

use Doctrine\Common\Persistence\ObjectManager;
use LP\PartnerBundle\Entity\Interest;
use LP\PartnerBundle\Entity\Member;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManager;

/* ------------------------------------------------------------------------------------------------------
 *
 *      Class InterestService
 *
 * ---------------------------------------------------------------------------------------------------- */

class InterestService
{

  protected $em;

/* ------------------------------------------------------------------------------------------------------
 *      constructeur
 * ---------------------------------------------------------------------------------------------------- */

  public function __construct(EntityManager $em)
  {
    $this->em = $em;
  }

/* ------------------------------------------------------------------------------------------------------
 *      function getListInterestAction
 * ---------------------------------------------------------------------------------------------------- */

  public function getListInterestAction(EntityManager $em, $id)
  {
    $member             = $em->getRepository('LPPartnerBundle:Member')->find($id);
    $intMember          = $member->getInterests();
    $listInterests      = $em->getRepository('LPPartnerBundle:Interest')->findAll();
    $tabInterests       = array();
    $tabMemberInterests = array();

    $i = 0;
    if ($intMember) 
    {
      foreach ( $intMember as $int ) 
      {
        $tabMemberInterests[$i] = $int->getName();
        $i++;
      }
    }

    foreach ($listInterests as $int) 
    {
      if (in_array( $int->getName() , $tabMemberInterests )) 
      {
        $tabInterests[$int->getName()] = 1 ;
      }
      else 
      {
        $tabInterests[$int->getName()] = 0 ;
      }          
    }
    return $tabInterests;
  }

}