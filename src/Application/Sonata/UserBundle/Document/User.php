<?php

namespace Application\Sonata\UserBundle\Document;

use FOS\UserBundle\Document\User as BaseUser;
#use Sonata\UserBundle\Document\BaseUser;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\Document
 * @MongoDB\HasLifecycleCallbacks
 */
class User extends BaseUser
{
    /**
     * @MongoDB\Id(strategy="auto")
     */
    protected $id;

    /**
     * @MongoDB\Field(type="date")
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @MongoDB\Field(type="date")
     * @var \DateTime
     */
    protected $updatedAt;

    /**
     * @MongoDB\Field(type="boolean")
     */
    protected $credentialsExpired;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $token;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $twoStepVerificationCode;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $firstname;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $lastname;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $phone;

    /**
     * @MongoDB\Field(type="date")
     * @var \DateTime
     */
    protected $dateOfBirth;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $website;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $biography;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $gender;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $locale;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $timezone;

    /**
     * @MongoDB\Field(type="collection")
     */
    protected $realRoles = array();

    /**
     * @MongoDB\Field(type="string")
     */
    protected $facebookUid;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $facebookName;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $twitterUid;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $twitterName;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $gplusUid;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $gplusName;

    /**
     * @MongoDB\ReferenceMany(targetDocument="Application\Sonata\UserBundle\Document\Group")
     */
    protected $groups = array();

    /**
     * Hook on pre-persist operations
     * @MongoDB\PrePersist
     */
    public function prePersist()
    {
        $this->createdAt = new \DateTime;
        $this->updatedAt = new \DateTime;
    }
    /**
     * Hook on pre-update operations
     * @MongoDB\PreUpdate
     */
    public function preUpdate()
    {
        $this->updatedAt = new \DateTime;
    }

    public function __construct()
    {
        $this->groups = new \Doctrine\Common\Collections\ArrayCollection();
        parent::__construct();
    }

    public static function getGenderList()
    {
        return array(
            'Male',
            'Female',
            'Other'
        );
    }
    
    /**
     * Get id
     *
     * @return id $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Add group
     *
     * @param Application\Sonata\UserBundle\Document\Group $group
     */
    public function addGroup(\FOS\UserBundle\Model\GroupInterface $group)
    {
        $this->groups[] = $group;
    }

    /**
     * Remove group
     *
     * @param Application\Sonata\UserBundle\Document\Group $group
     */
    public function removeGroup(\FOS\UserBundle\Model\GroupInterface $group)
    {
        $this->groups->removeElement($group);
    }

