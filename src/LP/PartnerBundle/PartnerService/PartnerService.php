<?php
// src/LP/PartnerBundle/PartnerService/PartnerService.php

namespace LP\PartnerBundle\PartnerService;

use LP\PartnerBundle\Entity\Member;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManager;


/* ------------------------------------------------------------------------------------------------------
 *
 *      Class PartnerService
 *
 * ---------------------------------------------------------------------------------------------------- */

class PartnerService
{

  protected $em;

/* ------------------------------------------------------------------------------------------------------
 *      constructeur
 * ---------------------------------------------------------------------------------------------------- */

  public function __construct(EntityManager $em)
  {
    $this->em = $em;
  }


/* ------------------------------------------------------------------------------------------------------
 *      fonction addPartner
 * ---------------------------------------------------------------------------------------------------- */

  public function addPartner(EntityManager $em, $member, $newPartner)
  {

    $isPartner = $this->isPartner($member, $newPartner);
    $added = false;

    if (!$isPartner) 
    {
        $member->addMyPartner($newPartner);             
        $em->persist($member);

        $newPartner->addMyPartner($member);             
        $em->persist($newPartner);
        
        $em->flush();
        $em->clear();

        $added = true;
    }

  	return $added;
  }


/* ------------------------------------------------------------------------------------------------------
 *      fonction isPartner
 * ---------------------------------------------------------------------------------------------------- */

  public function isPartner($member, $newPartner)
  {
    $alreadyPartner         = false;
    $tabMemberPartners      = array();
    $tabNewPartnerPartners  = array();

        $tabMemberPartners = $member->getMyPartners();
        foreach ($tabMemberPartners as $partner) 
        {
            if ($partner->getid() == $newPartner->getid()) 
            {
                $alreadyPartner = true;
            }
        }

        $tabNewPartnerPartners = $newPartner->getMyPartners();
        foreach ($tabNewPartnerPartners as $partner) 
        {
            if ($partner->getid() == $member->getid()) 
            {
                $alreadyPartner = true;
            }
        }

    return $alreadyPartner;
  }

/* ------------------------------------------------------------------------------------------------------
 *      fonction deselectPartner
 * ---------------------------------------------------------------------------------------------------- */

  public function deselectPartner(EntityManager $em, $member, $partner)
  {
    $deselected = false;

    // verif already partner
    if ($this->isPartner($member, $partner)) 
    {
      $member->removeMyPartner($partner);
      $em->persist($member);

      $partner->removeMyPartner($member);
      $em->persist($partner);

      $em->flush();

      $deselected = true;
    }

    return $deselected;
  }


}