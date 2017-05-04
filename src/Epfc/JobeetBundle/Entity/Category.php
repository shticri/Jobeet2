<?php

namespace Epfc\JobeetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Epfc\JobeetBundle\Utils\Jobeet as Jobeet;

/**
 * Category
 *
 * @ORM\Table(name="category")
 * @ORM\Entity(repositoryClass="Epfc\JobeetBundle\Repository\CategoryRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Category
{
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
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=255, unique=true)
     */
    private $slug;
    
    /**
     * @ORM\OneToMany(targetEntity="Job", mappedBy="category")
     */
    private $jobs;

    /**
     * Many Groups have Many Users.
     * @ORM\ManyToMany(targetEntity="Affiliate", mappedBy="category")
     */
    private $affiliates;

    private $active_jobs;
    
    private $more_jobs;
    
    
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
        return $this->getName();
    }
    
    public function __construct()
    {
        $this->jobs = new ArrayCollection();
        $this->affiliates = new ArrayCollection();
    }
    
    
    function setJobs($jobs) {
        $this->jobs = $jobs;
    }

    function getJobs() {
        return $this->jobs;
    }

    function setAffiliates($affiliates) {
        $this->affiliates = $affiliates;
    }

    function getAffiliates() {
        return $this->affiliates;
    }

    public function setActiveJobs($jobs)
    {
      $this->active_jobs = $jobs;
    }

    public function getActiveJobs()
    {
      return $this->active_jobs;
    }
    
    public function setMoreJobs($jobs)
    {
      $this->more_jobs = $jobs >=  0 ? $jobs : 0;
    }

    public function getMoreJobs()
    {
      return $this->more_jobs;
    }
    
    /**
     * Set slug
     *
     * @param string $slug
     *
     * @return string $slug
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }
    
    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
      return Jobeet::slugify($this->getName());
    }
    
    /**
     *
     * @ORM\PrePersist
     */
    public function setSlugValue()
    {
        $this->slug = Jobeet::slugify($this->getName());
    }
    
}

