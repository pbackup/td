<?php

namespace Tdom\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use FOS\UserBundle\Model\UserInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Tdom\GameBundle\Model\FindGamer as FindUser;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class UserController extends Controller
{

    /**
     * @Route("/profile/change/password", name="userChangePassword")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws AccessDeniedException
     */
    public function changePasswordAction()
    {
        $user = $this->container->get('security.context')->getToken()->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        $form = $this->container->get('fos_user.change_password.form');
        $formHandler = $this->container->get('fos_user.change_password.form.handler');

        $process = $formHandler->process($user);
        if ($process) {
            $this->setFlash('fos_user_success', 'change_password.flash.success');
            $rendered = $this->renderView("TdomUserBundle:ChangePassword:email.txt.twig", array('user' => $user));
            $this->sendEmail($rendered,$user);
            return new RedirectResponse($this->getRedirectionUrl($user));
        }

        return $this->container->get('templating')->renderResponse(
            'FOSUserBundle:ChangePassword:changePassword.html.'.$this->container->getParameter('fos_user.template.engine'),
            array('form' => $form->createView())
        );
    }

    /**
     * Generate the redirection url when the resetting is completed.
     *
     * @param \FOS\UserBundle\Model\UserInterface $user
     *
     * @return string
     */
    protected function getRedirectionUrl(UserInterface $user)
    {
        return $this->container->get('router')->generate('fos_user_profile_edit');
    }

    /**
     * @param string $action
     * @param string $value
     */
    protected function setFlash($action, $value)
    {
        $this->container->get('session')->getFlashBag()->set($action, $value);
    }

    private function sendEmail($renderedTemplate, $user) {

        $renderedLines = explode("\n", trim($renderedTemplate));
        $subject = $renderedLines[0];
        $body = implode("\n", array_slice($renderedLines, 1));

        $message = \Swift_Message::newInstance()
        ->setSubject($subject)
        ->setFrom(array('noreply@tabledom.com' => "Tabledom"))
        ->setTo($user->getEmail())
        ->setBody($body);

        $message->setContentType("text/html");

        $this->get('mailer')->send($message);
    }

    /**
     * User Add contact action
     *
     * @Route("/profile/add/connect/{userId}/{ajax}", name="userAddContact")
     * @param $userId
     * @param $ajax
     * @throws Exception
     * @return JsonResponse
     */
    public function addContactAction($userId, $ajax = false) {
        $response = new JsonResponse();

        /* @var \Tdom\UserBundle\Manager\UserManager $um */
        $um = $this->get("tdom.user.manager");

        /* @var \Tdom\UserBundle\Entity\User $targetUser */
        $targetUser = $this->getDoctrine()->getRepository('TdomUserBundle:User')->find($userId);

        if(!$targetUser)
            throw new Exception("User not found!!");


        $userConnect = $um->addToContact($targetUser);

        if($ajax) {
            if($userConnect) {

                $responseArr = array ("button" => "Remove from my contacts", "url" => $this->generateUrl(
                        'userRemoveContact', array('userId' => $targetUser->getId())));
                $responseArr['user'] = $this->renderView('TdomUserBundle:User:Partials/user.html.twig', array('user' => $targetUser));
                $response->setData($responseArr);
                return $response;
            }
        }

        return $this->redirect($this->generateUrl('socialiseAction'), 301);
    }

    /**
     * Remove Contact Action
     *
     * @Route("/profile/remove/connect/{userId}/{ajax}", name="userRemoveContact")
     * @param $userId
     * @param $ajax
     * @throws Exception
     * @return JsonRespone
     */
    public function removeContactAction($userId, $ajax = false) {
        $response = new JsonResponse();
        /* @var \Tdom\UserBundle\Entity\User $targetUser */
        $targetUser = $this->getDoctrine()->getRepository('TdomUserBundle:User')->find($userId);

        if(!$targetUser)
            throw new Exception("User not found!!");

        /** @var \Tdom\UserBundle\Entity\UserConnectRepository $userConnectRepo */
        $userConnectRepo = $this->getDoctrine()->getRepository("TdomUserBundle:UserConnect");

        $userConnect = $userConnectRepo->checkExistingUserConnect($targetUser->getId(), $this->getUser()->getId());

        if($userConnect) {
            $this->getDoctrine()->getManager()->remove($userConnect);
            $this->getDoctrine()->getManager()->flush();

            if($ajax) {
                $responseArr = array ("button" => "Add to my contacts", "url" => $this->generateUrl(
                        'userAddContact', array('userId' => $targetUser->getId())));

                $response->setData($responseArr);

            }
            else {
                $this->redirect($this->generateUrl('gamerFind'));
            }
        }

        return $response;
    }

    /**
     * User public profile page
     *
     * @Route("/user/{userId}", name="userProfile")
     * @param $userId
     * @return JsonResponse
     * @throws NotFoundHttpException
     */
    public function userProfileAction($userId) {
        /* @var \Tdom\GameBundle\Manager\GameManager $gm */
        $gm = $this->get("tdom.game.manager");

        /* @var \Tdom\UserBundle\Entity\User $user */
        $user = $this->getDoctrine()->getRepository('TdomUserBundle:User')->find($userId);

        if(!$user)
            throw new NotFoundHttpException("User not found!!");

        /** @var \Tdom\UserBundle\Entity\UserConnectRepository $userConnectRepo */
        $userConnectRepo = $this->getDoctrine()->getRepository("TdomUserBundle:UserConnect");

        if ($this->getUser())
            $userConnect = $userConnectRepo->checkExistingUserConnect($user->getId(), $this->getUser()->getId());

        $categoriesAndGames = $gm->findUserGames($user);
        if ($this->getRequest()->get('json')) {
            $response = new JsonResponse();

            $this->data['success'] = true;
            $this->data['content'] = $this->renderView('TdomUserBundle:User:userProfile.html.twig',
                array(
                    'categories' => $categoriesAndGames,
                    'isPublic' => true,
                    'json' => true,
                    'userConnect' => (isset($userConnect))? $userConnect : "",
                    'user' => $user
                ));
            $this->data['nickname'] = $user->getNickName();
            $response->setData($this->data);
            return $response;
        }

        return $this->render('TdomUserBundle:User:userProfile.html.twig',
            array(
                'categories' => $categoriesAndGames,
                'isPublic' => true,
                'userConnect' => (isset($userConnect))? $userConnect : "",
                'user' => $user
            ));
    }

    /**
     * Find user contacts
     *
     * @Route("/profile/find/connect/{q}", name="findUserContacts")
     * @param string $q
     * @return JsonResponse
     */
    public function findUsersAction($q = "") {
        $response = new JsonResponse();
        $responseArr = array();
        /** @var \Tdom\UserBundle\Manager\UserManager $um */
        $um = $this->get('tdom.user.manager');
        $connectUsers = $um->findUsers($q);
        $responseArr['data'] = $this->renderView('TdomUserBundle:User:Partials/list.html.twig' , array('connectUsers' => $connectUsers, 'ajax' => true));
        $response->setData($responseArr);
        return $response;
    }
}
