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

  public function getListInterest($tabMemberInterests)
  {

    // interests defined
    $tabInterests = array(  'travel', 
                            'cooking', 
                            'cinema', 
                            'music', 
                            'sport', 
                            'reading', 
                            'literature', 
                            'animals', 
                            'art', 
                            'economics', 
                            'politics', 
                            'meeting', 
                            'outing');

    // tab interest with yes or no
    $tabInterestsYesNo = array();

    foreach ($tabInterests as $value) {
      if (in_array($value , $tabMemberInterests)) 
      {
        $tabInterestsYesNo[$value] = 1;
      }
      else {
        $tabInterestsYesNo[$value] = 0;
      }
    }

    return $tabInterestsYesNo;
  }


/* ------------------------------------------------------------------------------------------------------
 *      fonction statInterests
 * ---------------------------------------------------------------------------------------------------- */

  public function statInterests($membersList)
  {
    $tabStatInterests = array(
        "travel"     => 0,
        "cooking"    => 0,
        "cinema"     => 0,
        "music"      => 0,
        "sport"      => 0,
        "reading"    => 0,
        "literature" => 0,
        "animals"    => 0,
        "art"        => 0,
        "economics"  => 0,
        "politics"   => 0,
        "meeting"    => 0,
        "outing"     => 0
      );

    foreach ($membersList as $member) {

      $tabInterestsMember = $member->getInterests();

    foreach ($member->getInterests() as $interest) 
    {
          switch ($interest) 
          {
            case 'travel':
              $tabStatInterests["travel"]++; // interest Travel
              break;
            case 'cooking': 
              $tabStatInterests["cooking"]++; // category Cooking
              break;
            case 'cinema':
              $tabStatInterests["cinema"]++; // interest Cinema
              break;
            case 'music':
              $tabStatInterests["music"]++; // interest Music
              break;
            case 'sport':
              $tabStatInterests["sport"]++; // interest Sport
              break;
            case 'reading':
              $tabStatInterests["reading"]++; // interest Reading
              break;
            case 'literature':
              $tabStatInterests["literature"]++; // interest Literature
              break;
            case 'animals':
              $tabStatInterests["animals"]++; // interest Animals
              break;
            case 'art':
              $tabStatInterests["art"]++; // interest Art
              break;
            case 'economics':
              $tabStatInterests["economics"]++; // interest Economics
              break;
            case 'politics':
              $tabStatInterests["politics"]++; // category Politics
              break;
            case 'meeting':
              $tabStatInterests["meeting"]++; // interest Meeting
              break;
            case 'outing':
              $tabStatInterests["outing"]++; // interest Outing
              break;
            default:
              # code...
              break;
          }  
          
    } 
    }

    return $tabStatInterests;
  }



}