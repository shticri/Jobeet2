<?php

namespace Epfc\JobeetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Category
 *
 * @ORM\Table(name="category")
 * @ORM\Entity(repositoryClass="Epfc\JobeetBundle\Repository\CategoryRepository")
 */
class Category
{
    
    /**
     * Many Groups have Many Users.
     * @ORM\ManyToMany(targetEntity="Affiliate", mappedBy="category")
     */
    private $affiliates;
    
    /**
     * @ORM\OneToMany(targetEntity="Job", mappedBy="category")
     */
    private $jobs;

    public function __construct()
    {
        $this->jobs = new ArrayCollection();
        $this->affiliates = new ArrayCollection();
    }
    function getAffiliates() {
        return $this->affiliates;
    }

    function getJobs() {
        return $this->jobs;
    }

    function setAffiliates($affiliates) {
        $this->affiliates = $affiliates;
        return $this;
    }

    function setJobs($jobs) {
        $this->jobs = $jobs;
        return $this;
    }

        /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     */
    private $name;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Category
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    
     public function __toString() {
        return $this->name.".";
    }
}

