<?php

// src/LP/PartnerBundle/Controller/MatchController.php

namespace LP\PartnerBundle\Controller;

use LP\PartnerBundle\Entity\Member;
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
    public function selectPartnerAction($idM, $idP)
    {
        $tabCategory                = array();
        $tabAgerange                = array();
        $tabStatus                  = array();
        $tabAvailability            = array();
        $tabUserInterests           = array();
        $tabPartnersMerge           = array();
        $tabPartnersFound           = array();
        $tabRangePartners           = array();
        $tabPartnerInterests        = array();
        $tabTotalPartnerInterests   = array();
        $tabInterestsMember         = array();
        $member = null;
        $partner = null;
        $idMember = $idM;
        $idPartner = $idP;

        // Récupèration EntityManager
        $em = $this->getDoctrine()->getManager();

        // recup member
        $member = $em->getRepository('LPPartnerBundle:Member')->find($idMember);
        // recup partner
        $partner = $em->getRepository('LPPartnerBundle:Member')->find($idPartner);

        // age range ========================================================================
        $agerange       = $this->container->get('lp_partner.agerange'); // service agerange
        $dateBirth      = $member->getDateBirth(); // recup dateBirth
        $rangeMember    = $agerange->calculateRangeAction($dateBirth); // calcul age range

        // interests ========================================================================
        $interestService    = $this->container->get('lp_partner.interest'); // service interest
        $tabMemberInterests = $member->getInterests();
        $tabInterests       = $interestService->getListInterest($tabMemberInterests);
        $totalInterests     = count($member->getInterests()); // total interests member
        $tabInterestsMember = $interestService->getListInterest($tabMemberInterests);

        // recup phone-call
        $phonecalls  = $this  ->getDoctrine()
                              ->getManager()
                              ->getRepository('LPPartnerBundle:PhoneCall')
                              ->findAll();

        echo "Member : " . $member->getName() . "  " . $idMember . "<br>";
        echo "Partner : " . $partner->getName() . "  " . $idPartner . "<br>";


        // add partner =======================================================================

        // verif si déjà partner
        $memberPartners = $member->getMyPartners($partner);
        echo gettype($memberPartners) . "<br>";
        foreach ($memberPartners as $partner) {

          //  echo gettype($partner) . "<br>";
            echo "partner Id  = " .  $partner->getId() . "<br>";

            if ($partner->getId() == $idPartner) {
                # code...
                $request->getSession()->getFlashBag()->add('info', 'Already partner !');
                return $this->redirect($this->generateUrl('lp_partner_search_partner', array('id' => $idMember)));
            }

        }

        $member->addMyPartner($partner);
        $em = $this->getDoctrine()->getManager();                
        $em->persist($member);
        $em->flush();
        $em->clear();

        $request->getSession()->getFlashBag()->add('info',  $partner->getFirstName() . ' ' . $partner->getName() . '
        added to ' . $member->getFirstName() . ' ' . $member->getName());
        return $this->redirect($this->generateUrl('lp_partner_search_partner', array('id' => $idMember)));




/*
        $member->addMyPartner($partner);

        $em = $this->getDoctrine()->getManager();
        $em->persist($member);
        $em->flush();

        $request->getSession()->getFlashBag()->add('info',  $partner->getFirstName() . ' ' . $partner->getName() . '
            added to ' . $member->getFirstName() . ' ' . $member->getName());
            return $this->redirect($this->generateUrl('lp_partner_search_partner', array('id' => $idMember)));

        }
        else{
            $request->getSession()->getFlashBag()->add('info', 'Form error !');
            return $this->redirect($this->generateUrl('lp_partner_search_partner', array('id' => $idMember)));
        }

*/


        // add partner ========================================================================

        // if already partner of member
/*
        $tabPartnersInside = $member->getMembers();

        foreach ($tabPartnersInside as $partner) {
            # code...
            echo $partner->getName() . "<br>";
        }
*/

        // Recup member's partners
 /*       $tabMyPartners = $member->getMyPartners();

        foreach ($tabMyPartners as $partner) {
            # code...
            echo $partner->getName() . "<br>";
        }

        // else

        $member->addMyPartner($partner);

        $em = $this->getDoctrine()->getManager();
        $em->persist($member);
        $em->flush();

        $request->getSession()->getFlashBag()->add('info',  $partner->getFirstName() . ' ' . $partner->getName() . '
             added to ' . $member->getFirstName() . ' ' . $member->getName());

        $idPartner = null;
*/
       // $tabPartners = $member->getMembers();


           

/*
            echo "<pre>";
            print_r($tabPartnersFound);
            echo "</pre>";
*/


        // rendering
        return $this->render('LPPartnerBundle:Partner:select-partner.html.twig', array(
          'member' => $member,
          'rangeMember' => $rangeMember,
          'phonecalls'  => $phonecalls,
          'tabInterests'                => $tabInterests,
          'totalInterests'              => $totalInterests,
          'tabInterestsMember'          => $tabInterestsMember
        ));

    }

}