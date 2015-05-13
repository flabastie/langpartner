<?php

// src/LP/PartnerBundle/Controller/DeleteController.php

namespace LP\PartnerBundle\Controller;

use LP\PartnerBundle\Entity\Member;
use LP\PartnerBundle\Entity\PhoneCall;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/* ------------------------------------------------------------------------------------------------------
 *
 *      Class DisplayController
 *
 * ---------------------------------------------------------------------------------------------------- */

class DeleteController extends Controller
{

/* ------------------------------------------------------------------------------------------------------
 *      fonction deleteMemberAction
 * ---------------------------------------------------------------------------------------------------- */

    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function deleteMemberAction($id, Request $request)
    {

        // Récupèration EntityManager
        $em = $this->getDoctrine()->getManager();

        // recup member
        $member = $em->getRepository('LPPartnerBundle:Member')->find($id);
        $memberName = $member->getName();
        $memberFirstName = $member->getFirstName();

        // Vérification
        if ($member === null) {
          throw $this->createNotFoundException("Member with id ".$id." no exists.");
        }        

        // recup service phonecall + delete phonecalls ---------------------------------------------------

        $phonecallService = $this->container->get('lp_partner.phonecall'); // phonecall service
        $deletePhoneCalls = $phonecallService->deletePhoneCallsAction($em, $id);

        // recup servicepartners + remove partners -------------------------------------------------------

        $partnerService = $this->container->get('lp_partner.partner'); // partner service
        $myPartners = $member->getMyPartners();

        if (count($myPartners)>0 && $deletePhoneCalls==true) 
        {
            foreach ($myPartners as $partner) 
            {
                $partnerService->deselectPartner($em, $member, $partner);
            }
        }

        // delete member ----------------------------------------------------------------------------------

        if (count($myPartners)==0)
        {
            $em->remove($member); // delete member
            $em->flush(); 
            // info confirmation delete
            $request->getSession()->getFlashBag()->add('info', $memberFirstName . " " . $memberName . ' deleted !');
        }
        else {
            $request->getSession()->getFlashBag()->add('info', 'Error deleting ' . $memberFirstName . " " . $memberName);
        }

        return $this->redirect($this->generateUrl('lp_partner_member_list'));

    }




}