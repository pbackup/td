<?php

namespace Tdom\GameBundle\Manager;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Security\Core\SecurityContext;
use Tdom\UserBundle\Entity\User;
use Tdom\AdminBundle\Entity\Game;
use Tdom\GameBundle\Model\FindGamer as FindUser;
use Gregwar\Image\Image;

class GameManager {
    /**
     * @var \Doctrine\ORM\EntityManager $em
     */
    private $em;

    /**
     * @var \Symfony\Component\Security\Core\SecurityContext $sc
     */
    private $sc;

    private $logger;

    private $session;

    private $router;

    /**
     * Injected entity manager and security context
     *
     * @param \Doctrine\ORM\EntityManager $em
     * @param \Symfony\Component\Security\Core\SecurityContext $sc
     * @param $session
     * @param $logger
     */
    function __construct(EntityManager $em, SecurityContext $sc, $session, $logger) {
        $this->em = $em;
        $this->sc = $sc;
        $this->session = $session;
        $this->logger = $logger;
    }

    /**
     * Find Usr Games in service end
     *
     * @param User $user
     * @return List<Game>
     */
    public function findUserGames(User $user){
        $games = array();
        try {

            /* @var $gameRepo \Tdom\AdminBundle\Entity\GameRepository ; */
            $gameRepo = $this->em->getRepository("TdomAdminBundle:Game");

            $games = $gameRepo->findUserGames($user->getId());
            $games = $this->makeGroupByUserGames($games);
        }
        catch (Exception $e){
            $this->logger->error('findUserGames: An error occurred '. $e->getMessage());
        }

        return $games;
    }

    /**
     * Make array by group (category)
     *
     * @param $games
     * @return array $group
     */
    private function makeGroupByUserGames($games){
        $groupGames = array();

        foreach($games as $game){
            $groupGames[$game->getCategory()->getId()][] = $game;
        }

        $categories = $this->findGameCategories();

        foreach ( $categories as $category){
            if(isset($groupGames[$category->getId()])){
                $category->setGames($groupGames[$category->getId()]);
            }
            else {
                $category->setGames(null);
            }
        }

        return $categories;
    }

    /**
     * Find categories
     *
     * @return array
     */
    public  function findGameCategories() {
        /**
         * @var \Tdom\AdminBundle\Entity\CategoryRepository $categoryRepo;
         */
        $categoryRepo = $this->em->getRepository("TdomAdminBundle:Category");

        return $categoryRepo->findBy(array('isActive' => true), array("orderBy" => "ASC"));
    }

    /**
     * Find game By name
     *
     * @param $name
     * @return array|string
     */
    public function findGameByName($name) {
        try {
            /* @var $gameRepo \Tdom\AdminBundle\Entity\GameRepository ; */
            $gameRepo = $this->em->getRepository("TdomAdminBundle:Game");

            /* @var $user \Tdom\UserBundle\Entity\User */
            $user = $this->sc->getToken()->getUser();

            $userExistingGames = array();
            foreach ($user->getGames() as $game) {
                $userExistingGames[] = $game->getId();
            }

            return $gameRepo->findByName($name, $userExistingGames);
        }
        catch (Exception  $e){
            return $e->getMessage();
        }
    }

    /**
     * Add User game
     *
     * @param $gameId
     * @return bool|null|object
     */
    public function addUserGame($gameId) {
        /* @var $gameRepo \Tdom\AdminBundle\Entity\GameRepository ; */
        $gameRepo = $this->em->getRepository("TdomAdminBundle:Game");

        /* @var $game \Tdom\AdminBundle\Entity\Game */
        $game = $gameRepo->find($gameId);
        $user = $this->sc->getToken()->getUser();

        if($user) {
            /* @var $user \Tdom\UserBundle\Entity\User */
            $user = $this->em->getRepository("TdomUserBundle:User")->find($user->getId());

            if(!$user->getGames()->contains($game)){
                $user->addGames($game);
                $this->em->persist($user);
                $this->em->flush();

                return $game;
            }
        }
        return false;
    }

