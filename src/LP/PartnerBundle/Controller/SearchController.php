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
        $tabIdAlreadyPartners         = array(); // already partners with this member

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
                        ->add('category', 'checkbox')
                        ->add('agerange', 'checkbox')
                        ->add('status', 'checkbox')
                        ->add('availability', 'checkbox')
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

//echo $session->get('category');

        if ($request->isMethod('POST')) {
/*
            echo "<pre>";
            print_r($_POST);
            echo "</pre>";
*/
            // remplissage $tabCategory, $tabAgerange, $tabStatus, $tabAvailability, $tabUserInterests =====================================

            if (isset($_POST['form'])) 
            {

                if(isset($_POST['form']['category']) && ($_POST['form']['category']==1)) // checked category
                {
                    //echo "checked : category <br>";
                    $nbCriteria++;
                    $session->set('category', 1);
                    foreach ($membersListCategory as $partner) 
                    {
                        $tabCategory[] = $partner->getId();
                    }
                }
                else {
                    $session->set('category', 0);      
                }

                if(isset($_POST['form']['agerange']) && ($_POST['form']['agerange']==1)) // checked agerange
                {
                    //echo "checked : agerange <br>";
                    $nbCriteria++;
                    $session->set('agerange', 1);
                    foreach ($membersListRange as $partner) 
                    {
                        if ($member->getId() != $partner->getId()) {
                            $tabAgerange[] = $partner->getId();
                        }
                    }
                }
                else {
                    $session->set('agerange', 0);      
                }

                if(isset($_POST['form']['status']) && ($_POST['form']['status']==1)) // checked status
                {
                    //echo "checked : status <br>";
                    $nbCriteria++;
                    $session->set('status', 1);
                    foreach ($membersListStatus as $partner) 
                    {
                        if ($member->getId() != $partner->getId()) {
                            $tabStatus[] = $partner->getId();
                        }
                    }
                }
                else {
                    $session->set('status', 0);      
                }

                if(isset($_POST['form']['availability']) && ($_POST['form']['availability']==1)) // checked availability
                {
                    //echo "checked : availability <br>";
                    $nbCriteria++;
                    $session->set('availability', 1);
                    foreach ($membersListAvailability as $partner) 
                    {
                        if ($member->getId() != $partner->getId()) {
                            $tabAvailability[] = $partner->getId();
                        }
                    }
                }
                else {
                    $session->set('availability', 0);      
                }
            }

            if (isset($_POST['userEnglishLevel'])) 
            {
                //echo "checked : userEnglishLevel <br>";
             //   $nbCriteria++;
                $session->set('englishlevel', $_POST['userEnglishLevel']);

            }

            if (isset($_POST['userFrenchLevel'])) 
            {
                //echo "checked : userFrenchLevel <br>";
            //    $nbCriteria++;
                $session->set('frenchlevel', $_POST['userFrenchLevel']);

            }

            if (isset($_POST['userInterests']) and $_POST['userInterests'] < 10 and $_POST['userInterests'] >0) 
            {
                //echo $_POST['userInterests'];
                $nbCriteria++;
                $session->set('userinterests', $_POST['userInterests']);                
                $membersListInterest = $searchService->searchByInterestAction($em, $member, $_POST['userInterests'], $allMembersList);

                foreach ($membersListInterest as $partnerId => $nbInt) 
                {
                    if ($member->getId() != $partnerId) {
                        $tabUserInterests[] = $partnerId;
                  //    echo $partnerId . "<br>";
                    }
                } 
            }
            elseif (isset($_POST['userInterests']) and $_POST['userInterests']==0) 
            {
                $session->set('userinterests', 0);
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
          'tabIdAlreadyPartners'        => $tabIdAlreadyPartners
        ));

    }

}