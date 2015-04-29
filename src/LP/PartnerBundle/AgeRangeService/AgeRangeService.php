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



}