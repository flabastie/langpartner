<?php
// src/LP/PartnerBundle/AgeRangeService/AgeRangeService.php

namespace LP\PartnerBundle\AgeRangeService;

use \DateTime;

/* ------------------------------------------------------------------------------------------------------
 *
 *      Class AgeRangeService
 *
 * ---------------------------------------------------------------------------------------------------- */

class AgeRangeService
{

/* ------------------------------------------------------------------------------------------------------
 *      function calculateRangeAction
 * ---------------------------------------------------------------------------------------------------- */

  public function calculateRangeAction($dateBirth)
  {

	$dateBirth = $dateBirth->format('d-m-Y');

  	$oDateNow = new DateTime();
  	$oDateBirth = new DateTime($dateBirth);
  	$oDateIntervall = $oDateNow->diff($oDateBirth);

  	$age = $oDateIntervall->y;
  	//echo $age;

	$range=NULL;

	if ($age<18) {
		// range -18
		$range = "-18";
	}

	if ($age>18 && $age<=25) {
		// range 18-25
		$range = "18-25";
	}

	if ($age>25 && $age<=35) {
		// range 25-35
		$range = "25-35";
	}

	if ($age>35 && $age<=45) {
		// range 35-45
		$range = "35-45";
	}

  if ($age>45 && $age<=55) {
    // range 45-55
    $range = "45-55";
  }

  if ($age>55 && $age<=60) {
    // range 55-60
    $range = "55-60";
  }

  if ($age>60 && $age<=65) {
    // range 60-65
    $range = "60-65";
  }

	if ($age>65) {
		// range 65+
		$range = "65+";
	}

    return $range;
  }

/* ------------------------------------------------------------------------------------------------------
 *      fonction statRange
 * ---------------------------------------------------------------------------------------------------- */

  public function statRange($membersList)
  {
  	$tabStatRange = array(
	  		"-18"    => 0,
	  		"18-25"  => 0,
	  		"25-35"  => 0,
	  		"35-45"  => 0,
	  		"45-55"  => 0,
        "55-60"  => 0,
        "60-65"  => 0,
        "65+"    => 0
  		);

    foreach ($membersList as $member) {

        $id = $member->getId();
        $dateBirth = $member->getDateBirth();
        $range = $this->calculateRangeAction($dateBirth);

        switch ($range) {
        	case '-18':
        		// range -18
        		$tabStatRange["-18"]++;
        		break;
        	case '18-25':
        		// range 18-25
        		$tabStatRange["18-25"]++;
        		break;
        	case '25-35':
        		// range 25-35
        		$tabStatRange["25-35"]++;
        		break;
        	case '35-45':
        		// range 35-45
        		$tabStatRange["35-45"]++;
        		break;
          case '45-55':
            // range 45-55
            $tabStatRange["45-55"]++;
            break;
          case '55-60':
            // range 55-60
            $tabStatRange["55-60"]++;
            break;
          case '60-65':
            // range 60-65
            $tabStatRange["60-65"]++;
            break;
        	case '65+':
        		// range 65+
        		$tabStatRange["65+"]++;
        		break;
        	default:
        		# code...
        		break;
        }   
    }

  	return $tabStatRange;
  }


/* ------------------------------------------------------------------------------------------------------
 *      function calculateAgeAction
 * ---------------------------------------------------------------------------------------------------- */

  public function calculateAgeAction($dateBirth)
  {
	$from = $dateBirth;
	$to   = new DateTime('today');
	$age = $from->diff($to)->y;

    return $age;
  }


/* ------------------------------------------------------------------------------------------------------
 *      function startEndRange
 * ---------------------------------------------------------------------------------------------------- */

  public function startEndRange($dateBirth)
  {

    $range          = array();
    $dateBirth      = $dateBirth->format('d-m-Y');
    $dateNow        = new DateTime();
    $DateBirth      = new DateTime($dateBirth);
    $dateIntervall  = $dateNow->diff($DateBirth);
    $age            = $dateIntervall->y;
    
  if ($age<18) {
    // range -18
    $dateStart  = date('Y-01-01', strtotime('-18 years'));
    $dateEnd    = date('Y-12-31', strtotime('-2 years'));
  }

  if ($age>18 && $age<=25) {
    // range 18-25
    $dateStart  = date('Y-01-01', strtotime('-25 years'));
    $dateEnd    = date('Y-12-31', strtotime('-18 years'));
  }

  if ($age>25 && $age<=35) {
    // range 25-35
    $dateStart  = date('Y-01-01', strtotime('-35 years'));
    $dateEnd    = date('Y-12-31', strtotime('-25 years'));
  }

  if ($age>35 && $age<=45) {
    // range 35-45
    $dateStart  = date('Y-01-01', strtotime('-45 years'));
    $dateEnd    = date('Y-12-31', strtotime('-35 years'));
  }

  if ($age>45 && $age<=55) {
    // range 45-55
    $dateStart  = date('Y-01-01', strtotime('-55 years'));
    $dateEnd    = date('Y-12-31', strtotime('-45 years'));
  }

  if ($age>55 && $age<=60) {
    // range 55-60
    $dateStart  = date('Y-01-01', strtotime('-60 years'));
    $dateEnd    = date('Y-12-31', strtotime('-55 years'));
  }

  if ($age>60 && $age<=65) {
    // range 60-65
    $dateStart  = date('Y-01-01', strtotime('-65 years'));
    $dateEnd    = date('Y-12-31', strtotime('-60 years'));
  }

  if ($age>65) {
    // range 65+
    $dateStart  = date('Y-01-01', strtotime('-100 years'));
    $dateEnd    = date('Y-12-31', strtotime('-65 years'));
  }

  $range['start'] = $dateStart;
  $range['end']   = $dateEnd;

    return $range;
  }


}