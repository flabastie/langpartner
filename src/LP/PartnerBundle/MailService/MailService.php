<?php
// src/LP/PartnerBundle/PhoneCall/MailService.php

namespace LP\PartnerBundle\PhoneCallService;

use Doctrine\Common\Persistence\ObjectManager;
use LP\PartnerBundle\Entity\PhoneCall;
use LP\PartnerBundle\Entity\Member;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManager;

/* ------------------------------------------------------------------------------------------------------
 *
 *      Class MailService
 *
 * ---------------------------------------------------------------------------------------------------- */

class MailService
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

  public function sendMail(EntityManager $em)
  {

          // Send the email --------------------------------------------------------------------------------
/*
          $email = $_POST['form']['useremail'];
          $text = " To activate your account, please click on this link :\n\n";
          $text .= 'http://flab-image.com/activate?email=' . urlencode($email) . "&key=$activation";

          $message = \Swift_Message::newInstance()
              ->setSubject('Registration Confirmation')
              ->setFrom('contact@flab-image.com')
              ->setTo($email)
              ->setBody($this->renderView('LPUserBundle:Security:email.txt.twig', array('text' => $text)));
          $this->get('mailer')->send($message);

          $request->getSession()->getFlashBag()->add('info', 'Thank you for registering ! A confirmation email has been sent to ' . $email);
*/

    return 0;
  }



}