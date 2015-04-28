<?php

// src/LP/PartnerBundle/Controller/StatController.php

namespace LP\PartnerBundle\Controller;

use LP\PartnerBundle\Entity\Member;
use LP\PartnerBundle\Entity\Interest;
use LP\PartnerBundle\Entity\PhoneCall;
use LP\PartnerBundle\Form\PhoneCallType;
use LP\PartnerBundle\AgeRangeService\AgeRangeService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

/* ------------------------------------------------------------------------------------------------------
 *
 *      Class StatController
 *
 * ---------------------------------------------------------------------------------------------------- */

class StatController extends Controller
{

/* ------------------------------------------------------------------------------------------------------
 *      fonction memberListAction
 * ---------------------------------------------------------------------------------------------------- */

    public function statRangeAction(Request $request)
    {
        $totalMembers = 0;
        $tabStatRange = array();
        $tabStatStatus = array();
        $tabStatMembership = array();
        $tabStatCategory = array();
        $tabStatInterests = array();

        $membersList = $this->getDoctrine()
          ->getManager()
          ->getRepository('LPPartnerBundle:Member')
          ->findAll();

        // totalMembers
        $totalMembers = count($membersList);

        // recup service agerange
        $agerange = $this->container->get('lp_partner.agerange');
        $tabStatRange = $agerange->statRange($membersList);

        // recup service stat
        $stat = $this->container->get('lp_partner.stat');
        $tabStatMembership  = $stat->statMembership($membersList);
        $tabStatStatus      = $stat->statStatus($membersList);
        $tabStatCategory    = $stat->statCategory($membersList);
        $tabStatInterests   = $stat->statInterests($membersList); 

/*
        echo "<pre>";
        print_r($tabStatInterests);
        echo "</pre>";
*/
        return $this->render('LPPartnerBundle:Partner:index.html.twig', array(
          'tabStatRange' => $tabStatRange,
          'totalMembers' => $totalMembers,
          'tabStatStatus' => $tabStatStatus,
          'tabStatMembership' => $tabStatMembership,
          'tabStatCategory' => $tabStatCategory,
          'tabStatInterests' => $tabStatInterests
        ));
    }


}