    /**
     * Get groups
     *
     * @return Doctrine\Common\Collections\Collection $groups
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * Set createdAt
     *
     * @param DateTime $createdAt
     * @return self
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * Get createdAt
     *
     * @return DateTime $createdAt
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param DateTime $updatedAt
     * @return self
     */
    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return DateTime $updatedAt
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set firstname
     *
     * @param string $firstname
     * @return self
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
        return $this;
    }

    /**
     * Get firstname
     *
     * @return string $firstname
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set lastname
     *
     * @param string $lastname
     * @return self
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
        return $this;
    }

    /**
     * Get lastname
     *
     * @return string $lastname
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * Set phone
     *
     * @param string $phone
     * @return self
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
        return $this;
    }

    /**
     * Get phone
     *
     * @return string $phone
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set credentialsExpired
     *
     * @param DateTime $credentialsExpired
     * @return self
     */
    public function setCredentialsExpired($credentialsExpired)
    {
        $this->credentialsExpired = $credentialsExpired;
        return $this;
    }

    /**
     * Get credentialsExpired
     *
     * @return DateTime $credentialsExpired
     */
    public function getCredentialsExpired()
    {
        return $this->credentialsExpired;
    }

    /**
     * Set token
     *
     * @param string $token
     * @return self
     */
    public function setToken($token)
    {
        $this->token = $token;
        return $this;
    }

    /**
     * Get token
     *
     * @return string $token
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set twoStepVerificationCode
     *
     * @param string $twoStepVerificationCode
     * @return self
     */
    public function setTwoStepVerificationCode($twoStepVerificationCode)
    {
        $this->twoStepVerificationCode = $twoStepVerificationCode;
        return $this;
    }

    /**
     * Get twoStepVerificationCode
     *
     * @return string $twoStepVerificationCode
     */
    public function getTwoStepVerificationCode()
    {
        return $this->twoStepVerificationCode;
    }

    /**
     * Set facebookUid
     *
     * @param string $facebookUid
     * @return self
     */
    public function setFacebookUid($facebookUid)
    {
        $this->facebookUid = $facebookUid;
        return $this;
    }

    /**
     * Get facebookUid
     *
     * @return string $facebookUid
     */
    public function getFacebookUid()
    {
        return $this->facebookUid;
    }

    /**
     * Set facebookName
     *
     * @param string $facebookName
     * @return self
     */
    public function setFacebookName($facebookName)
    {
        $this->facebookName = $facebookName;
        return $this;
    }

    /**
     * Get facebookName
     *
     * @return string $facebookName
     */
    public function getFacebookName()
    {
        return $this->facebookName;
    }

    /**
     * Set twitterUid
     *
     * @param string $twitterUid
     * @return self
     */
    public function setTwitterUid($twitterUid)
    {
        $this->twitterUid = $twitterUid;
        return $this;
    }

    /**
     * Get twitterUid
     *
     * @return string $twitterUid
     */
    public function getTwitterUid()
    {
        return $this->twitterUid;
    }

    /**
     * Set twitterName
     *
     * @param string $twitterName
     * @return self
     */
    public function setTwitterName($twitterName)
    {
        $this->twitterName = $twitterName;
        return $this;
    }

    /**
     * Get twitterName
     *
     * @return string $twitterName
     */
    public function getTwitterName()
    {
        return $this->twitterName;
    }

    /**
     * Set gplusUid
     *
     * @param string $gplusUid
     * @return self
     */
    public function setGplusUid($gplusUid)
    {
        $this->gplusUid = $gplusUid;
        return $this;
    }

    /**
     * Get gplusUid
     *
     * @return string $gplusUid
     */
    public function getGplusUid()
    {
        return $this->gplusUid;
    }

    /**
     * Set gplusName
     *
     * @param string $gplusName
     * @return self
     */
    public function setGplusName($gplusName)
    {
        $this->gplusName = $gplusName;
        return $this;
    }

    /**
     * Get gplusName
     *
     * @return string $gplusName
     */
    public function getGplusName()
    {
        return $this->gplusName;
    }

    /**
     * Set dateOfBirth
     *
     * @param DateTime $dateOfBirth
     * @return self
     */
    public function setDateOfBirth(\DateTime $dateOfBirth)
    {
        $this->dateOfBirth = $dateOfBirth;
        return $this;
    }

    /**
     * Get dateOfBirth
     *
     * @return DateTime $dateOfBirth
     */
    public function getDateOfBirth()
    {
        return $this->dateOfBirth;
    }

    /**
     * Set website
     *
     * @param string $website
     * @return self
     */
    public function setWebsite($website)
    {
        $this->website = $website;
        return $this;
    }

    /**
     * Get website
     *
     * @return string $website
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * Set biography
     *
     * @param string $biography
     * @return self
     */
    public function setBiography($biography)
    {
        $this->biography = $biography;
        return $this;
    }

    /**
     * Get biography
     *
     * @return string $biography
     */
    public function getBiography()
    {
        return $this->biography;
    }

    /**
     * Set gender
     *
     * @param string $gender
     * @return self
     */
    public function setGender($gender)
    {
        $this->gender = $gender;
        return $this;
    }

    /**
     * Get gender
     *
     * @return string $gender
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * Set locale
     *
     * @param string $locale
     * @return self
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
        return $this;
    }

    /**
     * Get locale
     *
     * @return string $locale
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * Set timezone
     *
     * @param string $timezone
     * @return self
     */
    public function setTimezone($timezone)
    {
        $this->timezone = $timezone;
        return $this;
    }

    /**
     * Get timezone
     *
     * @return string $timezone
     */
    public function getTimezone()
    {
        return $this->timezone;
    }

    /**
     * Set realRoles
     *
     * @param collection $realRoles
     * @return self
     */
    public function setRealRoles($realRoles)
    {
        $this->realRoles = $realRoles;
        return $this;
    }

    /**
     * Get realRoles
     *
     * @return collection $realRoles
     */
    public function getRealRoles()
    {
        return $this->realRoles;
    }
}
