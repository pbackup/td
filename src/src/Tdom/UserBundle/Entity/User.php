<?php

namespace Tdom\UserBundle\Entity;

use FOS\UserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Intl\Intl;
use Tdom\UserBundle\Validator\InvalidAge;

/**
 * User
 *
 * @ORM\Table()
 * @ORM\HasLifecycleCallbacks
 * @ORM\Entity(repositoryClass="Tdom\UserBundle\Entity\UserRepository")
 * @InvalidAge
 */
class User extends BaseUser
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     * @Assert\NotBlank()
     * @ORM\Column(name="nick_name", type="string", length=255)
     */
    private $nickName;

    /**
     * @var string
     * @Assert\NotBlank()
     * @ORM\Column(name="full_name", type="string", length=255)
     */
    private $fullName;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="birth_day", type="date", length=255)
     */
    private $birthDay;

    /**
     * @var string
     *
     * @ORM\Column(name="country", type="string", length=255, nullable=true)
     */
    private $country;

    /**
     * @var string
     *
     * @ORM\Column(name="city", type="string", length=255, nullable=true)
     */
    private $city;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", length=255, nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="avatar", type="string", length=255, nullable=true)
     */
    private $avatar;

    /**
     * @var string
     * @ORM\Column(name="location", type="string", length=255, nullable=true)
     */
    private $location;

    /**
     * @var User
     *
     * @ORM\ManyToMany(targetEntity="Tdom\AdminBundle\Entity\Game", inversedBy="users")
     */
    private $games;

    /**
     * @Assert\File(maxSize="6000000")
     */
    private $file;

    private $temp;

    private $totalUnreadMessage;

    /**
     * @ORM\OneToMany(targetEntity="Tdom\MessageBundle\Entity\UserMessage", mappedBy="sourceUser")
     */
    private $userMessages;


    public function __construct() {
        parent::__construct();
        $this->games = new ArrayCollection();
        $this->username = uniqid();
        $this->birthDay = new \DateTime();
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
     * @param  $avatar
     * @return $this
     */
    public function setAvatar($avatar) {
        $this->avatar = $avatar;
        return $this;
    }

    /**
     * @return string
     */
    public function getAvatar() {
        return $this->avatar;
    }

    /**
     * @param \DateTime $birthDay
     * @return $this
     */
    public function setBirthDay($birthDay) {
        $this->birthDay = $birthDay;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getBirthDay() {
        return $this->birthDay;
    }

    /**
     * @param string $city
     * @return $this
     */
    public function setCity($city) {
        $this->city = $city;

        return $this;
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param $country
     * @return $this
     */
    public function setCountry($country) {
        $this->country = $country;

        return $this;
    }

    /**
     * @return string
     */
    public function getCountry() {
        return $this->country;
    }

    /**
     * @param string $description
     * @return $this
     */
    public function setDescription($description) {
        $this->description = $description;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * @param string $fullName
     * @return $this
     */
    public function setFullName($fullName) {
        $this->fullName = $fullName;

        return $this;
    }

    /**
     * @return string
     */
    public function getFullName() {
        return $this->fullName;
    }

    /**
     *
     * @param string $nickName
     * @return $this
     */
    public function setNickName($nickName) {
        $this->nickName = $nickName;

        return $this;
    }

    /**
     * @return string
     */
    public function getNickName() {
        return $this->nickName;
    }

    /**
     * @param \Tdom\AdminBundle\Entity\Game $game
     */
    public function addGames($game) {
        $this->games[] = $game;
    }

    /**
     * Remove game
     *
     * @param \Tdom\AdminBundle\Entity\Game $game
     */
    public function removeGame($game) {
        $this->games->removeElement($game);
    }

    /**
     * @return \Tdom\UserBundle\Entity\User
     */
    public function getGames() {
        return $this->games;
    }

    /**
     * @param string $location
     * @return $this
     */
    public function setLocation($location) {
        $this->location = $location;

        return $this;
    }

    /**
     * @return string
     */
    public function getLocation() {
        return $this->location;
    }

    /**
     * Sets file.
     *
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file = null) {
        $this->file = $file;
        // check if we have an old image path
        if (isset($this->avatar)) {
            // store the old name to delete after the update
            $this->temp = $this->avatar;
            $this->avatar = null;
        } else {
            $this->avatar = 'initial';
        }
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload() {
        if (null !== $this->getFile()) {
            // do whatever you want to generate a unique name
            $filename = sha1(uniqid(mt_rand(), true));
            $this->avatar = $filename.'.'.$this->getFile()->guessExtension();
        }
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload() {
        if (null === $this->getFile()) {
            return;
        }

        // if there is an error when moving the file, an exception will
        // be automatically thrown by move(). This will properly prevent
        // the entity from being persisted to the database on error
        $this->getFile()->move($this->getUploadRootDir(), $this->avatar);

        // check if we have an old image
        if (isset($this->temp)) {
            // delete the old image
            unlink($this->getUploadRootDir().'/'.$this->temp);
            // clear the temp image path
            $this->temp = null;
        }
        $this->file = null;
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload() {
        if ($file = $this->getAbsolutePath()) {
            unlink($file);
        }
    }

    public function getAbsolutePath() {
        return null === $this->avatar
            ? null
            : $this->getUploadRootDir().'/'.$this->avatar;
    }

    public function getWebPath() {
        return null === $this->avatar
            ? null
            : $this->getUploadDir().'/'.$this->avatar;
    }

    protected function getUploadRootDir() {
        // the absolute directory path where uploaded
        // documents should be saved
        return __DIR__.'/../../../../web/'.$this->getUploadDir();
    }

    protected function getUploadDir() {

        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        return 'uploads';
    }

    /**
     * Get file.
     *
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }

    public function unsetPassword(){
        unset($this->password);
        $this->plainPassword = null;
    }

    public function getAge() {
        $now      = new \DateTime();
        $birthday = $this->getBirthDay();
        $interval = $now->diff($birthday);
        return $interval->format('%y');
    }

    public function getCountryFullName() {
        return Intl::getRegionBundle()->getCountryName($this->getCountry());
    }

    /**
     * @param mixed $userMessages
     */
    public function addUserMessages($userMessages) {
        $this->userMessages[] = $userMessages;
    }

    /**
     * @return mixed
     */
    public function getUserMessages() {
        return $this->userMessages;
    }

    public function __toString() {
        return $this->getNickName();
    }

    /**
     * @param mixed $totalUnreadMessage
     */
    public function setTotalUnreadMessage($totalUnreadMessage) {
        $this->totalUnreadMessage = $totalUnreadMessage;
    }

    /**
     * @return mixed
     */
    public function getTotalUnreadMessage() {
        return $this->totalUnreadMessage;
    }
}
