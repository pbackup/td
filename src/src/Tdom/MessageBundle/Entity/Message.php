<?php

namespace Tdom\MessageBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Message
 *
 * @ORM\Table(name="message")
 * @ORM\Entity(repositoryClass="Tdom\MessageBundle\Entity\MessageRepository")
 */
class Message
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
     *
     * @ORM\Column(name="subject", type="string", length=255)
     */
    private $subject;

    /**
     * @var string
     *
     * @ORM\Column(name="body", type="text")
     */
    private $body;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isRead", type="boolean")
     */
    private $isRead = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isDeleted", type="boolean")
     */
    private $isDeleted = false;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="upatedAt", type="datetime")
     * @Gedmo\Timestampable(on="update")
     */
    private $upatedAt;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="Tdom\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @var UserMessage
     *
     * @ORM\OneToMany(targetEntity="UserMessage", mappedBy="message")
     */
    private $userMessages;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set subject
     *
     * @param string $subject
     * @return Message
     */
    public function setSubject($subject) {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Get subject
     *
     * @return string 
     */
    public function getSubject() {
        return $this->subject;
    }

    /**
     * Set body
     *
     * @param string $body
     * @return Message
     */
    public function setBody($body) {
        $this->body = $body;

        return $this;
    }

    /**
     * Get body
     *
     * @return string 
     */
    public function getBody() {
        return $this->body;
    }

    /**
     * Set isRead
     *
     * @param boolean $isRead
     * @return Message
     */
    public function setIsRead($isRead) {
        $this->isRead = $isRead;

        return $this;
    }

    /**
     * Get isRead
     *
     * @return boolean 
     */
    public function getIsRead() {
        return $this->isRead;
    }

    /**
     * Set isDeleted
     *
     * @param boolean $isDeleted
     * @return Message
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
     * @return Message
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
     * Set upatedAt
     *
     * @param \DateTime $upatedAt
     * @return Message
     */
    public function setUpatedAt($upatedAt) {
        $this->upatedAt = $upatedAt;

        return $this;
    }

    /**
     * Get upatedAt
     *
     * @return \DateTime 
     */
    public function getUpatedAt() {
        return $this->upatedAt;
    }

    /**
     * @param \Tdom\MessageBundle\Entity\User $user
     * @return $this
     */
    public function setUser($user) {
        $this->user = $user;

        return $this;
    }

    /**
     * @return \Tdom\MessageBundle\Entity\User
     */
    public function getUser() {
        return $this->user;
    }

    /**
     * @param mixed $userMessages
     */
    public function addUserMessages($userMessages)
    {
        $this->userMessages[] = $userMessages;
    }

    /**
     * @return mixed
     */
    public function getUserMessages()
    {
        return $this->userMessages;
    }


}
