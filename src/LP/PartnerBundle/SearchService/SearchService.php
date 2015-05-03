<?php
// src/LP/PartnerBundle/SearchService/SearchService.php

namespace LP\PartnerBundle\SearchService;

use Doctrine\Common\Persistence\ObjectManager;
use LP\PartnerBundle\Entity\Member;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManager;
use \DateTime;

/* ------------------------------------------------------------------------------------------------------
 *
 *      Class SearchService
 *
 * ---------------------------------------------------------------------------------------------------- */

class SearchService
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
 *      function searchByCategoryAction
 * ---------------------------------------------------------------------------------------------------- */

  public function searchByCategoryAction(EntityManager $em, $catMember)
  {
    
    $membersListCategory = array();

    // category = fr
    if ($catMember === "fr") 
    {
      $membersListCategory = $em  ->getRepository('LPPartnerBundle:Member')
                          ->findBy(
                              array('category' => 'en'),
                              $limit  = null,
                              $offset = null
                            );
    }
    // category = en
    else 
    {
      $membersListCategory = $em  ->getRepository('LPPartnerBundle:Member')
                          ->findBy(
                              array('category' => 'fr'),
                              $limit  = null,
                              $offset = null
                            );
    }

    return $membersListCategory;
  }

/* ------------------------------------------------------------------------------------------------------
 *      function searchByStatusAction
 * ---------------------------------------------------------------------------------------------------- */

  public function searchByStatusAction(EntityManager $em, $statusMember)
  {
    
    $membersListStatus = array();

    // status = available
    if ($statusMember === "available") 
    {
      $membersListStatus = $em  ->getRepository('LPPartnerBundle:Member')
                          ->findBy(
                              array('status' => 'available'),
                              $limit  = null,
                              $offset = null
                            );
    }
    
    // status = ended
    if ($statusMember === "ended") 
    {
      $membersListStatus = $em  ->getRepository('LPPartnerBundle:Member')
                          ->findBy(
                              array('status' => 'ended'),
                              $limit  = null,
                              $offset = null
                            );
    }

    // status = new
    if ($statusMember === "new") 
    {
      $membersListStatus = $em  ->getRepository('LPPartnerBundle:Member')
                          ->findBy(
                              array('status' => 'new'),
                              $limit  = null,
                              $offset = null
                            );
    }

    // status = not available
    if ($statusMember === "not available") 
    {
      $membersListStatus = $em  ->getRepository('LPPartnerBundle:Member')
                          ->findBy(
                              array('status' => 'not available'),
                              $limit  = null,
                              $offset = null
                            );
    }

    return $membersListStatus;
  }

/* ------------------------------------------------------------------------------------------------------
 *      function searchByRangeAction
 * ---------------------------------------------------------------------------------------------------- */

  public function searchByRangeAction(EntityManager $em, $dateBirth)
  {
    
    $membersListRange = array();

    $todayDate = new DateTime();
    $todayDate = $todayDate->format('d-m-Y');

    $month  = date("m", strtotime($todayDate));
    $day    = date("d", strtotime($todayDate));

    $dateBirth = $dateBirth->format('d-m-Y');

    $oDateNow       = new DateTime();
    $oDateBirth     = new DateTime($dateBirth);
    $oDateIntervall = $oDateNow->diff($oDateBirth);

    $age = $oDateIntervall->y;

    // $range = "-18";
    if ($age<18) 
    {  
      $start  = date('Y') -18 .'-' . $month . '-' . $day;
      $end    = date('Y')     .'-' . $month . '-' . $day;
    }

    // $range = "18-25";
    if ($age>18 && $age<=25) 
    {
      $start  = date('Y') -25 .'-' . $month . '-' . $day;
      $end    = date('Y') -18 .'-' . $month . '-' . $day;
    }

    // $range = "25-35";
    if ($age>25 && $age<=35) 
    {  
      $start  = date('Y') -35 .'-' . $month . '-' . $day;
      $end    = date('Y') -25 .'-' . $month . '-' . $day;
    }

    //$range = "35-45";
    if ($age>35 && $age<=45) 
    {  
      $start  = date('Y') -45 .'-' . $month . '-' . $day;
      $end    = date('Y') -35 .'-' . $month . '-' . $day;
    }

    // $range = "45+";
    if ($age>45) 
    {  
      $start  = date('Y') -100 .'-' . $month . '-' . $day;
      $end    = date('Y') -45 . '-' . $month . '-' . $day;
    }

    $membersListRange = $em   ->getRepository('LPPartnerBundle:Member')
                              ->myFindRange($start, $end);

    return $membersListRange;
  }

/* ------------------------------------------------------------------------------------------------------
 *      function searchByAvailabilityAction
 * ---------------------------------------------------------------------------------------------------- */

  public function searchByAvailabilityAction(EntityManager $em, $member)
  {
    $membersListAvailability = array();
    // recup dateStart et dateEnd
    $memberDateStart  = $member->getDateStart();
    $memberDateEnd    = $member->getDateEnd();
    $start            = $memberDateStart->format('Y-m-d');
    $end              = $memberDateEnd->format('Y-m-d');

    $membersListAvailability = $em->getRepository('LPPartnerBundle:Member')
                                  ->findAvailability($start, $end);

    return $membersListAvailability;
  }

/* ------------------------------------------------------------------------------------------------------
 *      function searchByInterestAction
 * ---------------------------------------------------------------------------------------------------- */

  public function searchByInterestAction(EntityManager $em, $member, $nbInterests, $allMembersList)
  {
    $tabMemberInterests   = $member->getInterests();
    $tabPartnersInterests = array();
    $tabIdPartnerInterests       = array();  // tab key=id value=total interets communs
/*
    echo "<pre>";
    print_r($tabMemberInterests);
    echo "</pre>";
 */   
    // Parcours des partenaires ==========================================================================

    foreach ($allMembersList as $partner) 
    {
      if ( $partner->getId() != $member->getId() ) 
      {
        foreach ($partner->getInterests() as $interestName) 
        {
          // if name interests in $tabMemberInterests
          if (in_array($interestName, $tabMemberInterests)) 
          {
            $tabPartnersInterests[$partner->getId()][] = $interestName;
          }
        }
      }
    }

    foreach ($tabPartnersInterests as $idPartner => $tabInterestsCommun) {
      $tabIdPartnerInterests[$idPartner] = count($tabInterestsCommun);
    }
/*
    echo "<pre>";
    print_r($tabPartnersInterests);
    echo "</pre>";
*/
    arsort($tabIdPartnerInterests); // tri tabNbInterests value desc

    // suppression partners with $value < $nbInterests ===================================================
    foreach ($tabIdPartnerInterests as $key => $value) 
    {
      if ($value < $nbInterests) 
      {
        unset($tabIdPartnerInterests[$key]);
      }
    }
/*
    echo "<pre>";
    print_r($tabIdPartnerInterests);
    echo "</pre>";
*/
    return $tabIdPartnerInterests;
  }

}