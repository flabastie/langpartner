<?php

// src/LP/PartnerBundle/Controller/SearchController.php

namespace LP\PartnerBundle\Controller;

use LP\PartnerBundle\Entity\Member;
use LP\PartnerBundle\Entity\Interest;
use LP\PartnerBundle\Entity\PhoneCall;
use LP\PartnerBundle\Form\PhoneCallType;
use LP\PartnerBundle\AgeRange\AgeRange;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\Session\Session;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class SearchController extends Controller
{

    /**
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function indexAction()
    {
        $content = $this->get('templating')->render('LPPartnerBundle:Partner:index.html.twig');
    	return new Response($content);
    }


/* ------------------------------------------------------------------------------------------------------
 *      fonction searchPartnerAction
 * ---------------------------------------------------------------------------------------------------- */

    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function searchPartnerAction($id, $page, Request $request)
    {
        // recup session 
        $session = $this->getRequest()->getSession();

        $tabSelectionChecked = array(
            'category'      => null,
            'agerange'      => null,
            'status'        => null,
            'availability'  => null,
            'englishlevel'  => null,
            'frenchlevel'   => null,
            'interests'     => null );

        $tabDateStartEnd = array();
        $partnersByCategory = array();
        $partnersByStatus = array();
        $partnersByAgeRange = array();
        $partnersByEnglishLevel = array();
        $partnersByFrenchLevel = array();

        $tabInterestsPartners = array();

        $tabAgerange                = array();
        $tabStatus                  = array();
    //    $tabAvailability            = array();
        $tabUserInterests           = array();
        $tabEnglishLevel            = array();
        $tabFrenchLevel             = array();

        $tabPartnersMerge           = array();
        $tabPartnersFound           = array();
        $tabRangePartners           = array(); //

        $tabTotalPartnerInterests   = array();
        $tabInterestsMember         = array();
        $tabIdAlreadyPartners         = array(); // already partners with this member ///
        $tabPhoneCallEvalPartners   = array();

        $nbInterests    = 3; // nbInterests by default

        // Vérification
        if ($page === null) {
          throw $this->createNotFoundException("Page with id ".$page." no exists.");
        }

        // Récupèration EntityManager
        $em = $this->getDoctrine()->getManager();

        // recup member
        $member = $em->getRepository('LPPartnerBundle:Member')->find($id);

        // Vérification
        if ($member === null) {
          throw $this->createNotFoundException("Member with id ".$id." no exists.");
        }

        // recup already partners
        $memberPartners = $member->getMyPartners();
        if (!empty($memberPartners)) 
        {
            foreach ($memberPartners as $alreadyPartner) 
            {
                $tabIdAlreadyPartners[] = $alreadyPartner->getId();
            }
        }
   
        // age range ========================================================================
        $agerange       = $this->container->get('lp_partner.agerange'); // service agerange
        $dateBirth      = $member->getDateBirth(); // recup dateBirth
        $memberRange    = $agerange->calculateRangeAction($dateBirth); // calcul age range

        // interests ========================================================================
        $interestService    = $this->container->get('lp_partner.interest'); // service interest
        $totalInterests     = count($member->getInterests()); // total interests
        $tabInterestsMember = $interestService->getListInterest($member->getInterests());

        // recup phone-call
        $phonecalls  = $this  ->getDoctrine()
                              ->getManager()
                              ->getRepository('LPPartnerBundle:PhoneCall')
                              ->findAll();

        // recup service phonecall
        $phoneCallService = $this->container->get('lp_partner.phonecall');  
        // phonecall date evaluation
        $evaluationCall = $phoneCallService->evaluateDateCall($em, $member);

      //  $searchService    = $this->container->get('lp_partner.search');                                              // service search


        $partnersByCategory     = $em   ->getRepository('LPPartnerBundle:Member')
                                        ->findPartners(  1, 0, 0, 0, null, null, null, $member, null);

        $partnersByAgeRange     = $em   ->getRepository('LPPartnerBundle:Member')
                                        ->findPartners(  0, 1, 0, 0, null, null, null, $member, $agerange->startEndRange($dateBirth));

        $partnersByStatus       = $em   ->getRepository('LPPartnerBundle:Member')
                                        ->findPartners(  0, 0, 1, 0, null, null, null, $member, null);

        $partnersByEnglishLevel = $em   ->getRepository('LPPartnerBundle:Member')
                                        ->findPartners(  0, 0, 0, 0, $member->getEnglishLevel(), null, null, $member, null);

        $partnersByFrenchLevel  = $em   ->getRepository('LPPartnerBundle:Member')
                                        ->findPartners(  0, 0, 0, 0, $member->getFrenchLevel(), null, null, $member, null);

        $partnersByInterests    = $em   ->getRepository('LPPartnerBundle:Member')
                                        ->findPartners(  0, 0, 0, 0, null, null, 3, $member, null);
          
        $partnersByAvailability = $em   ->getRepository('LPPartnerBundle:Member')
                                        ->findPartners(  0, 0, 0, 1, null, null, null, $member, null);

        // searchform =======================================================================

        $data = array();
        $form = $this   ->createFormBuilder($data)
                        ->add('category', 'checkbox')
                        ->add('agerange', 'checkbox')
                        ->add('status', 'checkbox')
                        ->add('availability', 'checkbox')
                        ->add('englishLevel',   'choice', array(
                                'choices'   => array(
                                    'Beginner'          => 'Beginner', 
                                    'Pre intermediate'  => 'Pre intermediate', 
                                    'Intermediate'      => 'Intermediate', 
                                    'Advanced'          => 'Advanced', 
                                    'Mother tongue'     => 'Mother tongue',
                                    'not_selected'      => 'All'),
                                    'required'          => true))
                        ->add('frenchLevel',    'choice', array(
                                'choices'   => array(
                                    'Beginner'          => 'Beginner', 
                                    'Pre intermediate'  => 'Pre intermediate', 
                                    'Intermediate'      => 'Intermediate', 
                                    'Advanced'          => 'Advanced', 
                                    'Mother tongue'     => 'Mother tongue',
                                    'not_selected'      => 'All'),
                                    'required'  => true))
                        ->add('interest', 'choice', array(
                                'choices' => array(
                                    '0'     => '0',
                                    '1'     => '1',
                                    '2'     => '2',
                                    '3'     => '3',
                                    '4'     => '4',
                                    '5'     => '5',
                                    '6'     => '6',
                                    '7'     => '7',
                                    '8'     => '8',
                                ),
                            ))
                        ->add('save', 'submit')
                        ->getForm();

        // recup form ======================================================================================================================

        if ($request->isMethod('POST')) {

            if (isset($_POST['form'])) 
            {
                if(isset($_POST['form']['category']) && ($_POST['form']['category']==1))    // checked category
                {
                    $session->set('category', 1);
                    $tabSelectionChecked['category'] = 1;
                }
                else {
                    $session->set('category', 0);      
                }

                if(isset($_POST['form']['agerange']) && ($_POST['form']['agerange']==1))    // checked agerange
                {
                    $session->set('agerange', 1);
                    $tabSelectionChecked['agerange'] = 1;
                    $tabDateStartEnd = $agerange->startEndRange($dateBirth);
                }
                else {
                    $session->set('agerange', 0);      
                }

                if(isset($_POST['form']['status']) && ($_POST['form']['status']==1))        // checked status
                {
                    $session->set('status', 1);
                    $tabSelectionChecked['status'] = 1;
                }
                else {
                    $session->set('status', 0);      
                }

                if(isset($_POST['form']['availability']) && ($_POST['form']['availability']==1)) // checked availability
                {
                    $session->set('availability', 1);
                    $tabSelectionChecked['availability'] = 1;
                }
                else {
                    $session->set('availability', 0);      
                }
            }

            if (isset($_POST['userEnglishLevel'])) 
            {
                if ($_POST['userEnglishLevel']!= 'not_selected')                            // checked englishlevel
                {
                    $session->set('englishlevel', $_POST['userEnglishLevel']); 
                    $tabSelectionChecked['englishlevel'] = $_POST['userEnglishLevel'];
                }
                else {
                    $session->set('englishlevel', $_POST['userEnglishLevel']);  
                }   
            }

            if (isset($_POST['userFrenchLevel'])) 
            {
                if ($_POST['userFrenchLevel']!= 'not_selected')                             // checked french level
                {
                    $session->set('frenchlevel', $_POST['userFrenchLevel']); 
                    $tabSelectionChecked['frenchlevel'] = $_POST['userFrenchLevel'];
                }
                else {
                    $session->set('frenchlevel', $_POST['userFrenchLevel']);  
                }   
            }           

            if (isset($_POST['userInterests'])) 
            {
                if ($_POST['userInterests'] < 9 and $_POST['userInterests'] >0) {
                    $session->set('userinterests', $_POST['userInterests']);                
                    $tabSelectionChecked['interests'] = $_POST['userInterests'];
                }
                elseif ($_POST['userInterests']==0) 
                {
                    $session->set('userinterests', 0);
                }
            }

            if (implode($tabSelectionChecked)) 
            {
           
                $tabPartnersFound = $em ->getRepository('LPPartnerBundle:Member')
                                        ->findPartners(  
                                            $tabSelectionChecked['category'],
                                            $tabSelectionChecked['agerange'],
                                            $tabSelectionChecked['status'],
                                            $tabSelectionChecked['availability'],
                                            $tabSelectionChecked['englishlevel'],
                                            $tabSelectionChecked['frenchlevel'],
                                            $tabSelectionChecked['interests'],
                                            $member,
                                            $tabDateStartEnd
                                            );

                foreach ($tabPartnersFound as $partner) 
                {
                    $idPartner = $partner->getId();

                    $tabRangePartners[$partner->getId()] = $agerange->calculateRangeAction($partner->getDateBirth());
                    $tabInterestsPartners[$partner->getId()] = $interestService->getListInterest($partner->getInterests());
                    $tabTotalPartnerInterests[$partner->getId()] = count($partner->getInterests());
                    $tabPhoneCallEvalPartners[$idPartner] = $phoneCallService->evaluateDateCall($em, $partner);
                }

            }

        }

        // rendering
        return $this->render('LPPartnerBundle:Partner:search-partner.html.twig', array(
          'form'                        => $form->createView(),
          'member'                      => $member,
          'memberRange'                 => $memberRange,
          'tabInterestsMember'          => $tabInterestsMember,
          'totalInterests'              => $totalInterests,
          'page'                        => $page,

          'partnersByCategory'         => $partnersByCategory,
          'partnersByStatus'           => $partnersByStatus,
          'partnersByAgeRange'         => $partnersByAgeRange,
          'partnersByEnglishLevel'     => $partnersByEnglishLevel,
          'partnersByFrenchLevel'      => $partnersByFrenchLevel,
          'partnersByInterests'        => $partnersByInterests,
          'partnersByAvailability'     => $partnersByAvailability,

          'tabPartnersFound'            => $tabPartnersFound,
          'tabRangePartners'            => $tabRangePartners,
          'tabInterestsPartners'        => $tabInterestsPartners,
          'tabTotalPartnerInterests'    => $tabTotalPartnerInterests,
          'tabPhoneCallEvalPartners'    => $tabPhoneCallEvalPartners,

          'phonecalls'                  => $phonecalls,
          'evaluationCall'              => $evaluationCall,
          
          'tabIdAlreadyPartners'        => $tabIdAlreadyPartners
        ));

    }

}