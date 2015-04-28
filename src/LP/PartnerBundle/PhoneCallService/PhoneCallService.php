<?php
// src/LP/PartnerBundle/PhoneCall/PhoneCallService.php

namespace LP\PartnerBundle\PhoneCallService;

use Doctrine\Common\Persistence\ObjectManager;
use LP\PartnerBundle\Entity\PhoneCall;
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
/*
          // recup dateCall
          $lastdatecall = $phonecall->getDateCall();

          // noteCall
          echo $phonecall->getNoteCall() . "<br>";

          // recup member
          $user = $phonecall->getMember();

          // recup user
          $user = $phonecall->getUser();
*/
          // comptage de calls
          $totalPhoneCalls++;
        }
        // transfo date to string
        $lastdatecall = $lastdatecall->format('d-m-Y');
    }

    return $totalPhoneCalls;
  }

/* ------------------------------------------------------------------------------------------------------
 *      function calculateAgeAction
 * ---------------------------------------------------------------------------------------------------- */

  public function calculateAgeAction()
  {


    return 0;
  }

}