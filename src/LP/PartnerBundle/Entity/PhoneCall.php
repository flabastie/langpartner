<?php

namespace LP\PartnerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PhoneCall
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="LP\PartnerBundle\Entity\PhoneCallRepository")
 */
class PhoneCall
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_call", type="date")
     */
    private $dateCall;

    /**
     * @var string
     *
     * @ORM\Column(name="note_call", type="text", nullable=true)
     */
    private $noteCall;

    /**
    * @ORM\ManyToOne(targetEntity="LP\PartnerBundle\Entity\Member")
    * @ORM\JoinColumn(nullable=false)
    */
    private $member;

    /**
    * @ORM\ManyToOne(targetEntity="LP\PartnerBundle\Entity\User")
    * @ORM\JoinColumn(nullable=false)
    */
    private $user;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set dateCall
     *
     * @param \DateTime $dateCall
     * @return PhoneCall
     */
    public function setDateCall($dateCall)
    {
        $this->dateCall = $dateCall;

        return $this;
    }

    /**
     * Get dateCall
     *
     * @return \DateTime 
     */
    public function getDateCall()
    {
        return $this->dateCall;
    }

    /**
     * Set noteCall
     *
     * @param string $noteCall
     * @return PhoneCall
     */
    public function setNoteCall($noteCall)
    {
        $this->noteCall = $noteCall;

        return $this;
    }

    /**
     * Get noteCall
     *
     * @return string 
     */
    public function getNoteCall()
    {
        return $this->noteCall;
    }

    /**
     * Set member
     *
     * @param \LP\PartnerBundle\Entity\Member $member
     * @return PhoneCall
     */
    public function setMember(\LP\PartnerBundle\Entity\Member $member)
    {
        $this->member = $member;

        return $this;
    }

    /**
     * Get member
     *
     * @return \LP\PartnerBundle\Entity\Member 
     */
    public function getMember()
    {
        return $this->member;
    }

    /**
     * Set user
     *
     * @param \LP\PartnerBundle\Entity\User $user
     * @return PhoneCall
     */
    public function setUser(\LP\PartnerBundle\Entity\User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \LP\PartnerBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }
}
