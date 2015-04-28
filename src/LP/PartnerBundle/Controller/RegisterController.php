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

    public function addMemberAction(Request $request)
    {
    	$member = new Member();
    	$form = $this->get('form.factory')->create(new MemberType(), $member);

	    if ($form->handleRequest($request)->isValid()) {
	     	$em = $this->getDoctrine()->getManager();
	     	$em->persist($member);
	      	$em->flush();

	      	$request->getSession()->getFlashBag()->add('info', 'Member well saved.');

	      return $this->redirect($this->generateUrl('lp_partner_member_list'));
	    }

    	return $this->render('LPPartnerBundle:Member:add-member.html.twig', array(
      		'form' => $form->createView(),
    	));
    }

/* ------------------------------------------------------------------------------------------------------
 *      fonction editMemberAction
 * ---------------------------------------------------------------------------------------------------- */

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