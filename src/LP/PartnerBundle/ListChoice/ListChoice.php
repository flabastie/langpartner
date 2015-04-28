<?php
// src/LP/PartnerBundle/ListChoice/ListChoice.php

namespace LP\PartnerBundle\ListChoice;



/* ------------------------------------------------------------------------------------------------------
 *
 *      Class ListChoice
 *
 * ---------------------------------------------------------------------------------------------------- */

class ListChoice
{

/* ------------------------------------------------------------------------------------------------------
 *      function categoryChoiceAction
 * ---------------------------------------------------------------------------------------------------- */

  public function categoryChoiceAction($key)
  {
  	$tabCategory = array( 'en' => 'English', 'fr' => 'French');

    return $tabCategory[$key];
  }

/* ------------------------------------------------------------------------------------------------------
 *      function membershipChoiceAction
 * ---------------------------------------------------------------------------------------------------- */

  public function membershipChoiceAction($key)
  {
  	$tabMembership = array('0' => 'no', '1' => 'yes', '2' => 'pending');

    return $tabMembership[$key];
  }

 /* ------------------------------------------------------------------------------------------------------
 *      function statusChoiceAction
 * ---------------------------------------------------------------------------------------------------- */

  public function statusChoiceAction($key)
  {
  	$tabStatus = array('0' => 'available', '1' => 'ended', '2' => 'new', '3' => 'not available');

    return $tabStatus[$key];
  }

}