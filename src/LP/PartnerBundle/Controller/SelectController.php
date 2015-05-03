<?php

// src/LP/PartnerBundle/Controller/MatchController.php

namespace LP\PartnerBundle\Controller;

use LP\PartnerBundle\Entity\Member;
use LP\PartnerBundle\Entity\PhoneCall;
use LP\PartnerBundle\Form\PhoneCallType;
use LP\PartnerBundle\AgeRange\AgeRange;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\Session\Session;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class SelectController extends Controller
{


/* ------------------------------------------------------------------------------------------------------
 *      fonction selectPartnerAction
 * ---------------------------------------------------------------------------------------------------- */

    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function selectPartnerAction($idMember, $idPartner)
    {

        // Récupèration EntityManager
        $em = $this->getDoctrine()->getManager();

        // recup member
        $member = $em->getRepository('LPPartnerBundle:Member')->find($idMember);
        // recup partner
        $partner = $em->getRepository('LPPartnerBundle:Member')->find($idPartner);


        echo "Member : " . $member->getName() . "<br>";
        echo "Partner : " . $partner->getName() . "<br>";
         
           

/*
            echo "<pre>";
            print_r($tabPartnersFound);
            echo "</pre>";
*/


        // rendering
        return $this->render('LPPartnerBundle:Partner:select-partner.html.twig', array(
          'member' => $member
        ));

    }

}