    /**
     * Remove user game
     *
     * @param int $gameId
     * @return bool|\Tdom\AdminBundle\Entity\Game
     */
    public function removeUserGame($gameId) {
        /* @var $gameRepo \Tdom\AdminBundle\Entity\GameRepository ; */
        $gameRepo = $this->em->getRepository("TdomAdminBundle:Game");

        /* @var $game \Tdom\AdminBundle\Entity\Game */
        $game = $gameRepo->find($gameId);
        $user = $this->sc->getToken()->getUser();

        if($user) {
            /* @var $user \Tdom\UserBundle\Entity\User */
            $user = $this->em->getRepository("TdomUserBundle:User")->find($user->getId());

            if($user->getGames()->contains($game)){
                $user->removeGame($game);
                $this->em->persist($user);
                $this->em->flush();

                return $game;
            }
        }
        return false;
    }

    /**
     * Add new game from user end
     *
     * @param Game $game
     * @return bool|null|object|Game
     */
    public  function addGame(Game $game) {

       try  {
            $game->setIsActive(true);
            $this->em->persist($game);
            $this->em->flush();
            $game = $this->addUserGame($game->getId());

            if ($game)
                return $game;
       }
       catch (Exception $e){
            $this->logger->info($e->getMessage());
       }
        return false;
    }

    public function findFirstCategoryId() {
        return $this->em->createQuery('SELECT c.id FROM TdomAdminBundle:Category as c Order by c.name ASC')
            ->setMaxResults(1)
            ->getSingleScalarResult();
    }

    /**
     * Find user by games based on user filter from find page
     *
     * @param FindUser $findUser
     * @param $router
     * @return array|bool
     */
    public function findUserByGames(FindUser $findUser, $router) {
        if(!$findUser)
            return false;

        $games = array();
        $categoryId = ($findUser->getCategory())? $findUser->getCategory()->getId() : 0;

        foreach ($findUser->getGames() as $game) {
            $games[] = $game->getId();
        }

        /** @var $userRepo \Tdom\UserBundle\Entity\UserRepository */
        $userRepo = $this->em->getRepository("TdomUserBundle:User");
        $users = $userRepo->findUsersByGamesAndCategory($games, $categoryId);

        if ($users) {
            $this->router = $router;
            return $this->makeUserDataStuctureForMap($users);
        }

        return false;
    }

    /**
     * Make data stucture for map viewing
     *
     * @param array $users
     * @return array
     */
    public function makeUserDataStuctureForMap(array $users) {
        $data = array();

        /**@var $user \Tdom\UserBundle\Entity\User */
        foreach ($users as $user) {

            if (!$location = $user->getLocation())
                continue;

            if($user->getAvatar()) {
              $img = Image::open(__DIR__.'/../../../../web/uploads/'.$user->getAvatar())
                    ->resize(200);
              $img = "/".(string)$img;
            }
            else {
                $img ='/bundles/tdomuser/images/default_avatar.jpeg';
            }

            $profilePath = $this->router->generate('userProfile', array('userId' => $user->getId()));
            $mapData = array();
            $mapData['userId'] = $user->getId();
            $mapData['nickname'] = $user->getNickName();
            $mapData['avatar'] = (string)$img;
            $mapData['fullName'] = $user->getFullName();
            $mapData['birthday'] = $user->getAge()." years";
            $mapData['country'] = $user->getCountry();
            $mapData['city'] = $user->getCity();
            $mapData['path'] = $profilePath;

            if ($location = $user->getLocation()) {
                $locArr = explode("," , $location);
                $mapData['lat'] = $locArr[0];
                $mapData['lng'] = $locArr[1];
            }

            $data[] = $mapData;
        }
        return $data;
    }
} 