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
    public function selectPartnerAction($idMember, $idNewPartner, Request $request)
    {
        $page = 1; // default page
        $tabPartners = array();

        // Vérification
        if ($page === null) {
          throw $this->createNotFoundException("Page with id ".$page." no exists.");
        }

        // Récupèration EntityManager
        $em = $this->getDoctrine()->getManager();

        // adding a new partner =============================================================================

        $member         = $em->getRepository('LPPartnerBundle:Member')->find($idMember); // recup member
        $newPartner     = $em->getRepository('LPPartnerBundle:Member')->find($idNewPartner); // recup newpartner
        $partnerService = $this->container->get('lp_partner.partner'); // partner service
        $add = $partnerService->addPartner($em, $member, $newPartner);
        if ($add) {
            $request->getSession()->getFlashBag()->add('info', $newPartner->getName() .' : Partner added.');
        }
        else {
            $request->getSession()->getFlashBag()->add('info', $newPartner->getName() .' : Is already partner.');
        }



        // recup partners
        $tabPartners = $member->getMyPartners();

        // Vérification
        if ($member === null) {
          throw $this->createNotFoundException("Member with id ".$idMember." no exists.");
        }

        // member interests
        $tabMemberInterests = $member->getInterests();
        $totalMemberInterests = count($tabMemberInterests);

        // recup service interest
        $interestService = $this->container->get('lp_partner.interest');
        $tabInterestsYesNo = $interestService->getListInterest($tabMemberInterests);

        // recup service AgeRangeService
        $agerange = $this->container->get('lp_partner.agerange');
        $dateBirth = $member->getDateBirth(); // recup dateBirth
        $rangeMember = $agerange->calculateRangeAction($dateBirth); // calcul age range

        $tabPartnersRange = array(); // partners agerange
        $tabPartnersInterestsYesNo =array();
        $tabTotalPartnersInterest = array();

        foreach ($tabPartners as $partner) 
        {
          // partners agerange
          $id = $partner->getId();
          $dateBirth = $partner->getDateBirth();  // recup dateBirth
          $range = $agerange->calculateRangeAction($dateBirth); // calcul age range
          $tabPartnersRange[$id] = $range;

          $tabTotalPartnersInterest[$id] = count($partner->getInterests());
          $tabPartnersInterestsYesNo[$id] = $interestService->getListInterest($partner->getInterests());
        }

        // phonecall =======================================================================================

        $idMember = $member->getId();
        $tabPhonecalls  = array();
        $tabPhonecalls  = $em ->getRepository('LPPartnerBundle:PhoneCall')
                              ->findBy(array('member' => $idMember));

        // phonecall form ==================================================================================

        $user = $this->getUser();

        if (null === $user) {
          // Ici, l'utilisateur est anonyme ou l'URL n'est pas derrière un pare-feu
          $request->getSession()->getFlashBag()->add('info', 'Error Phone-call : User not found.');
        } else {
          // recup user (provisoire)
          //$user = $em->getRepository('LPUserBundle:User')->find(1);

          // today date
          $todayDate = new \Datetime();
          $phonecall = new PhoneCall();
          $phonecall->setMember($member);
          $phonecall->setUser($user);
          $phonecall->setDateCall($todayDate);

          $form = $this->get('form.factory')->create(new PhoneCallType(), $phonecall);

          if ($form->handleRequest($request)->isValid()) {

            // if noCall empty
            if ($phonecall->getNoteCall() == NULL) {
              $phonecall->setNoteCall("...");
            }

              $em = $this->getDoctrine()->getManager();
              $em->persist($phonecall);
              $em->flush();

              $request->getSession()->getFlashBag()->add('info', 'Phone-call well saved.');

            return $this->redirect($this->generateUrl('lp_partner_view_member', array('id' => $idMember, 'page' => $page)));
          }

        }
        // ========================= end phonecall form =======================================

        // rendering
        return $this->render('LPPartnerBundle:Member:view-member.html.twig', array(
          'form'            => $form->createView(),
          'page'            => $page,
          'member'          => $member,
          'rangeMember'           => $rangeMember,
          'tabPhonecalls'    => $tabPhonecalls,
          'totalMemberInterests' => $totalMemberInterests,
          'tabInterestsYesNo'    => $tabInterestsYesNo,
          'page'            => $page,
          'todayDate'       => $todayDate,
          'tabPartners'     => $tabPartners,
          'tabPartnersRange' => $tabPartnersRange,
          'tabTotalPartnersInterest' => $tabTotalPartnersInterest,
          'tabPartnersInterestsYesNo' => $tabPartnersInterestsYesNo
        ));

    }


/* ------------------------------------------------------------------------------------------------------
 *      fonction deselectPartnerAction
 * ---------------------------------------------------------------------------------------------------- */

    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function deselectPartnerAction($idMember, $idPartner, Request $request)
    {

      // Récupèration EntityManager
      $em = $this->getDoctrine()->getManager();

      $member           = $em->getRepository('LPPartnerBundle:Member')->find($idMember); // recup member
      $partnerToRemove  = $em->getRepository('LPPartnerBundle:Member')->find($idPartner); // recup partner
      
      // partner service
      $partnerService = $this->container->get('lp_partner.partner');
      $deseleted = $partnerService->deselectPartner($em, $member, $partnerToRemove);
      
      if ($deseleted) 
      {
        $request->getSession()->getFlashBag()->add('info', $partnerToRemove->getName() .' : Partner removed.');
      }
      else 
      {
        $request->getSession()->getFlashBag()->add('info', ' Error ! ');
      }

      return $this->redirect($this->generateUrl('lp_partner_view_member', array('id' => $idMember)));


    }


}