<?php
// src/LP/PartnerBundle/PhoneCall/MailService.php

namespace LP\UserBundle\MailService;

use Symfony\Component\DependencyInjection\ContainerInterface;

/* ------------------------------------------------------------------------------------------------------
 *
 *      Class MailService
 *
 * ---------------------------------------------------------------------------------------------------- */

class MailService
{

  private $email_sender;
  private $mailer;
  private $templating;

/* ------------------------------------------------------------------------------------------------------
 *      constructor
 * ---------------------------------------------------------------------------------------------------- */

  public function __construct($email_sender, $mailer, $templating)
  {
    $this->email_sender = $email_sender;
    $this->mailer       = $mailer;
    $this->templating   = $templating;
  }

/* ------------------------------------------------------------------------------------------------------
 *      function sendMail
 * ---------------------------------------------------------------------------------------------------- */

  public function sendMail($email, $subject, $text)
  {

          $message = \Swift_Message::newInstance()
                    ->setSubject($subject)
                    ->setFrom($this->email_sender)
                    ->setTo($email)
                    ->setBody($text);
                   // ->setBody($this->templating->render('LPUserBundle:Security:email.txt.twig', array('text' => $text)));

          $this->mailer->send($message);

    return 0;
  }

}