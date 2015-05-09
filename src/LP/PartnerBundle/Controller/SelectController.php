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

    /*
     * @Security("has_role('ROLE_USER')")
     */
    public function selectPartnerAction($idMember, $idNewPartner, Request $request)
    {
      // Recup EntityManager
      $em = $this->getDoctrine()->getManager();

      // adding a new partner =============================================================================

      $member         = $em->getRepository('LPPartnerBundle:Member')->find($idMember); // recup member
      $newPartner     = $em->getRepository('LPPartnerBundle:Member')->find($idNewPartner); // recup newpartner

      // partner service ==================================================================================
      $partnerService = $this->container->get('lp_partner.partner'); // partner service
      $add = $partnerService->addPartner($em, $member, $newPartner);
      
      if ($add) {
        $request->getSession()->getFlashBag()->add('info', $newPartner->getName() .' : Partner added.');
      }
      else {
        $request->getSession()->getFlashBag()->add('info', $newPartner->getName() .' : Is already partner.');
      }

      // redirect to lp_partner_view_member
      return $this->redirect($this->generateUrl('lp_partner_view_member', array('id' => $idMember)));
    }


/* ------------------------------------------------------------------------------------------------------
 *      fonction deselectPartnerAction
 * ---------------------------------------------------------------------------------------------------- */

    /*
     * @Security("has_role('ROLE_USER')")
     */
    public function deselectPartnerAction($idMember, $idPartner, Request $request)
    {
      // recup EntityManager
      $em = $this->getDoctrine()->getManager();

      $member           = $em->getRepository('LPPartnerBundle:Member')->find($idMember);  // recup member
      $partnerToRemove  = $em->getRepository('LPPartnerBundle:Member')->find($idPartner); // recup partner
      
      // partner service ==================================================================================
      $partnerService = $this->container->get('lp_partner.partner');
      $deseleted      = $partnerService->deselectPartner($em, $member, $partnerToRemove);
      
      if ($deseleted) 
      {
        $request->getSession()->getFlashBag()->add('info', $partnerToRemove->getFirstName() . ' ' . $partnerToRemove->getName() .' removed.');
      }
      else 
      {
        $request->getSession()->getFlashBag()->add('info', ' Error ! ');
      }

      // redirect to lp_partner_view_member
      return $this->redirect($this->generateUrl('lp_partner_view_member', array('id' => $idMember)));
    }


}