<?php

// src/LP/PartnerBundle/Controller/StatController.php

namespace LP\PartnerBundle\Controller;

use LP\PartnerBundle\Entity\Member;
use LP\PartnerBundle\Entity\PhoneCall;
use LP\PartnerBundle\Form\PhoneCallType;
use LP\PartnerBundle\AgeRangeService\AgeRangeService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/* ------------------------------------------------------------------------------------------------------
 *
 *      Class StatController
 *
 * ---------------------------------------------------------------------------------------------------- */

class StatController extends Controller
{

/* ------------------------------------------------------------------------------------------------------
 *      fonction statRangeAction
 * ---------------------------------------------------------------------------------------------------- */

    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function statRangeAction(Request $request)
    {
        $totalMembers         = 0;
        $tabStatRange         = array();
        $tabStatStatus        = array();
        $tabStatMembership    = array();
        $tabStatCategory      = array();
        $tabStatInterests     = array();
        $tabStatEnglishLevel  = array();
        $tabStatFrenchLevel   = array();

        // recup all members
        $membersList          = $this ->getDoctrine()
                                      ->getManager()
                                      ->getRepository('LPPartnerBundle:Member')
                                      ->findAll();
        // totalMembers
        $totalMembers         = count($membersList);

        // recup service agerange
        $agerange             = $this->container->get('lp_partner.agerange');
        $tabStatRange         = $agerange->statRange($membersList);

        // recup service stat
        $stat                 = $this->container->get('lp_partner.stat');
        $tabStatMembership    = $stat->statMembership($membersList);
        $tabStatStatus        = $stat->statStatus($membersList);
        $tabStatCategory      = $stat->statCategory($membersList);
        $tabStatEnglishLevel  = $stat->statEnglishLevel($membersList);
        $tabStatFrenchLevel   = $stat->statFrenchLevel($membersList);
        
        // recup service interest
        $interestService      = $this->container->get('lp_partner.interest');
        $tabStatInterests     = $interestService->statInterests($membersList); 

/*
        echo "<pre>";
        print_r($tabStatEnglishLevel);
        echo "</pre>";
*/
        return $this->render('LPPartnerBundle:Partner:index.html.twig', array(
          'tabStatRange'      => $tabStatRange,
          'totalMembers'      => $totalMembers,
          'tabStatStatus'     => $tabStatStatus,
          'tabStatMembership' => $tabStatMembership,
          'tabStatCategory'   => $tabStatCategory,
          'tabStatInterests'  => $tabStatInterests,
          'tabStatEnglishLevel' => $tabStatEnglishLevel,
          'tabStatFrenchLevel' => $tabStatFrenchLevel
        ));
    }


}