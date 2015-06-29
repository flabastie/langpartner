<?php

// src/LP/PartnerBundle/Controller/DisplayController.php

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
 *      Class DisplayController
 *
 * ---------------------------------------------------------------------------------------------------- */

class DisplayController extends Controller
{

/* ------------------------------------------------------------------------------------------------------
 *      fonction memberListAction
 * ---------------------------------------------------------------------------------------------------- */

    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function listMemberAction($page, Request $request)
    {
        if ($page < 1) {
          throw new NotFoundHttpException('Page "'.$page.'" not found.');
        }

        $tabRange             = array();  // Array tabRange
        $tabMembersInterests  = array();  // Array interests
        $tabPhoneCallEval     = array();  // Array eval phonecalls
        $nbPerPage            = 25;       // pagination

        // recup members
        $membersList = $this->getDoctrine()
          ->getManager()
          ->getRepository('LPPartnerBundle:Member')
          ->getMembers($page, $nbPerPage)
        ;

        // services
        $em               = $this->getDoctrine()->getManager();             // recup EntityManager
        $agerange         = $this->container->get('lp_partner.agerange');   // recup service agerange
        $interestService  = $this->container->get('lp_partner.interest');   // recup service interest
        $phoneCallService = $this->container->get('lp_partner.phonecall');  // recup service phonecall

        // loop membersList
        foreach ($membersList as $member) {

            $id = $member->getId();

            // agerangeService
            $dateBirth = $member->getDateBirth();
            $tabRange[$id] = $agerange->calculateRangeAction($dateBirth);

            // service interest
            $tabMembersInterests[$id] = $interestService->getListInterest($member->getInterests());

            // total interests
            $tabTotalInterests[$id] = count($member->getInterests());

            // phonecall date evaluation
            $tabPhoneCallEval[$id] = $phoneCallService->evaluateDateCall($em, $member);

        }

        // recup phone-call
        $phonecalls  = $this  ->getDoctrine()
                              ->getManager()
                              ->getRepository('LPPartnerBundle:PhoneCall')
                              ->findAll();

        // pagination
        $nbPages = ceil(count($membersList)/$nbPerPage);

        // verif page
        if ($page > $nbPages) {
            //throw $this->createNotFoundException("La page ".$page." n'existe pas.");
            $request->getSession()->getFlashBag()->add('info', 'Please enter members in the database !');
            // redirection
            $url = $this->get('router')->generate('lp_partner_add_member');
            return $this->redirect($url);
        }

        return $this->render('LPPartnerBundle:Member:list-member.html.twig', array(
          'membersList'         => $membersList,
          'nbPages'             => $nbPages,
          'page'                => $page,
          'tabRange'            => $tabRange,
          'phonecalls'          => $phonecalls,
          'tabPhoneCallEval'    => $tabPhoneCallEval,
          'tabMembersInterests' => $tabMembersInterests,
          'tabTotalInterests'   => $tabTotalInterests
        ));
    }

/* ------------------------------------------------------------------------------------------------------
 *      fonction viewMemberAction
 * ---------------------------------------------------------------------------------------------------- */

    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function viewMemberAction($id, $page, Request $request)
    {

        $tabPartners = array();
        $tabPhoneCallEvalPartners = array();
        $tabPhoneCallsPartners = array();

        // Vérification
        if ($page === null) {
          throw $this->createNotFoundException("Page with id ".$page." no exists.");
        }

        // Récupèration EntityManager
        $em = $this->getDoctrine()->getManager();

        // recup 1 member
        $member = $em->getRepository('LPPartnerBundle:Member')->find($id);

        // recup partners
        $tabPartners = $member->getMyPartners();

        // Vérification
        if ($member === null) {
          throw $this->createNotFoundException("Member with id ".$id." no exists.");
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

        // phonecall =======================================================================================

        $idMember = $member->getId();
        $tabPhonecalls  = array();
        $tabPhonecalls  = $em ->getRepository('LPPartnerBundle:PhoneCall')
                              ->findBy(array('member' => $idMember));

        // phonecall date evaluation =======================================================================
        
        $phoneCallService = $this->container->get('lp_partner.phonecall'); // recup service phonecall
        $evaluationCall = $phoneCallService->evaluateDateCall($em, $member);
        //echo "evaluationCall = " . $evaluationCall . "<br>" ;

        // loop partners ===================================================================================

        foreach ($tabPartners as $partner) 
        {
          // partners agerange
          $id = $partner->getId();
          $dateBirth = $partner->getDateBirth();  // recup dateBirth
          $range = $agerange->calculateRangeAction($dateBirth); // calcul age range
          $tabPartnersRange[$id] = $range;

          $tabTotalPartnersInterest[$id] = count($partner->getInterests());
          $tabPartnersInterestsYesNo[$id] = $interestService->getListInterest($partner->getInterests());

          // recup phonecalls partner
          $phonecallsPartner  = $em ->getRepository('LPPartnerBundle:PhoneCall')
                              ->findBy(array('member' => $partner));
          $tabPhoneCallsPartners[$id] = count($phonecallsPartner);

          $tabPhoneCallEvalPartners[$id] = $phoneCallService->evaluateDateCall($em, $partner);
        }

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
          'member'          => $member,
          'rangeMember'           => $rangeMember,
          'tabPhonecalls'    => $tabPhonecalls,
          'evaluationCall'  => $evaluationCall,
          'totalMemberInterests' => $totalMemberInterests,
          'tabInterestsYesNo'    => $tabInterestsYesNo,
          'page'            => $page,
          'todayDate'       => $todayDate,
          'tabPartners'     => $tabPartners,
          'tabPartnersRange' => $tabPartnersRange,
          'tabTotalPartnersInterest' => $tabTotalPartnersInterest,
          'tabPartnersInterestsYesNo' => $tabPartnersInterestsYesNo,
          'tabPhoneCallEvalPartners'  => $tabPhoneCallEvalPartners,
          'tabPhoneCallsPartners' => $tabPhoneCallsPartners
        ));

    }


}