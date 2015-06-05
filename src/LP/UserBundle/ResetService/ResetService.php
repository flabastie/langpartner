<?php
// src/LP/UserBundle/ResetService/ResetService.php

namespace LP\UserBundle\ResetService;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

/* ------------------------------------------------------------------------------------------------------
 *
 *      Class ResetService
 *
 * ---------------------------------------------------------------------------------------------------- */

class ResetService
{

/* ------------------------------------------------------------------------------------------------------
 *      constructor
 * ---------------------------------------------------------------------------------------------------- */

  public function __construct()
  {
   /* $this->email_sender = $email_sender;
    $this->mailer       = $mailer;
    $this->templating   = $templating;
    */
  }

/* ------------------------------------------------------------------------------------------------------
 *      function validation
 * ---------------------------------------------------------------------------------------------------- */

  public function validation($userName, $userEmail)
  {


    // userName==true && userEmail==null
    if ($userName==true && $userEmail==null) 
    {
      // search user from name

      echo "Name true & email null<br>";
      echo "Name = " . $userName . "<br>";
    }

    // userName==null && userEmail==true
    if ($userName==null && $userEmail==true) 
    {
      // search user from email

      echo "Name null & email true<br>";
      echo "Email = " . $userEmail . "<br>";
    }


    // userName==true && userEmail==true
    if ($userName==true && $userEmail==true) 
    {
      // search user from name

      // search user from email

      // compare users

      echo "Name true & email true<br>";
      echo "Name = " . $userName . "<br>";
      echo "Email = " . $userEmail . "<br>";
    }

    // userName==null && userEmail==null
    if ($userName==null && $userEmail==null) 
    {
      // error message
    }

    return 0;
  }

}