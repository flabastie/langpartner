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


}