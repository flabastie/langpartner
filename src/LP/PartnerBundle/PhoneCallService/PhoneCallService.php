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

}