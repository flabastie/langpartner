<?php
// src/LP/PartnerBundle/StatusService/StatusService.php

namespace LP\PartnerBundle\StatService;

use \DateTime;

/* ------------------------------------------------------------------------------------------------------
 *
 *      Class StatusService
 *
 * ---------------------------------------------------------------------------------------------------- */

class StatService
{

/* ------------------------------------------------------------------------------------------------------
 *      fonction statStatus
 * ---------------------------------------------------------------------------------------------------- */

  public function statStatus($membersList)
  {
  	$tabStatus = array(
	  		"available" => 0,
	  		"ended" => 0,
	  		"new" => 0,
	  		"not available" => 0
  		);

    foreach ($membersList as $member) {

        $id = $member->getId();
        $status = $member->getStatus();

        switch ($status) {
        	case 'available':
        		// status available
        		$tabStatus["available"]++;
        		break;
        	case 'ended':
        		// status ended
        		$tabStatus["ended"]++;
        		break;
        	case 'new':
        		// status new
        		$tabStatus["new"]++;
        		break;
        	case 'not available':
        		// status not available
        		$tabStatus["not available"]++;
        		break;
        	default:
        		# code...
        		break;
        }   
    }
    
  	return $tabStatus;
  }


/* ------------------------------------------------------------------------------------------------------
 *      fonction statMembership
 * ---------------------------------------------------------------------------------------------------- */

  public function statMembership($membersList)
  {
  	$tabMembership = array(
	  		"no" => 0,
	  		"yes" => 0,
	  		"pending" => 0
  		);

    foreach ($membersList as $member) {

        $id = $member->getId();
        $membership = $member->getMembership();

        switch ($membership) {
        	case 'no':
        		// membership no
        		$tabMembership["no"]++;
        		break;
        	case 'yes':
        		// membership yes
        		$tabMembership["yes"]++;
        		break;
        	case 'pending':
        		// membership pending
        		$tabMembership["pending"]++;
        		break;
        	default:
        		# code...
        		break;
        }   
    }

  	return $tabMembership;
  }

/* ------------------------------------------------------------------------------------------------------
 *      fonction statCategory
 * ---------------------------------------------------------------------------------------------------- */

  public function statCategory($membersList)
  {
  	$tabStatCategory = array(
	  		"en" => 0,
	  		"fr" => 0
  		);

    foreach ($membersList as $member) {

        $id = $member->getId();
        $membership = $member->getCategory();

        switch ($membership) {
        	case 'en':
        		// category en
        		$tabStatCategory["en"]++;
        		break;
        	case 'fr':
        		// category en
        		$tabStatCategory["fr"]++;
        		break;
        	default:
        		# code...
        		break;
        }   
    }

  	return $tabStatCategory;
  }

/* ------------------------------------------------------------------------------------------------------
 *      fonction statEnglishLevel
 * ---------------------------------------------------------------------------------------------------- */

  public function statEnglishLevel($membersList)
  {

    $tabStatEnglishLevel = array(
        "Beginner"          => 0,
        "Pre intermediate"  => 0,
        "Intermediate"      => 0,
        "Advanced"          => 0,
        "Mother tongue"     => 0
        );

    foreach ($membersList as $member) {

        $englishLevel = $member->getEnglishLevel();

        switch ($englishLevel) {
          case 'Beginner':
            // englishLevel Beginner
            $tabStatEnglishLevel["Beginner"]++;
            break;
          case 'Pre intermediate':
            // englishLevel Pre intermediate
            $tabStatEnglishLevel["Pre intermediate"]++;
            break;
          case 'Intermediate':
            // englishLevel Intermediate
            $tabStatEnglishLevel["Intermediate"]++;
            break;
          case 'Advanced':
            // englishLevel Advanced
            $tabStatEnglishLevel["Advanced"]++;
            break;
          case 'Mother tongue':
            // englishLevel Mother tongue
            $tabStatEnglishLevel["Mother tongue"]++;
            break;
        }   
    }

    return $tabStatEnglishLevel;
  }

/* ------------------------------------------------------------------------------------------------------
 *      fonction statFrenchLevel
 * ---------------------------------------------------------------------------------------------------- */

  public function statFrenchLevel($membersList)
  {

    $tabStatFrenchLevel = array(
        "Beginner"          => 0,
        "Pre intermediate"  => 0,
        "Intermediate"      => 0,
        "Advanced"          => 0,
        "Mother tongue"     => 0
        );

    foreach ($membersList as $member) {

        $frenchLevel = $member->getFrenchLevel();

        switch ($frenchLevel) {
          case 'Beginner':
            // frenchLevel Beginner
            $tabStatFrenchLevel["Beginner"]++;
            break;
          case 'Pre intermediate':
            // frenchLevel Pre intermediate
            $tabStatFrenchLevel["Pre intermediate"]++;
            break;
          case 'Intermediate':
            // frenchLevel Intermediate
            $tabStatFrenchLevel["Intermediate"]++;
            break;
          case 'Advanced':
            // frenchLevel Advanced
            $tabStatFrenchLevel["Advanced"]++;
            break;
          case 'Mother tongue':
            // frenchLevel Mother tongue
            $tabStatFrenchLevel["Mother tongue"]++;
            break;
        }   
    }
    
    return $tabStatFrenchLevel;
  }


}