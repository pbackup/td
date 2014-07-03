<?php

namespace Tdom\MessageBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * UserMessage
 *
 * @ORM\Table(name="user_message")
 * @ORM\Entity(repositoryClass="Tdom\MessageBundle\Entity\UserMessageRepository")
 */
class UserMessage
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
     * @ORM\ManyToOne(targetEntity="Tdom\UserBundle\Entity\User", inversedBy="userMessages")
     * @ORM\JoinColumn(name="source_user_id", referencedColumnName="id")
     */
    private $sourceUser;

    /**
     * @var user
     *
     * @ORM\ManyToOne(targetEntity="Tdom\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="target_user_id", referencedColumnName="id")
     */
    private $targetUser;

    /**
     * @var user
     *
     * @ORM\ManyToOne(targetEntity="Tdom\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @var message
     *
     * @ORM\ManyToOne(targetEntity="Message", inversedBy="userMessages")
     * @ORM\JoinColumn(name="message_id", referencedColumnName="id")
     */
    private $message;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @param mixed $message
     */
    public function setMessage($message) {
        $this->message = $message;
    }

    /**
     * @return mixed
     */
    public function getMessage() {
        return $this->message;
    }

    /**
     * @param mixed $sourceUser
     * @return $this
     */
    public function setSourceUser($sourceUser) {
        $this->sourceUser = $sourceUser;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSourceUser() {
        return $this->sourceUser;
    }

    /**
     * @param mixed $targetUser
     * @return $this
     */
    public function setTargetUser($targetUser) {
        $this->targetUser = $targetUser;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTargetUser() {
        return $this->targetUser;
    }

    /**
     * @param mixed $user
     * @return $this
     */
    public function setUser($user) {
        $this->user = $user;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getUser() {
        return $this->user;
    }


}
