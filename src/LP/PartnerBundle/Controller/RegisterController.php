<?php

// src/LP/PartnerBundle/Controller/RegisterController.php

namespace LP\PartnerBundle\Controller;

use LP\PartnerBundle\Entity\Member;
use LP\PartnerBundle\Form\MemberType;
use LP\PartnerBundle\Entity\Interest;
use LP\PartnerBundle\Form\InterestType;
use LP\PartnerBundle\Entity\PhoneCall;
use LP\PartnerBundle\Form\PhoneCallType;
use LP\PartnerBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;


/* ------------------------------------------------------------------------------------------------------
 *
 *      Class RegisterController
 *
 * ---------------------------------------------------------------------------------------------------- */

class RegisterController extends Controller
{

/* ------------------------------------------------------------------------------------------------------
 *      fonction addMemberAction
 * ---------------------------------------------------------------------------------------------------- */

    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function addMemberAction(Request $request)
    {
    	$member = new Member();
    	$form = $this->get('form.factory')->create(new MemberType(), $member);

        // today date
        $todayDate = new \Datetime();

        if ($request->isMethod('POST')) {

            if (isset($_POST['form'])) 
            {
                $valid = true;
                // name
                if (isset($_POST['form']['name'])) {
                    $member->setName($_POST['form']['name']);
                }
                else{
                    $valid = false;
                    $member->setName("Not specified");
                }  
                // firstName
                if (isset($_POST['form']['firstName'])) {
                    $member->setFirstName($_POST['form']['firstName']);
                }
                else{
                    $valid = false;
                    $member->setFirstName("Not specified");
                }  
                // dateBirth
                if (isset($_POST['form']['dateBirth'])) {
                    $member->setDateBirth($_POST['form']['dateBirth']);
                }
                else{
                    $valid = false;
                    $dateDefault = new DateTime();
                    $dateDefault->setDate(1970, 1, 1);
                    $member->setDateBirth($dateDefault);
                }  
                // profession
                if (isset($_POST['form']['profession'])) {
                    $member->setProfession($_POST['form']['profession']);
                }
                else{
                    $member->setProfession("Not specified");
                }   
                // email
                if (isset($_POST['form']['email'])) {
                    $member->setEmail($_POST['form']['email']);
                }
                else{
                    $valid = false;
                    $member->setEmail("Not specified");
                }         
                // telephone
                if (isset($_POST['form']['telephone'])) {
                    $member->setTelephone($_POST['form']['telephone']);
                }
                else{
                    $valid = false;
                    $member->setTelephone("Not specified");
                }   
                // telephoneBis
                if (isset($_POST['form']['telephoneBis'])) {
                    $member->setTelephoneBis($_POST['form']['telephoneBis']);
                }
                else{
                    $member->setTelephoneBis("Not specified");
                }  
                // objective
                if (isset($_POST['form']['objective'])) {
                    $member->setObjective($_POST['form']['objective']);
                }
                else{
                    $member->setObjective("Not specified");
                } 
                // englishLevel
                if (isset($_POST['form']['englishLevel'])) {
                    $member->setEnglishLevel($_POST['form']['englishLevel']);
                }
                else{
                    $valid = false;
                    $member->setEnglishLevel("Not specified");
                } 
                // frenchLevel
                if (isset($_POST['form']['frenchLevel'])) {
                    $member->setFrenchLevel($_POST['form']['frenchLevel']);
                }
                else{
                    $valid = false;
                    $member->setFrenchLevel("Not specified");
                } 
                // dateStart
                if (isset($_POST['form']['dateStart'])) {
                    $member->setDateStart($_POST['form']['dateStart']);
                }
                else{
                    $valid = false;
                    $todayDate = new \Datetime();
                    $member->setDateStart($todayDate);
                } 
                // dateEnd
                if (isset($_POST['form']['dateEnd'])) {
                    $member->setDateEnd($_POST['form']['dateEnd']);
                }
                else{
                    $valid = false;
                    $dateEnd=date("Y-m-d", strtotime("+1 year"));
                    $member->setDateEnd($dateEnd);
                } 
                // status
                if (isset($_POST['form']['status'])) {
                    $member->setStatus($_POST['form']['status']);
                }
                else{
                    $member->setStatus("Not specified");
                } 
                // membership
                if (isset($_POST['form']['membership'])) {
                    $member->setMembership($_POST['form']['membership']);
                }
                else{
                    $member->setMembership("Not specified");
                }  


                /*
                echo "<pre>";
                print_r($_POST);
                echo "</pre>";
                */
/*
                echo $_POST['form']['name'] . "<br>";
                echo $_POST['form']['firstName'] . "<br>";
                echo $_POST['form']['dateBirth'] . "<br>";
                echo $_POST['form']['profession'] . "<br>";
                echo $_POST['form']['email'] . "<br>";
                echo $_POST['form']['telephone'] . "<br>";
                echo $_POST['form']['telephoneBis'] . "<br>";
                echo $_POST['form']['objective'] . "<br>";
                echo $_POST['form']['englishLevel'] . "<br>";
                echo $_POST['form']['frenchLevel'] . "<br>";
                echo $_POST['form']['dateStart'] . "<br>";
                echo $_POST['form']['dateEnd'] . "<br>";
                echo $_POST['form']['status'] . "<br>";
                echo $_POST['form']['membership'] . "<br>";
*/
            }

            if (isset($_POST['categoryRadioOptions'])) 
            {
                echo $_POST['categoryRadioOptions'] . "<br>";
                $member->setCategory($_POST['categoryRadioOptions']);
            }


            if (isset($_POST['interestsCheckbox'])) 
            {
                foreach ($_POST['interestsCheckbox'] as $value) {
                    echo $value . "<br>";
                }
            } else {
                $valid = false;
            }


    }



	    if ($form->handleRequest($request)->isValid()) {
	  /*   	$em = $this->getDoctrine()->getManager();
	     	$em->persist($member);
	      	$em->flush();

	      	$request->getSession()->getFlashBag()->add('info', 'Member well saved.');

	    //  return $this->redirect($this->generateUrl('lp_partner_member_list'));
*/
            echo "OK";


	    }

    	return $this->render('LPPartnerBundle:Member:add-member.html.twig', array(
      		'form' => $form->createView(),
            'todayDate' => $todayDate
    	));
    }

/* ------------------------------------------------------------------------------------------------------
 *      fonction editMemberAction
 * ---------------------------------------------------------------------------------------------------- */

    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function editMemberAction($id, $page, Request $request)
    {
        if ($page < 1) {
          throw new NotFoundHttpException('Page "'.$page.'" not found.');
        }

        // recup EntityManager
        $em = $this->getDoctrine()->getManager();
        // recup member
        $editMember = $em->getRepository('LPPartnerBundle:Member')->find($id);

        $form = $this->get('form.factory')->create(new MemberType(), $editMember);

        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($editMember);
            $em->flush();

            $request->getSession()->getFlashBag()->add('info', 'Member well modified.');

          return $this->redirect($this->generateUrl('lp_partner_view_member', array('id' => $id, 'page' => $page)));

        }

