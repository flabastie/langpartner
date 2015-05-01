<?php

// src/LP/PartnerBundle/Controller/RegisterController.php

namespace LP\PartnerBundle\Controller;

use LP\PartnerBundle\Entity\Member;
use LP\PartnerBundle\Form\MemberType;
use LP\PartnerBundle\Entity\PhoneCall;
use LP\PartnerBundle\Form\PhoneCallType;
use LP\PartnerBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use \DateTime;

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
                    $format = 'd/m/Y';
                    $dateBirth = DateTime::createFromFormat($format, $_POST['form']['dateBirth']);
                    $member->setDateBirth($dateBirth);
                }
                else{
                    $valid = false;
                }  
                // profession
                if (isset($_POST['form']['profession']) and !empty($_POST['form']['profession'])) {
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
                if (isset($_POST['form']['telephoneBis']) and !empty($_POST['form']['telephoneBis'])) {
                    $member->setTelephoneBis($_POST['form']['telephoneBis']);
                }
                else{
                    $member->setTelephoneBis("Not specified");
                }  
                // objective
                if (isset($_POST['form']['objective']) and !empty($_POST['form']['objective'])) {
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
                    $format = 'd/m/Y';
                    $dateStart = DateTime::createFromFormat($format, $_POST['form']['dateStart']);
                    $member->setDateStart($dateStart);
                }
                else{
                    $valid = false;
                } 
                // dateEnd
                if (isset($_POST['form']['dateEnd'])) {
                    $format = 'd/m/Y';
                    $dateEnd = DateTime::createFromFormat($format, $_POST['form']['dateEnd']);
                    $member->setDateEnd($dateEnd);
                }
                else{
                    $valid = false;
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
            }

            // category
            if (isset($_POST['categoryRadioOptions'])) {
                $member->setCategory($_POST['categoryRadioOptions']);
            } else {
                $valid = false;
            }

            // interests
            if (isset($_POST['interestsCheckbox'])) {
                $member->setInterests($_POST['interestsCheckbox']);
            } else {
                $valid = false;
            }

            if ($valid ) {
                echo "VALID";
                $em = $this->getDoctrine()->getManager();
                $em->persist($member);
                $em->flush();

                $request->getSession()->getFlashBag()->add('info', 'Member well saved.');
                return $this->redirect($this->generateUrl('lp_partner_add_member'));
            }
            else{
                $request->getSession()->getFlashBag()->add('info', 'Form error !');
            }


        }

    	return $this->render('LPPartnerBundle:Member:add-member.html.twig', array(
      		'form' => $form->createView()
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