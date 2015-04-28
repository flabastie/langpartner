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

	// $age under 18
	if ($age<18) {
		// range -18
		$range = "-18";
	}

	// $age between 18-25
	if ($age>18 && $age<=25) {
		// range 18-25
		$range = "18-25";
	}

	// $age between 25-35
	if ($age>25 && $age<=35) {
		// range 25-35
		$range = "25-35";
	}

	// $age between 35-45
	if ($age>35 && $age<=45) {
		// range 35-45
		$range = "35-45";
	}

	// $age more than 45
	if ($age>45) {
		// range 45+
		$range = "45+";
	}

    return $range;
  }

/* ------------------------------------------------------------------------------------------------------
 *      fonction statRange
 * ---------------------------------------------------------------------------------------------------- */

  public function statRange($membersList)
  {
  	$tabStatRange = array(
	  		"-18" => 0,
	  		"18-25" => 0,
	  		"25-35" => 0,
	  		"35-45" => 0,
	  		"45+" => 0
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
        	case '45+':
        		// range 45+
        		$tabStatRange["45+"]++;
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