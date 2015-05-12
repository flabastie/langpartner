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
                    $member->setName("...");
                }  
                // firstName
                if (isset($_POST['form']['firstName'])) {
                    $member->setFirstName($_POST['form']['firstName']);
                }
                else{
                    $valid = false;
                    $member->setFirstName("...");
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
                    $member->setProfession("...");
                }   
                // email
                if (isset($_POST['form']['email'])) {
                    $member->setEmail($_POST['form']['email']);
                }
                else{
                    $valid = false;
                    $member->setEmail("...");
                }         
                // telephone
                if (isset($_POST['form']['telephone'])) {
                    $member->setTelephone($_POST['form']['telephone']);
                }
                else{
                    $valid = false;
                    $member->setTelephone("...");
                }   
                // telephoneBis
                if (isset($_POST['form']['telephoneBis']) and !empty($_POST['form']['telephoneBis'])) {
                    $member->setTelephoneBis($_POST['form']['telephoneBis']);
                }
                else{
                    $member->setTelephoneBis("...");
                }  
                // objective
                if (isset($_POST['form']['objective']) and !empty($_POST['form']['objective'])) {
                    $member->setObjective($_POST['form']['objective']);
                }
                else{
                    $member->setObjective("...");
                } 
                // englishLevel
                if (isset($_POST['form']['englishLevel'])) {
                    $member->setEnglishLevel($_POST['form']['englishLevel']);
                }
                else{
                    $valid = false;
                    $member->setEnglishLevel("...");
                } 
                // frenchLevel
                if (isset($_POST['form']['frenchLevel'])) {
                    $member->setFrenchLevel($_POST['form']['frenchLevel']);
                }
                else{
                    $valid = false;
                    $member->setFrenchLevel("...");
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
                    $member->setStatus("...");
                } 
                // membership
                if (isset($_POST['form']['membership'])) {
                    $member->setMembership($_POST['form']['membership']);
                }
                else{
                    $member->setMembership("...");
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
        /*
                echo "<pre>";
                print_r($_POST);
                echo "</pre>";
*/
        if ($page < 1) {
          throw new NotFoundHttpException('Page "'.$page.'" not found.');
        }

        // recup EntityManager
        $em = $this->getDoctrine()->getManager();
        // recup member
        $memberToEdit = $em->getRepository('LPPartnerBundle:Member')->find($id);

        $form = $this->get('form.factory')->create(new MemberType(), $memberToEdit);


// =======================================================================================================

        if ($request->isMethod('POST')) {

            if (isset($_POST['form'])) 
            {
                $valid = true;
                // name
                if (isset($_POST['form']['name'])) {
                    $memberToEdit->setName($_POST['form']['name']);
                }
                else{
                    $valid = false;
                    $memberToEdit->setName("...");
                }  
                // firstName
                if (isset($_POST['form']['firstName'])) {
                    $memberToEdit->setFirstName($_POST['form']['firstName']);
                }
                else{
                    $valid = false;
                    $memberToEdit->setFirstName("...");
                }  
                // dateBirth
                if (isset($_POST['form']['dateBirth'])) {
                    $format = 'd/m/Y';
                    $dateBirth = DateTime::createFromFormat($format, $_POST['form']['dateBirth']);
                    $memberToEdit->setDateBirth($dateBirth);
                }
                else{
                    $valid = false;
                }  
                // profession
                if (isset($_POST['form']['profession']) and !empty($_POST['form']['profession'])) {
                    $memberToEdit->setProfession($_POST['form']['profession']);
                }
                else{
                    $memberToEdit->setProfession("...");
                }   
                // email
                if (isset($_POST['form']['email'])) {
                    $memberToEdit->setEmail($_POST['form']['email']);
                }
                else{
                    $valid = false;
                    $memberToEdit->setEmail("...");
                }         
                // telephone
                if (isset($_POST['form']['telephone'])) {
                    $memberToEdit->setTelephone($_POST['form']['telephone']);
                }
                else{
                    $valid = false;
                    $memberToEdit->setTelephone("...");
                }   
                // telephoneBis
                if (isset($_POST['form']['telephoneBis']) and !empty($_POST['form']['telephoneBis'])) {
                    $memberToEdit->setTelephoneBis($_POST['form']['telephoneBis']);
                }
                else{
                    $memberToEdit->setTelephoneBis("...");
                }  
                // objective
                if (isset($_POST['form']['objective']) and !empty($_POST['form']['objective'])) {
                    $memberToEdit->setObjective($_POST['form']['objective']);
                }
                else{
                    $memberToEdit->setObjective("...");
                } 
                // englishLevel
                if (isset($_POST['form']['englishLevel'])) {
                    $memberToEdit->setEnglishLevel($_POST['form']['englishLevel']);
                }
                else{
                    $valid = false;
                    $memberToEdit->setEnglishLevel("...");
                } 
                // frenchLevel
                if (isset($_POST['form']['frenchLevel'])) {
                    $memberToEdit->setFrenchLevel($_POST['form']['frenchLevel']);
                }
                else{
                    $valid = false;
                    $memberToEdit->setFrenchLevel("...");
                } 
                // dateStart
                if (isset($_POST['form']['dateStart'])) {
                    $format = 'd/m/Y';
                    $dateStart = DateTime::createFromFormat($format, $_POST['form']['dateStart']);
                    $memberToEdit->setDateStart($dateStart);
                }
                else{
                    $valid = false;
                } 
                // dateEnd
                if (isset($_POST['form']['dateEnd'])) {
                    $format = 'd/m/Y';
                    $dateEnd = DateTime::createFromFormat($format, $_POST['form']['dateEnd']);
                    $memberToEdit->setDateEnd($dateEnd);
                }
                else{
                    $valid = false;
                } 
                // status
                if (isset($_POST['form']['status'])) {
                    $memberToEdit->setStatus($_POST['form']['status']);
                }
                else{
                    $memberToEdit->setStatus("...");
                } 
                // membership
                if (isset($_POST['form']['membership'])) {
                    $memberToEdit->setMembership($_POST['form']['membership']);
                }
                else{
                    $memberToEdit->setMembership("...");
                }  
                                
            }

            // category
            if (isset($_POST['categoryRadioOptions'])) {
                $memberToEdit->setCategory($_POST['categoryRadioOptions']);
            } else {
                $valid = false;
            }

            // interests
            if (isset($_POST['interestsCheckbox'])) {
                $memberToEdit->setInterests($_POST['interestsCheckbox']);
            } else {
                $valid = false;
            }

            if ($valid ) {

                $em = $this->getDoctrine()->getManager();
                $em->persist($memberToEdit);
                $em->flush();

                $request->getSession()->getFlashBag()->add('info', 'Member well modified.');
                return $this->redirect($this->generateUrl('lp_partner_view_member', array('id' => $id, 'page' => $page)));
            }
            else{
                $request->getSession()->getFlashBag()->add('info', 'Form error !');
            }
        }

        return $this->render('LPPartnerBundle:Member:edit-member.html.twig', array(
            'form' => $form->createView(),
            'id' => $id,
            'page' => $page,
            'memberToEdit' => $memberToEdit
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

        $phonecall = new PhoneCall();
        $phonecall->setMember($member);
        // recup user
        $user = $this->getUser();
        if (null === $user) {
            // Ici utilisateur anonyme ou URL pas derrière un pare-feu
            throw $this->createNotFoundException("User not valid");
        } else {
            $phonecall->setUser($user);
        }
        
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