<?php

// src/LP/PartnerBundle/Controller/MatchController.php

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

class MatchController extends Controller
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
        //$session->set('foo', 'bar');
        //$foo = $session->get('foo');
        //{{ app.session.get('foo', 'bar'); }}

        $tabCategory                = array();
        $tabAgerange                = array();
        $tabStatus                  = array();
        $tabAvailability            = array();
        $tabUserInterests           = array();
        $tabPartnersMerge           = array();
        $tabPartnersFound           = array();
        $tabRangePartners           = array();
        $tabPartnerInterests        = array();
        $tabTotalPartnerInterests   = array();
        $tabInterestsMember         = array();
        $tabLabelSelection          = array(); // for display user selection

        $nbCriteria     = 0; // nb criteres de selection 
        $nbInterests    = 3; // nbInterests by default

        // Vérification
        if ($page === null) {
          throw $this->createNotFoundException("Page with id ".$page." no exists.");
        }

        // Récupèration EntityManager
        $em = $this->getDoctrine()->getManager();

        // recup member
        $member = $em->getRepository('LPPartnerBundle:Member')->find($id);
        // recup list members
        $allMembersList = $em->getRepository('LPPartnerBundle:Member')->findAll();
          

        // age range ========================================================================
        $agerange       = $this->container->get('lp_partner.agerange'); // service agerange
        $dateBirth      = $member->getDateBirth(); // recup dateBirth
        $range          = $agerange->calculateRangeAction($dateBirth); // calcul age range

        // interests ========================================================================
        $interestService    = $this->container->get('lp_partner.interest'); // service interest
        $tabMemberInterests = $member->getInterests();
        $tabInterests       = $interestService->getListInterest($tabMemberInterests);
        $totalInterests     = count($member->getInterests()); // total interests
        $tabInterestsMember = $interestService->getListInterest($tabMemberInterests);

        // Vérification
        if ($member === null) {
          throw $this->createNotFoundException("Member with id ".$id." no exists.");
        }

        // recup phone-call
        $phonecalls  = $this  ->getDoctrine()
                              ->getManager()
                              ->getRepository('LPPartnerBundle:PhoneCall')
                              ->findAll();

		// search by category ===============================================================
        $searchService       = $this->container->get('lp_partner.search'); // service search
        $catMember           = $member->getCategory();
        $membersListCategory = $searchService->searchByCategoryAction($em, $catMember);
        
        // search by status =================================================================
        $statusMember = $member->getStatus();
        $membersListStatus = $searchService->searchByStatusAction($em, $statusMember);
        
        // search by range ==================================================================
        $membersListRange = $searchService->searchByRangeAction($em, $dateBirth);

        // search by availability ===========================================================
        $membersListAvailability = $searchService->searchByAvailabilityAction($em, $member);

        // search by interest ===============================================================
        $membersListInterest = $searchService->searchByInterestAction($em, $member, $nbInterests, $allMembersList);
        
        // searchform =======================================================================
        $data = array();
        $form = $this   ->createFormBuilder($data)
                        ->add('selection', 'choice', array(
                                'choices' => array(
                                    'category'      => 'en-fr',
                                    'agerange'      => 'Age range',
                                    'status'        => 'Status',
                                    'availability'  => 'Availability',
                                ),
                                'expanded'  => true,
                                'multiple'  => true,
                            ))
                        ->add('englishLevel',   'choice', array(
                                'choices'   => array(
                                    'debutant'          => 'Débutant', 
                                    'faux_debutant'     => 'Faux Débutant', 
                                    'intermediaire'     => 'Intermédiaire', 
                                    'avance'            => 'Avancé', 
                                    'langue_maternelle' => 'Langue Maternelle'),
                                    'required'          => true))
                        ->add('frenchLevel',    'choice', array(
                                'choices'   => array(
                                    'debutant'          => 'Débutant', 
                                    'faux_debutant'     => 'Faux Débutant', 
                                    'intermediaire'     => 'Intermédiaire', 
                                    'avance'            => 'Avancé', 
                                    'langue_maternelle' => 'Langue Maternelle'),
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

        if ($request->isMethod('GET')) {

            // remplissage $tabCategory, $tabAgerange, $tabStatus, $tabAvailability, $tabUserInterests =====================================

            if (isset($_GET['userSelection'])) 
            {
                if(in_array("category", $_GET['userSelection'])) // checked category
                {
                    //echo "checked : category <br>";
                    $nbCriteria++;
                    $tabLabelSelection[] = "en-fr";
                    foreach ($membersListCategory as $partner) 
                    {
                        $tabCategory[] = $partner->getId();
                    }
                }
                if(in_array("agerange", $_GET['userSelection'])) // checked agerange
                {
                    //echo "checked : agerange <br>";
                    $nbCriteria++;
                    $tabLabelSelection[] = "Age Range";
                    foreach ($membersListRange as $partner) 
                    {
                        if ($member->getId() != $partner->getId()) {
                            $tabAgerange[] = $partner->getId();
                        }
                    }
                }
                if(in_array("status", $_GET['userSelection'])) // checked status
                {
                    //echo "checked : status <br>";
                    $nbCriteria++;
                    $tabLabelSelection[] = "Status";
                    foreach ($membersListStatus as $partner) 
                    {
                        if ($member->getId() != $partner->getId()) {
                            $tabStatus[] = $partner->getId();
                        }
                    }
                }
                if(in_array("availability", $_GET['userSelection'])) // checked availability
                {
                    //echo "checked : availability <br>";
                    $nbCriteria++;
                    $tabLabelSelection[] = "Availability";
                    foreach ($membersListAvailability as $partner) 
                    {
                        if ($member->getId() != $partner->getId()) {
                            $tabAvailability[] = $partner->getId();
                        }
                    }
                }
            }
            if (isset($_GET['userInterests']) and $_GET['userInterests'] < 10 and $_GET['userInterests'] >0) 
            {
               // echo $_GET['userInterests'];
                $nbCriteria++;
                if ($_GET['userInterests'] >1) {
                    $tabLabelSelection[] = $_GET['userInterests'] . " Interests";
                }
                else {
                    $tabLabelSelection[] = $_GET['userInterests'] . " Interest";
                }
                
                $membersListInterest = $searchService->searchByInterestAction($em, $member, $_GET['userInterests'], $allMembersList);

                foreach ($membersListInterest as $partnerId => $nbInt) 
                {
                    if ($member->getId() != $partnerId) {
                        $tabUserInterests[] = $partnerId;
                  //    echo $partnerId . "<br>";
                    }
                }
                
            }

            // intersect with $tabCategory, $tabAgerange, $tabStatus, $tabAvailability, $tabUserInterests =============================
          
            foreach ($allMembersList as $partner) 
            {
                $memberId = $partner->getId();

                // test in_array tabCategory
                if (!empty($tabCategory))
                {
                    if (in_array($memberId, $tabCategory)) 
                    {
                        $tabPartnersFound[$memberId] = 1;
                    }
                }
                // test in_array tabAgerange
                if (!empty($tabAgerange))
                {
                    if (in_array($memberId, $tabAgerange)) 
                    {
                        empty($tabPartnersFound[$memberId]) ? ($tabPartnersFound[$memberId]=1) : ($tabPartnersFound[$memberId]++);
                    }
                }
                // test in_array tabStatus
                if (!empty($tabStatus))
                {
                    if (in_array($memberId, $tabStatus)) 
                    {
                        empty($tabPartnersFound[$memberId]) ? ($tabPartnersFound[$memberId]=1) : ($tabPartnersFound[$memberId]++);
                    }
                }
                // test in_array tabAvailability
                if (!empty($tabAvailability))
                {
                    if (in_array($memberId, $tabAvailability)) 
                    {
                        empty($tabPartnersFound[$memberId]) ? ($tabPartnersFound[$memberId]=1) : ($tabPartnersFound[$memberId]++);
                    }
                }
                // test in_array tabUserInterests
                if (!empty($tabUserInterests))
                {
                    if (in_array($memberId, $tabUserInterests)) 
                    {
                        empty($tabPartnersFound[$memberId]) ? ($tabPartnersFound[$memberId]=1) : ($tabPartnersFound[$memberId]++);
                    }
                }

            }
            // order by value desc
            arsort ($tabPartnersFound);

           // echo "<br>" . $nbCriteria . " criteres de selection <br>";

            foreach ($tabPartnersFound as $id => $value) 
            {
             //   echo $id . " " . $value .  "<br>";
                if ($value < $nbCriteria) 
                { 
                    unset($tabPartnersFound[$id]);
                }
                else{
                    $tabPartnersFound[$id] = $em->getRepository('LPPartnerBundle:Member')->find($id);
                    $tabRangePartners[$id] = $agerange->calculateRangeAction($tabPartnersFound[$id]->getDateBirth());

                    // service interest
                    $tabInterests = $interestService->getListInterest($tabPartnersFound[$id]->getInterests());
                    $tabPartnerInterests[$id] = $tabInterests;

                    // total interests
                    $tabTotalPartnerInterests[$id] = count($tabPartnersFound[$id]->getInterests());   
                }
            }
/*
            echo "<pre>";
            print_r($tabPartnersFound);
            echo "</pre>";
*/
        }

        // rendering
        return $this->render('LPPartnerBundle:Partner:search-partner.html.twig', array(
          'form'                        => $form->createView(),
          'member'                      => $member,
          'range'                       => $range,
          'tabInterests'                => $tabInterests,
          'totalInterests'              => $totalInterests,
          'page'                        => $page,
          'membersListCategory'         => $membersListCategory,
          'membersListStatus'           => $membersListStatus,
          'membersListRange'            => $membersListRange,
          'membersListAvailability'     => $membersListAvailability,
          'membersListInterest'         => $membersListInterest,
          'allMembersList'              => $allMembersList,
          'tabPartnersFound'            => $tabPartnersFound,
          'tabRangePartners'            => $tabRangePartners,
          'phonecalls'                  => $phonecalls,
          'tabPartnerInterests'         => $tabPartnerInterests,
          'tabTotalPartnerInterests'    => $tabTotalPartnerInterests,
          'tabInterestsMember'          => $tabInterestsMember,
          'tabLabelSelection'           => $tabLabelSelection
        ));

    }

}