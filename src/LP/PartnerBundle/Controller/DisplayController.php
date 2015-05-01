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

        // pagination
        $nbPerPage = 12;

        $membersList = $this->getDoctrine()
          ->getManager()
          ->getRepository('LPPartnerBundle:Member')
          ->getMembers($page, $nbPerPage)
        ;

        // Récupèration EntityManager
        $em = $this->getDoctrine()->getManager();

        // Array tabRange
        $tabRange = array();
        // recup service agerange
        $agerange = $this->container->get('lp_partner.agerange');
        // Array interests
        $tabMembersInterests  = array();

        // recup service interest
        $interestService = $this->container->get('lp_partner.interest');

        // agerangeService
        foreach ($membersList as $member) {

            $id = $member->getId();
            $dateBirth = $member->getDateBirth();
            $range = $agerange->calculateRangeAction($dateBirth);
            $tabRange[$id] = $range;

            // service interest
            $tabMembersInterests[$id] = $interestService->getListInterest($member->getInterests());

            // total interests
            $tabTotalInterests[$id] = count($member->getInterests());
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
          'tabMembersInterests' => $tabMembersInterests,
          'tabTotalInterests'   => $tabTotalInterests
        ));
    }

/* ------------------------------------------------------------------------------------------------------
 *      fonction dashboardAction
 * ---------------------------------------------------------------------------------------------------- */

    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function dashboardAction(Request $request)
    {
        return $this->render('LPPartnerBundle:Dashboard:dashboard.html.twig');
    }

/* ------------------------------------------------------------------------------------------------------
 *      fonction viewMemberAction
 * ---------------------------------------------------------------------------------------------------- */

    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function viewMemberAction($id, $page, Request $request)
    {

        // Vérification
        if ($page === null) {
          throw $this->createNotFoundException("Page with id ".$page." no exists.");
        }

        // Récupèration EntityManager
        $em = $this->getDoctrine()->getManager();

        // recup 1 member
        $member = $em->getRepository('LPPartnerBundle:Member')->find($id);

        // Vérification
        if ($member === null) {
          throw $this->createNotFoundException("Member with id ".$id." no exists.");
        }

        // recup service listchoice
/*        $listchoice = $this->container->get('lp_partner.listchoice');
        $categoryKey = $member->getCategory();
        $category = $listchoice->categoryChoiceAction($categoryKey);
        $membership = $listchoice->membershipChoiceAction($categoryKey);
        $status = $listchoice->statusChoiceAction($categoryKey);
*/
        // recup service AgeRangeService
        $agerange = $this->container->get('lp_partner.agerange');
        // recup dateBirth
        $dateBirth = $member->getDateBirth();
        // calcul age range
        $range = $agerange->calculateRangeAction($dateBirth);

        // member interests
        $tabMemberInterests = $member->getInterests();
        $totalMemberInterests = count($tabMemberInterests);

        // recup service interest
        $interestService = $this->container->get('lp_partner.interest');
        $tabInterestsYesNo = $interestService->getListInterest($tabMemberInterests);

        // recup entity phone-call
        $listPhonecall  = $em ->getRepository('LPPartnerBundle:PhoneCall')
                              ->findBy(array('member' => $id));

        $totalPhoneCalls = 0;
        $lastdatecall = NULL;

        if ($listPhonecall) {
          foreach ( $listPhonecall as $phonecall ) {
            // recup NoteCall
            //echo $phonecall->getNoteCall() . "<br>";
            // recup DateCall
            $lastdatecall = $phonecall->getDateCall();
            // comptage de calls
            $totalPhoneCalls++;
          }
          // transfo date to string
          $lastdatecall = $lastdatecall->format('d-m-Y');
        }

        // ======================== phonecall form ===========================================

        // recup user (provisoire)
        $user = $em->getRepository('LPUserBundle:User')->find(1);

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

          return $this->redirect($this->generateUrl('lp_partner_view_member', array('id' => $id, 'page' => $page)));
        }

        // ========================= end phonecall form =======================================

        // rendering
        return $this->render('LPPartnerBundle:Member:view-member.html.twig', array(
          'form'            => $form->createView(),
          'member'          => $member,
          'range'           => $range,
          'totalPhoneCalls' => $totalPhoneCalls,
          'lastdatecall'    => $lastdatecall,
          'totalMemberInterests' => $totalMemberInterests,
          'tabInterestsYesNo'    => $tabInterestsYesNo,
          'page'            => $page,
          'listPhonecall'   => $listPhonecall,
          'todayDate'       => $todayDate
        ));

    }


}