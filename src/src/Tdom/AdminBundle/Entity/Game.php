<?php

namespace Tdom\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Tdom\AdminBundle\Validator\UniqueValue;

/**
 * Game
 *
 * @ORM\Table(name="game")
 * @ORM\Entity(repositoryClass="Tdom\AdminBundle\Entity\GameRepository")
 * @UniqueValue
 */
class Game
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
     * @var string
     * @Assert\NotBlank()
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_active", type="boolean", nullable=true)
     */
    private $isActive;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_system", type="boolean", nullable=true)
     */
    private $isSystem;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_deleted", type="boolean", nullable=true)
     */
    private $isDeleted = false;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime")
     * @Gedmo\Timestampable(on="update")
     */
    private $updatedAt;

    /**
     * @var Category
     *
     * @Assert\NotBlank()
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="games")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     */
    private $category;

    /**
     * @var User
     *
     * @ORM\ManyToMany(targetEntity="Tdom\UserBundle\Entity\User", mappedBy="games")
     */
    private $users;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="Tdom\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $userBy;

    public function __construct() {
       $this->users =  new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return game
     */
    public function setName($name) {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName() {

        if (strlen($this->name) > 19) {
            return substr($this->name,0,19)."...";
        }

        return $this->name;
    }


    public function getFullName() {
        return $this->name;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     * @return game
     */
    public function setIsActive($isActive) {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean 
     */
    public function getIsActive() {
        return $this->isActive;
    }

    /**
     * Set isSystem
     *
     * @param boolean $isSystem
     * @return game
     */
    public function setIsSystem($isSystem) {
        $this->isSystem = $isSystem;

        return $this;
    }

    /**
     * Get isSystem
     *
     * @return boolean 
     */
    public function getIsSystem() {
        return $this->isSystem;
    }

    /**
     * Set isDeleted
     *
     * @param boolean $isDeleted
     * @return game
     */
    public function setIsDeleted($isDeleted) {
        $this->isDeleted = $isDeleted;

        return $this;
    }

    /**
     * Get isDeleted
     *
     * @return boolean 
     */
    public function getIsDeleted() {
        return $this->isDeleted;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return game
     */
    public function setCreatedAt($createdAt) {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt() {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return game
     */
    public function setUpdatedAt($updatedAt) {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime 
     */
    public function getUpdatedAt() {
        return $this->updatedAt;
    }

    /**
     * Set user
     *
     * @param \stdClass $user
     * @return game
     */
    public function addUser($user) {
        $this->users[] = $user;

        return $this;
    }

    /**
     * Get users
     *
     * @return \Doctrine\Common\Collections\ArrayCollection();
     */
    public function getUsers() {
        return $this->users;
    }

    /**
     * @param \Tdom\AdminBundle\Entity\Category $category
     */
    public function setCategory($category) {
        $this->category = $category;
    }

    /**
     * @return \Tdom\AdminBundle\Entity\Category
     */
    public function getCategory() {
        return $this->category;
    }

    /**
     * @param \Tdom\AdminBundle\Entity\User $userBy
     */
    public function setUserBy($userBy) {
        $this->userBy = $userBy;
    }

    /**
     * @return \Tdom\AdminBundle\Entity\User
     */
    public function getUserBy() {
        return $this->userBy;
    }

    public function __toString() {
        return $this->getFullName();
    }
}
