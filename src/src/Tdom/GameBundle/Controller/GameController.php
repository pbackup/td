<?php

namespace Tdom\GameBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tdom\AdminBundle\Entity\Game;
use Tdom\GameBundle\Form\GameFormType;
use Symfony\Component\HttpFoundation\JsonResponse;


class GameController extends Controller
{
    /**
     * My games action is linked from user profile menu
     *
     * @Route("/profile/mygames", name="gameMygames")
     * @Template()
     */
    public function myGamesAction() {
        /**
         * @var \Tdom\GameBundle\Manager\GameManager $gm
         */
        $gm = $this->get("tdom.game.manager");
        $categoriesAndGames = $gm->findUserGames($this->getUser());
        $form = $this->getGameForm();

        return array("categories" => $categoriesAndGames, "form" => $form->createView());
    }

    /**
     * Create new game action from mygames page
     *
     * @Route("/profile/game/form", name="profileGameForm")
     * @Template()
     */
    public function formAction(){
        $form = $this->getGameForm();
        return array("form" => $form->createView());
    }

    /**
     * Game Saving action
     *
     * @Route("/profile/game/save", name="profileGameSave")
     * @param Request $request
     * @return Response
     */
    public function saveAction(Request $request) {
        /**
         * @var \Tdom\GameBundle\Manager\GameManager $gm
         */
        $gm = $this->get("tdom.game.manager");
        $response = new JsonResponse();
        $game = new Game();
        $game->setUserBy($this->getUser());
        $form = $this->getGameForm($game);
        $form->handleRequest($request);

        if($form->isValid()) {
            try {
                //Adding a new game
                $game = $gm->addGame($game);

                if($game) {
                    $this->data['error'] = false;
                    $this->data['cat'] = $game->getCategory()->getId();
                    $this->data['item'] = $this->renderView('TdomGameBundle:Game:Partials/item.html.twig', array("game" => $game));
                    $form = $this->getGameForm();
                    $this->data['form'] = $this->renderView('TdomGameBundle:Game:Partials/form.html.twig', array("form" => $form->createView(), "success" => true));
                }
            }
            catch (Exception $e) {
                $this->data['error'] = true;
                $this->data['form'] = $this->renderView('TdomGameBundle:Game:Partials/form.html.twig', array("form" => $form->createView()));
            }
        }
        else {
            $this->data['error'] = true;
            $this->data['form'] = $this->renderView('TdomGameBundle:Game:Partials/form.html.twig', array("form" => $form->createView()));
        }
        $response->setData($this->data);
        return $response;
    }

    /**
     * Game Search ajax action
     *
     * @Route("/profile/game/search/{q}", name="gameSearch")
     * @param String $q
     * @return JsonResponse
     */
    public function searchAction($q = null) {
        /**
         * @var \Tdom\GameBundle\Manager\GameManager $gm
         */
        $gm = $this->get("tdom.game.manager");
        $response = new JsonResponse();

        $games = $gm->findGameByName($q);

        $this->data['data'] = $this->renderView('TdomGameBundle:Game:Partials/list.html.twig', array("games" => $games, 'q' => $q));
        $this->data['q'] =  $q;

        $response->setData($this->data);
        return $response;
    }

    /**
     * Add as assign game action
     *
     * @Route("/profile/game/add/{id}", name="userAddGame")     *
     * @param $id
     * @return JsonResponse
     */
    public function addGameAction($id) {
        /* @var \Tdom\GameBundle\Manager\GameManager $gm */
        $gm = $this->get("tdom.game.manager");
        $response = new JsonResponse();
        $this->data['success'] = false;

        /* @var $game \Tdom\AdminBundle\Entity\Game */
        $game =$gm->addUserGame($id);
        if($game) {
            $this->data['success'] = true;
            $this->data['item'] = $this->renderView('TdomGameBundle:Game:Partials/item.html.twig', array("game" => $game));
            $this->data['id'] = $id;
            $this->data['cat'] = $game->getCategory()->getId();
        }
        $response->setData($this->data);
        return $response;
    }

    /**
     * Get Game adding form
     *
     * @param Game $game
     * @return \Symfony\Component\Form\FormView
     */
    private function getGameForm(Game $game = null) {
       return $this->createForm(new GameFormType(), $game);
    }

    /**
     * Remove as unassign game action
     *
     * @Route("/profile/game/remove/{id}", name="userRemoveGame")     *
     * @param $id
     * @return JsonResponse
     */
    public function removeAction($id) {
        /* @var \Tdom\GameBundle\Manager\GameManager $gm */
        $gm = $this->get("tdom.game.manager");
        $response = new JsonResponse();
        $this->data['success'] = false;

        /* @var $game \Tdom\AdminBundle\Entity\Game */
        $game =$gm->removeUserGame($id);

        if($game) {
            $this->data['success'] = true;
        }

        $response->setData($this->data);
        return $response;
    }

}
