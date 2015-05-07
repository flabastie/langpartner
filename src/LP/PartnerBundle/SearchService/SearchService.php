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
    $tabCategory = array();

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

    foreach ($membersListCategory as $partner) 
    {
      $tabCategory[] = $partner->getId();
    }

    return $tabCategory;
  }

/* ------------------------------------------------------------------------------------------------------
 *      function searchMembersByCategory
 * ---------------------------------------------------------------------------------------------------- */

  public function searchMembersByCategory(EntityManager $em, $member)
  {
    
    $memberCategory = $member->getCategory();
    $membersListCategory = array();
    $tabCategory = array();

    // category = fr
    if ($memberCategory === "fr") 
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

    foreach ($membersListCategory as $partner) 
    {
      $tabCategory[] = $partner->getId();
    }

    return $membersListCategory;
  }

/* ------------------------------------------------------------------------------------------------------
 *      function searchByStatusAction
 * ---------------------------------------------------------------------------------------------------- */

  public function searchByStatusAction(EntityManager $em, $member, $statusMember)
  {
    
    $membersListStatus = array();
    $tabStatus = array(); 

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

    foreach ($membersListStatus as $partner) 
    {
      if ($member->getId() != $partner->getId()) 
      {
        $tabStatus[] = $partner->getId();
      }
    }

    return $tabStatus;
  }

/* ------------------------------------------------------------------------------------------------------
 *      function searchByRangeAction
 * ---------------------------------------------------------------------------------------------------- */

  public function searchByRangeAction(EntityManager $em, $member, $dateBirth)
  {
    
    $membersListRange = array();
    $tabAgerange = array();

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

    foreach ($membersListRange as $partner) 
    {
      if ($member->getId() != $partner->getId()) 
      {
          $tabAgerange[] = $partner->getId();
      }
    }

    return $tabAgerange;
  }

/* ------------------------------------------------------------------------------------------------------
 *      function searchByAvailabilityAction
 * ---------------------------------------------------------------------------------------------------- */

  public function searchByAvailabilityAction(EntityManager $em, $member)
  {
    $membersListAvailability = array();
    $tabAvailability = array();
    // recup dateStart et dateEnd
    $memberDateStart  = $member->getDateStart();
    $memberDateEnd    = $member->getDateEnd();
    $start            = $memberDateStart->format('Y-m-d');
    $end              = $memberDateEnd->format('Y-m-d');

    $membersListAvailability = $em->getRepository('LPPartnerBundle:Member')
                                  ->findAvailability($start, $end);

    foreach ($membersListAvailability as $partner) 
    {
      if ($member->getId() != $partner->getId()) 
      {
        $tabAvailability[] = $partner->getId();
      }
    }

    return $tabAvailability;
  }

/* ------------------------------------------------------------------------------------------------------
 *      function searchByInterest
 * ---------------------------------------------------------------------------------------------------- */

  public function searchByInterest(EntityManager $em, $member, $nbInterests, $allMembersList)
  {
    $tabMemberInterests     = $member->getInterests();
    $tabPartnersInterests   = array();
    $tabIdPartnerInterests  = array();  // tab key=id value=total interets communs
    $tabUserInterests       = array();
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

    foreach ($tabIdPartnerInterests as $partnerId => $nbInt) 
    {
      if ($member->getId() != $partnerId) 
      {
        $tabUserInterests[] = $partnerId;
      }
    } 

    return $tabUserInterests;
  }


/* ------------------------------------------------------------------------------------------------------
 *      function searchByEnglishLevel
 * ---------------------------------------------------------------------------------------------------- */

  public function searchByEnglishLevel(EntityManager $em, $member)
  {
    $englishLevel         = $member->getEnglishLevel();
    $membersEnglishLevel  = array();
    $tabEnglishLevel      = array();

    if ($englishLevel === "Beginner") 
    {
        $membersEnglishLevel = $em  ->getRepository('LPPartnerBundle:Member')
                                    ->findBy(
                                              array('englishLevel' => 'Beginner'),
                                              $limit  = null,
                                              $offset = null
                                            );
    }

    if ($englishLevel === "Pre intermediate") 
    {
        $membersEnglishLevel = $em  ->getRepository('LPPartnerBundle:Member')
                                    ->findBy(
                                              array('englishLevel' => 'Pre intermediate'),
                                              $limit  = null,
                                              $offset = null
                                            );
    }

    if ($englishLevel === "Intermediate") 
    {
        $membersEnglishLevel = $em  ->getRepository('LPPartnerBundle:Member')
                                    ->findBy(
                                              array('englishLevel' => 'Intermediate'),
                                              $limit  = null,
                                              $offset = null
                                            );
    }

    if ($englishLevel === "Advanced") 
    {
        $membersEnglishLevel = $em  ->getRepository('LPPartnerBundle:Member')
                                    ->findBy(
                                              array('englishLevel' => 'Advanced'),
                                              $limit  = null,
                                              $offset = null
                                            );
    }

    if ($englishLevel === "Mother tongue") 
    {
        $membersEnglishLevel = $em  ->getRepository('LPPartnerBundle:Member')
                                    ->findBy(
                                              array('englishLevel' => 'Mother tongue'),
                                              $limit  = null,
                                              $offset = null
                                            );
    }

    foreach ($membersEnglishLevel as $partner) 
    {
      if ($member->getId() != $partner->getId()) 
      {
        $tabEnglishLevel[] = $partner->getId();
      }
    }

    return $tabEnglishLevel;
  }

/* ------------------------------------------------------------------------------------------------------
 *      function searchByFrenchLevel
 * ---------------------------------------------------------------------------------------------------- */

  public function searchByFrenchLevel(EntityManager $em, $member)
  {
    $frenchLevel        = $member->getFrenchLevel();
    $membersFrenchLevel = array();
    $tabFrenchLevel     = array();

    if ($frenchLevel === "Beginner") 
    {
        $membersFrenchLevel = $em  ->getRepository('LPPartnerBundle:Member')
                                    ->findBy(
                                              array('frenchLevel' => 'Beginner'),
                                              $limit  = null,
                                              $offset = null
                                            );
    }

    if ($frenchLevel === "Pre intermediate") 
    {
        $membersFrenchLevel = $em  ->getRepository('LPPartnerBundle:Member')
                                    ->findBy(
                                              array('frenchLevel' => 'Pre intermediate'),
                                              $limit  = null,
                                              $offset = null
                                            );
    }

    if ($frenchLevel === "Intermediate") 
    {
        $membersFrenchLevel = $em  ->getRepository('LPPartnerBundle:Member')
                                    ->findBy(
                                              array('frenchLevel' => 'Intermediate'),
                                              $limit  = null,
                                              $offset = null
                                            );
    }

    if ($frenchLevel === "Advanced") 
    {
        $membersFrenchLevel = $em  ->getRepository('LPPartnerBundle:Member')
                                    ->findBy(
                                              array('frenchLevel' => 'Advanced'),
                                              $limit  = null,
                                              $offset = null
                                            );
    }

    if ($frenchLevel === "Mother tongue") 
    {
        $membersFrenchLevel = $em  ->getRepository('LPPartnerBundle:Member')
                                    ->findBy(
                                              array('frenchLevel' => 'Mother tongue'),
                                              $limit  = null,
                                              $offset = null
                                            );
    }

    foreach ($membersFrenchLevel as $partner) 
    {
      if ($member->getId() != $partner->getId()) 
      {
          $tabFrenchLevel[] = $partner->getId();
      }
    }

    return $tabFrenchLevel;
  }

}