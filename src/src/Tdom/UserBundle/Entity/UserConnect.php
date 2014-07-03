<?php

namespace Tdom\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * UserConnect
 *
 * @ORM\Table(name="user_connect")
 * @ORM\Entity(repositoryClass="Tdom\UserBundle\Entity\UserConnectRepository")
 */
class UserConnect
{

    /**
     * @var User
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Tdom\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="source_user_id", referencedColumnName="id")
     */
    private $sourceUser;

    /**
     * @var User
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Tdom\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="target_user_id", referencedColumnName="id")
     */
    private $targetUser;

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
     * @var integer
     * @ORM\Column(name="status", type="integer")
     */
    private $status = 0;


    public function __construct(User $targetUser, User $sourceUser) {
        $this->sourceUser = $sourceUser;
        $this->targetUser = $targetUser;
    }

    /**
     * @param \DateTime $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt) {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt() {
        return $this->createdAt;
    }

    /**
     * @param mixed $sourceUser
     * @return $this;
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
     * @param \Tdom\UserBundle\Entity\user $targetUser
     * @return $this;
     */
    public function setTargetUser($targetUser) {
        $this->targetUser = $targetUser;

        return $this;
    }

    /**
     * @return \Tdom\UserBundle\Entity\user
     */
    public function getTargetUser() {
        return $this->targetUser;
    }

    /**
     * @param \DateTime $updatedAt
     * @return $this;
     */
    public function setUpdatedAt($updatedAt) {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt() {
        return $this->updatedAt;
    }

    /**
     * @param int $status
     */
    public function setStatus($status) {
        $this->status = $status;
    }

    /**
     * @return int
     */
    public function getStatus() {
        return $this->status;
    }
}
