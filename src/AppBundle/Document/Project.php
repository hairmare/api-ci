<?php

namespace AppBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\Document
 */
class Project
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
     * @MongoDB\Field(type="string")
     */
    protected $name;

    /**
     * @MongoDB\ReferenceOne(targetDocument="Application\Sonata\UserBundle\Document\User")
     */
    protected $owner;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $githubName;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $masterVersion;

    /**
     * @MongoDB\ReferenceMany(targetDocument="DocumentationFile")
     */
    protected $docFiles;

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
     * Set githubName
     *
     * @param string $githubName
     * @return self
     */
    public function setGithubName($githubName)
    {
        $this->githubName = $githubName;
        return $this;
    }

    /**
     * Get githubName
     *
     * @return string $githubName
     */
    public function getGithubName()
    {
        return $this->githubName;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get name
     *
     * @return string $name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set owner
     *
     * @param Application\Sonata\UserBundle\Document\User $owner
     * @return self
     */
    public function setOwner(\Application\Sonata\UserBundle\Document\User $owner)
    {
        $this->owner = $owner;
        return $this;
    }

    /**
     * Get owner
     *
     * @return Application\Sonata\UserBundle\Document\User $owner
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * Set masterVersion
     *
     * @param string $masterVersion
     * @return self
     */
    public function setMasterVersion($masterVersion)
    {
        $this->masterVersion = $masterVersion;
        return $this;
    }

    /**
     * Get masterVersion
     *
     * @return string $masterVersion
     */
    public function getMasterVersion()
    {
        return $this->masterVersion;
    }
    public function __construct()
    {
        $this->docFiles = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Add docFile
     *
     * @param AppBundle\Document\DocumentationFile $docFile
     */
    public function addDocFile(\AppBundle\Document\DocumentationFile $docFile)
    {
        $this->docFiles[] = $docFile;
    }

    /**
     * Remove docFile
     *
     * @param AppBundle\Document\DocumentationFile $docFile
     */
    public function removeDocFile(\AppBundle\Document\DocumentationFile $docFile)
    {
        $this->docFiles->removeElement($docFile);
    }

    /**
     * Get docFiles
     *
     * @return Doctrine\Common\Collections\Collection $docFiles
     */
    public function getDocFiles()
    {
        return $this->docFiles;
    }
}