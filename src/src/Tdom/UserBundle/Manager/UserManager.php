<?php

namespace Tdom\UserBundle\Manager;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Security\Core\SecurityContext;
use Tdom\UserBundle\Entity\User;
use Tdom\UserBundle\Entity\UserConnect;

class UserManager {

    /** @var \Doctrine\ORM\EntityManager $em */
    protected $em;

    /** @var \Symfony\Component\Security\Core\SecurityContext $sc */
    protected $sc;

    protected $logger;

    /**
     * Injected entity manager and security context
     *
     * @param \Doctrine\ORM\EntityManager $em
     * @param \Symfony\Component\Security\Core\SecurityContext $sc
     * @param $logger
     */
    function __construct(EntityManager $em, SecurityContext $sc, $logger) {
        $this->em = $em;
        $this->sc = $sc;
        $this->logger = $logger;
    }

    /**
     * Add to contact
     *
     * @param User $targetUser
     * @return bool
     */
    public function addToContact(User $targetUser) {

        try {
            /** @var \Tdom\UserBundle\Entity\UserConnectRepository $userConnectRepo  */
            $userConnectRepo = $this->em->getRepository("TdomUserBundle:UserConnect");

            /** @var \Tdom\UserBundle\Entity\User $sourceUser */
            $sourceUser = $this->sc->getToken()->getUser();
            $userConnect = $userConnectRepo->checkExistingUserConnect($sourceUser->getId(), $targetUser->getId());
            if (!$userConnect) {
                $userConnect = new UserConnect($targetUser, $sourceUser);
                $this->em->persist($userConnect);
                $this->em->flush();
                return $userConnect;
            }
        }
        catch (Exception $e) {
            $this->logger->error($e->getMessage());
        }

        return false;
    }

    /**
     * Remove From User contact
     *
     * @param UserConnect $userConnect
     * @return bool
     */
    public function removefromContact(UserConnect $userConnect) {
        try {
            $this->em->remove($userConnect);
            $this->em->flush();
        }
        catch (Exception $e) {
            $this->logger->error($e->getMessage());
        }

        return false;
    }

    /**
     * Find User connects
     *
     * @param string $filterText
     * @return array|bool
     */
    public function findUserConnects($filterText = "") {
        try {
            /** @var \Tdom\UserBundle\Entity\UserRepository $userRepo */
            $userRepo = $this->em->getRepository("TdomUserBundle:User");

            /** @var \Tdom/UserBundle\Entity\User $user */
            $user = $this->sc->getToken()->getUser();

            $contactedUsers = $userRepo->findUsersBasedOnUserConnect($user->getId(), $filterText);

            return  $this->addTotalUnreadMessage($contactedUsers);

        }
        catch (Exception $e) {

        }

        return false;
    }

    /**
     * Find Users
     *
     * @param string $filterText
     * @return array|bool
     */
    public function findUsers($filterText = "") {
        try {
            /** @var \Tdom\UserBundle\Entity\UserRepository $userRepo */
            $userRepo = $this->em->getRepository("TdomUserBundle:User");

            $contactedUsers = $this->findUserConnects();
            $exceptUsers = array();
            foreach ($contactedUsers as $usr) {
                $exceptUsers[] = $usr->getId();
            }

            /** @var \Tdom/UserBundle\Entity\User $user */
            $user = $this->sc->getToken()->getUser();
            $exceptUsers[] = $user->getId();
            return $userRepo->findUsers($exceptUsers, $filterText);
        }
        catch (Exception $e) {

        }

        return false;
    }

    /**
     * Add Total Unread Messages
     *
     * @param array $contactedUsers
     * @return array
     */
    private function addTotalUnreadMessage(array $contactedUsers ) {
        $sourceUser = $this->sc->getToken()->getUser();
        /** @var  \Tdom\MessageBundle\Entity\MessageRepository $messageRepo */
        $messageRepo = $this->em->getRepository("TdomMessageBundle:Message");

        /**@var \Tdom\UserBundle\Entity\User $user */
        foreach ($contactedUsers as $user) {
            $totalUnreadMessage = $messageRepo->findTotalUnreadUserMessage($sourceUser->getId(), $user->getId());
            $user->setTotalUnreadMessage($totalUnreadMessage);
        }

        return $contactedUsers;
    }
}