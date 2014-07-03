<?php

namespace Tdom\MessageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Tdom\MessageBundle\Entity\Message;
use Tdom\MessageBundle\Form\MessageType;
use Symfony\Component\HttpFoundation\JsonResponse;

class SocialiseController extends Controller
{
    /**
     * Socialise action
     *
     * @Route("socialize/{userId}", name="socialiseAction")
     * @param int $userId
     * @return array
     * @Template()
     */
    public function indexAction($userId = 0) {
        /** @var \Tdom\UserBundle\Manager\UserManager $um */
        $um = $this->get('tdom.user.manager');
        $defaultUser = $this->getDoctrine()->getRepository('TdomUserBundle:User')->findOneBy(array("email" => "info@tabledom.com"));
        if ($defaultUser)
            $um->addToContact($defaultUser);

        //User find user based on user contact list
        $contactedUsers = $um->findUserConnects();

        //Get target User
        $targetUser = $this->getTargetUser($userId);

        $message = new Message();
        $form = $this->createForm(new MessageType(), $message);

        //Get User messages based on target user
        $userMessages = $this->getUserMessages($targetUser);

        return array(
                "form" => $form->createView(),
                'connectUsers' => $contactedUsers,
                'userMessages'=> $userMessages,
                'targetUser' => ($targetUser)? $targetUser : $defaultUser);
    }

    /**
     * Message send action
     *
     * @Route("socialize/message/send/{userId}", name="messageSend")
     * @param Request $request
     * @param $userId
     * @return JsonResponse
     */
    public function sendMessageAction(Request $request, $userId) {
        $response = new JsonResponse();
        /** @var \Tdom\MessageBundle\Manager\MessageManager $mm */
        $mm = $this->get('tdom.message.manager');
        /* @var \Tdom\UserBundle\Manager\UserManager $um */
        $um = $this->get("tdom.user.manager");

        /* @var \Tdom\UserBundle\Entity\User $targetUser */
        $targetUser = $this->getDoctrine()->getRepository('TdomUserBundle:User')->find($userId);

        $message = new Message();
        $form = $this->createForm(new MessageType(), $message);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $userMessage = $mm->saveMessage($message, $targetUser);
            if ($userMessage) {
                $responseArr = $this->findMessages($targetUser);
                $response->setData($responseArr);

                //Add to contact if user doesn't have contacted list
                $um->addToContact($targetUser);
            }
        }

        return $response;
    }

    /**
     * Message send action
     *
     * @Route("socialize/message/sendall/{userId}", name="messageSendToAll")
     * @param $userId
     * @param Request $request
     * @return JsonResponse
     * @throws NotFoundHttpException
     */
    public function sendAllAction(Request $request, $userId = 0) {
        $response = new JsonResponse();
        if(!$this->container->get('security.context')->isGranted('ROLE_SUPER_ADMIN')){
            $response->setData(array("error" => "Access denied!!"));
            return $response;
        }

        /** @var \Tdom\MessageBundle\Manager\MessageManager $mm */
        $mm = $this->get('tdom.message.manager');

        /* @var \Tdom\UserBundle\Entity\User $targetUser */
        $targetUser = $this->getDoctrine()->getRepository('TdomUserBundle:User')->find($userId);

        $message = new Message();
        $form = $this->createForm(new MessageType(), $message);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $sendAll = $mm->sendMessageToAll($message);
            if ($sendAll && $targetUser) {
                $responseArr = $this->findMessages($targetUser);
                $response->setData($responseArr);
            }
        }

        return $response;
    }

    /**
     * Refresh action
     *
     * @Route("socialize/message/refresh/{userId}", name="refreshMessage")
     * @param $userId
     * @return JsonResponse
     */
    public function refreshMessageAction($userId) {
        $response = new JsonResponse();
        /* @var \Tdom\UserBundle\Entity\User $targetUser */
        $targetUser = $this->getDoctrine()->getRepository('TdomUserBundle:User')->find($userId);

        //Get User messages based on target user
        $responseArr = $this->findMessages($targetUser);

        if ($responseArr) {
            $response->setData($responseArr);
        }

        return $response;
    }

    /**
     * Find all messages based on target User
     *
     * @param $targetUser
     * @return array|bool
     */
    private function findMessages($targetUser) {
        /** @var \Tdom\UserBundle\Manager\UserManager $um */
        $um = $this->get('tdom.user.manager');

        $responseArr = array();
        try {

            // Find user connects
            $connectUsers = $um->findUserConnects();

            // Get User messages based on target user
            $userMessages = $this->getUserMessages($targetUser);

            $responseArr['users'] = $this->renderView('TdomMessageBundle:Socialise:Partials/user_connect.html.twig' , array('connectUsers' => $connectUsers, 'ajax' => true));
            $responseArr['messages'] = $this->renderView('TdomMessageBundle:Socialise:Partials/message_item.html.twig' ,
                                        array('userMessages' => $userMessages));
            $responseArr['success'] = true;
        }
        catch (Exception $e) {
            return false;
        }

        return $responseArr;
    }

    /**
     * GET User message
     *
     * @param $targetUser
     * @return array|null
     */
    private function getUserMessages($targetUser) {
        /** @var \Tdom\MessageBundle\Manager\MessageManager $mm */
        $mm = $this->get('tdom.message.manager');

        if ($targetUser)
            return $mm->findUserMessages($targetUser);

        return null;
    }

    /**
     *
     * @param $userId
     * @return \Tdom\UserBundle\Entity\User
     */
    private function getTargetUser($userId) {

        /** @var \Tdom\UserBundle\Entity\User $targetUser */
        $targetUser = $this->getDoctrine()->getRepository('TdomUserBundle:User')->find($userId);

        return $targetUser;
    }

}
