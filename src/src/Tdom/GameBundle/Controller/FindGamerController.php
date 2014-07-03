<?php

namespace Tdom\GameBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use Tdom\GameBundle\Model\FindGamer as FindUser;
use Tdom\GameBundle\Form\FilterFormType;


class FindGamerController extends Controller
{
    /**
     * Find Action, This action mainly working on find user based on games and category into the google map
     *
     * @Route("/find", name="gamerFind")
     * @Template()
     */
    public function findAction() {
        $findUser = new FindUser();
        $form = $this->getFilterForm($findUser);
        return array('form' => $form->createView());
    }

    /**
     * Process action for filter user based on submit action
     *
     * @Route("/find/process", name="findProcessAction")
     * @param Request $request
     * @method(post)
     * @return JsonResponse
     */
    public function processAction(Request $request) {
        $requestData = $request->get('tdom_filter_game');
        $session = $request->getSession();
        $response = new JsonResponse();
        $findUser = new FindUser();
        $form = $this->getFilterForm($findUser, $requestData['category']);
        $form->handleRequest($request);
        $this->data['success'] = false;

        if($form->isValid()) {
            /** @var \Tdom\GameBundle\Manager\GameManager $gm */
            $gm = $this->get("tdom.game.manager");
            $this->data['success'] = true;
            $result = $gm->findUserByGames($findUser, $this->get('router'));
            $session->set('mapData', $result);
            $session->set('category', $findUser->getCategory());
            $session->set('games', $findUser->getGames());
            $this->data['result'] = $result;
        }
        else {
            $this->data['message'] = "Invalid data posting!";
        }
        $response->setData($this->data);
        return $response;
    }

    /**
     * Change category action is used to update form while change category from list
     *
     * @Route("/find/form/{categoryId}", name="changeCategoryAction")
     * @param $categoryId
     * @return JsonResponse
     */
    public function changeCategoryAction($categoryId = 0) {
        $response = new JsonResponse();
        $category = $this->getDoctrine()->getRepository('TdomAdminBundle:Category')->find($categoryId);
        $this->data['success'] = false;
        $findUser = new FindUser();
        if ($category) {
            $findUser->setCategory($category);
        }

        $form = $this->getFilterForm($findUser, $categoryId);
        $this->data['form'] = $this->renderView('TdomGameBundle:FindGamer:Partials/filter_form.html.twig', array("form" => $form->createView()));
        /** @var \Tdom\GameBundle\Manager\GameManager $gm */
        $gm = $this->get("tdom.game.manager");
        $this->data['success'] = true;
        $this->data['users'] = $gm->findUserByGames($findUser, $this->get('router'));
        $response->setData($this->data);
        return $response;
    }

    /**
     * Get filter form
     *
     * @param FindUser $findUser
     * @param integer $categoryId
     * @return \Symfony\Component\Form\Form
     */
    private function getFilterForm(FindUser $findUser = null, $categoryId = 0) {
        $options = array('category' => $categoryId);
        if(!$categoryId) {
            /** @var \Tdom\GameBundle\Manager\GameManager $gm */
            $gm = $this->get("tdom.game.manager");
            $options['category'] = 0; // $gm->findFirstCategoryId();
        }
        return $this->createForm(new FilterFormType(), $findUser, $options);
    }



}
