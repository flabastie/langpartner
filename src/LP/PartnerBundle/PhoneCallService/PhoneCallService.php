<?php
// src/LP/PartnerBundle/PhoneCall/PhoneCallService.php

namespace LP\PartnerBundle\PhoneCallService;

use Doctrine\Common\Persistence\ObjectManager;
use LP\PartnerBundle\Entity\PhoneCall;
use LP\PartnerBundle\Entity\Member;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManager;

/* ------------------------------------------------------------------------------------------------------
 *
 *      Class PhoneCall
 *
 * ---------------------------------------------------------------------------------------------------- */

class PhoneCallService
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
 *      function getTotalCallAction
 * ---------------------------------------------------------------------------------------------------- */

  public function getTotalCallAction(EntityManager $em)
  {

    // recup phone-call
    $listPhonecall  = $em ->getRepository('LPPartnerBundle:PhoneCall')->findAll();

    $totalPhoneCalls = 0;
    $lastdatecall = NULL;
    $tabPhoneCalls = array();

    if ($listPhonecall) {
        foreach ( $listPhonecall as $phonecall ) {

          $tabPhoneCalls = array( $phonecall->getMember(), $phonecall->getUser(), $phonecall->getDateCall(), getNoteCall() );

          echo $phonecall->getId() . " ";

          // comptage de calls
          $totalPhoneCalls++;
        }
        // transfo date to string
        $lastdatecall = $lastdatecall->format('d-m-Y');
    }

    return $totalPhoneCalls;
  }

/* ------------------------------------------------------------------------------------------------------
 *      function deletePhoneCallsAction
 * ---------------------------------------------------------------------------------------------------- */

    public function deletePhoneCallsAction(EntityManager $em, $id)
    {

      $validation     = false;
      $tabPhonecalls  = array();
      $totalCalls     = 0;

      // Vérification
      if ($id === null) {
      throw $this->createNotFoundException("Member with id ".$id." no exists.");
      }

      // phonecall ---------------------------------------------------------------------------
      $tabPhonecalls  = $em ->getRepository('LPPartnerBundle:PhoneCall')
                            ->findBy(array('member' => $id));

      $totalCalls = count($tabPhonecalls);

      // Vérification
      if ($totalCalls > 0) 
      {
        foreach ($tabPhonecalls as $phonecall) 
        {
          $em->remove($phonecall);
        }
        $em->flush();
        $validation = true;
      }
      elseif ($totalCalls == 0) 
      {
        $validation = true;
      }

        return $validation;
    }

/* ------------------------------------------------------------------------------------------------------
 *      function evaluateDateCall
 * ---------------------------------------------------------------------------------------------------- */

  public function evaluateDateCall(EntityManager $em, Member $member)
  {

    $dateEval = 0;

    // recup phone-call --------------------------------------------------------------------------------

    $lastPhonecall  = $em ->getRepository('LPPartnerBundle:PhoneCall')->getLastPhoneCall($member);

    if($lastPhonecall === null || empty($lastPhonecall[0][last_datecall])) 
    {
      $dateEval = 3;
      return $dateEval;
    } 

    // date last phonecall
    $lastPhoneCallDate = $lastPhonecall[0][last_datecall];
    $lastPhoneCallDate = $lastPhoneCallDate->format('Y-m-d');

    // dates creation  ---------------------------------------------------------------------------------

    // todayDate
    $todayDate = new \Datetime();
    //echo "todayDate = " . $todayDate->format('Y-m-d') . "<br>";

    // last15daysDate
    $last15daysDate = date('Y-m-d', strtotime('-15 days'));
    //echo "last15daysDate = " . $last15daysDate . "<br>";

    // lastMonthDate
    $lastMonthDate = date('Y-m-d', strtotime("last month"));
    //echo "lastMonthDate = " . $lastMonthDate . "<br>";

    // dates comparaison  ------------------------------------------------------------------------------

    // if older than 15 days
    if ($lastPhoneCallDate < $last15daysDate) 
    { 
      $dateEval = 1;
    }

    // if older than 1 month
    if ($lastPhoneCallDate < $lastMonthDate) 
    { 
      $dateEval = 2;
    }

    //echo $dateEval;
    return $dateEval;
  }


}