        return $this->render('LPPartnerBundle:Member:edit-member.html.twig', array(
            'form' => $form->createView(),
            'id' => $id,
            'page' => $page,
            'editMember' => $editMember
        ));
    }

/* ------------------------------------------------------------------------------------------------------
 *      fonction addInterestAction
 * ---------------------------------------------------------------------------------------------------- */

    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function addInterestAction(Request $request)
    {

        // Recup list interests
        $interestsList  = $this->getDoctrine()
            ->getManager()
            ->getRepository('LPPartnerBundle:Interest')
            ->findAll();

        $interest = new Interest();
        $form = $this->get('form.factory')->create(new InterestType(), $interest);

        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($interest);
            $em->flush();

            $request->getSession()->getFlashBag()->add('info', 'Interest well saved.');

          return $this->redirect($this->generateUrl('lp_partner_add_interest'));
        }

        return $this->render('LPPartnerBundle:Interest:add-interest.html.twig', array(
            'form' => $form->createView(),
            'interestsList' => $interestsList
        ));
    }

/* ------------------------------------------------------------------------------------------------------
 *      fonction addPhonecallAction
 * ---------------------------------------------------------------------------------------------------- */

    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function addPhonecallAction($id, Request $request)
    {

        // Récupèration EntityManager
        $em = $this->getDoctrine()->getManager();

        // Pour récupérer une annonce unique : on utilise find()
        $member = $em->getRepository('LPPartnerBundle:Member')->find($id);

        // Vérification
        if ($member === null) {
          throw $this->createNotFoundException("Member with id ".$id." no exists.");
        }

        // recup user
        $user = $em->getRepository('LPPartnerBundle:User')->find(1);
/*
        // Recup list interests
        $phonecallList  = $this->getDoctrine()
            ->getManager()
            ->getRepository('LPPartnerBundle:PhoneCall')
            ->findAll();
*/
        $phonecall = new PhoneCall();
        $phonecall->setMember($member);
        $phonecall->setUser($user);
        
        $form = $this->get('form.factory')->create(new PhoneCallType(), $phonecall);

        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($phonecall);
            $em->flush();

            $request->getSession()->getFlashBag()->add('info', 'Phone-call well saved.');

          return $this->redirect($this->generateUrl('lp_partner_view_member', array('id' => $id)));
        }

        return $this->render('LPPartnerBundle:PhoneCall:add-phonecall.html.twig', array(
            'form' => $form->createView(),
            'member' => $member
        ));
    }

}