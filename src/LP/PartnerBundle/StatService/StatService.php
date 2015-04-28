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
 *      fonction statInterests
 * ---------------------------------------------------------------------------------------------------- */

  public function statInterests($membersList)
  {
  	$tabStatInterests = array(
	  		"Travel" 		=> 0,
	  		"Cooking" 		=> 0,
	  		"Cinema" 		=> 0,
	  		"Music" 		=> 0,
	  		"Sport" 		=> 0,
	  		"Reading" 		=> 0,
	  		"Literature" 	=> 0,
	  		"Animals" 		=> 0,
	  		"Art" 			=> 0,
	  		"Economics" 	=> 0,
	  		"Politics" 		=> 0,
	  		"Meeting" 		=> 0,
	  		"Outing" 		=> 0
  		);

    foreach ($membersList as $member) {

		foreach ($member->getInterests() as $interest) 
		{
	        switch ($interest->getName()) 
	        {
	        	case 'Travel':
	        		$tabStatInterests["Travel"]++; // interest Travel
	        		break;
	        	case 'Cooking': 
	        		$tabStatInterests["Cooking"]++; // category Cooking
	        		break;
	        	case 'Cinema':
	        		$tabStatInterests["Cinema"]++; // interest Cinema
	        		break;
	        	case 'Music':
	        		$tabStatInterests["Music"]++; // interest Music
	        		break;
	        	case 'Sport':
	        		$tabStatInterests["Sport"]++; // interest Sport
	        		break;
	        	case 'Reading':
	        		$tabStatInterests["Reading"]++; // interest Reading
	        		break;
	        	case 'Literature':
	        		$tabStatInterests["Literature"]++; // interest Literature
	        		break;
	        	case 'Animals':
	        		$tabStatInterests["Animals"]++; // interest Animals
	        		break;
	        	case 'Art':
	        		$tabStatInterests["Art"]++; // interest Art
	        		break;
	        	case 'Economics':
	        		$tabStatInterests["Economics"]++; // interest Economics
	        		break;
	        	case 'Politics':
	        		$tabStatInterests["Politics"]++; // category Politics
	        		break;
	        	case 'Meeting':
	        		$tabStatInterests["Meeting"]++; // interest Meeting
	        		break;
	        	case 'Outing':
	        		$tabStatInterests["Outing"]++; // interest Outing
	        		break;
	        	default:
	        		# code...
	        		break;
	        }  
		} 
    }

  	return $tabStatInterests;
  }

}