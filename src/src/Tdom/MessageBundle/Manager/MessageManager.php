<?php

namespace Tdom\MessageBundle\Manager;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Security\Core\SecurityContext;
use Tdom\MessageBundle\Entity\Message;
use Tdom\MessageBundle\Entity\UserMessage;
use Tdom\UserBundle\Entity\User;
use Tdom\UserBundle\Entity\UserConnect;

class MessageManager {

    private $em;
    private $sc;
    private $logger;
    private $mailer;
    private $userManager;

    /**
     * Injected entity manager and security context
     *
     * @param \Doctrine\ORM\EntityManager $em
     * @param \Symfony\Component\Security\Core\SecurityContext $sc
     * @param $logger
     * @param $mailer
     * @param $userManager
     */
    function __construct(EntityManager $em, SecurityContext $sc, $logger, $mailer, $userManager) {
        $this->em = $em;
        $this->sc = $sc;
        $this->logger = $logger;
        $this->mailer = $mailer;
        $this->userManager = $userManager;
    }

    /**
     * Save message
     *
     * @param Message $message
     * @param User $targetUser
     * @return UserMessage
     */
    function saveMessage(Message $message, User $targetUser) {
       $user = $this->sc->getToken()->getUser();

        //Message
       $message->setUser($user);
       $message->setSubject("User Message");
       $this->em->persist($message);

        //User Message
       $userMessage = new UserMessage();
       $userMessage->setUser($user);
       $userMessage->setSourceUser($user);
       $userMessage->setTargetUser($targetUser);
       $userMessage->setMessage($message);
       $this->em->persist($userMessage);

       $this->em->flush();

       //$this->sendEmail($targetUser->getEmail());

       return $userMessage;
    }

    /**
     * Send message to all
     *
     * @param Message $message
     * @return bool
     */
    public function sendMessageToAll(Message $message) {

       try  {
            /** @var \Tdom\UserBundle\Entity\UserRepository $userRepo */
            $userRepo = $this->em->getRepository('TdomUserBundle:User');

            $exceptUsers[] = $this->sc->getToken()->getUser()->getId();
            $users = $userRepo->findUsers($exceptUsers);

            /** @var  \Tdom\UserBundle\Entity\User $user*/
            foreach ($users as $user) {
                $userMessage = $this->saveMessage($message, $user);
                $this->userManager->addToContact($user);
                if (!$userMessage->getId()) {
                    $this->logger->info('Message has not been sent for user ('.$user->getNickName().')');
                }
            }

            return true;
        }
        catch (Exception $e) {
            $this->logger->errpr($e->getMessage());
        }

        return false;
    }

    /**
     * Find User message
     *
     * @param User $targetUser
     * @return array
     */
    public function findUserMessages(User $targetUser) {
        $user = $this->sc->getToken()->getUser();
        /** @var \Tdom\MessageBundle\Entity\UserMessageRepository $userMessageRepo */
        $userMessageRepo = $this->em->getRepository('TdomMessageBundle:UserMessage');
        $userMessages = $userMessageRepo->findUserMessages($user->getId(), $targetUser->getId());

        //Make Unread messages to read
        $this->em->getRepository('TdomMessageBundle:Message')->makeUnreadToRead($user->getId(), $targetUser->getId());

        return $userMessages;
    }

    /**
     * Sending user message notification by user email
     *
     * @param $targetUser
     */
    private function sendEmail(User $targetUser) {
        $user = $this->sc->getToken()->getUser();
        $sourceNickName = $user->getNickName();
        $nickName = $targetUser->getNickName();
        $msg = "
        <pre>
          Hi $nickName,
          You have got an message from $sourceNickName.

          Regards
          Tabledom Administration
        </pre>
        ";
        $message = \Swift_Message::newInstance()
            ->setSubject('~')
            ->setFrom(array("admin@tabledom.com" => "Tabledom - ". $sourceNickName))
            ->setTo($targetUser->getEmail())
            ->setBody($msg, 'text/html');
        $this->mailer->send($message);
    }
}