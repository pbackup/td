<?php

namespace Tdom\GameBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;

class FindGamer {

    private $category;

    private $games;

    private $type;


    public function __construct() {
        $this->games = new ArrayCollection();
    }

    /**
     * @param mixed $category
     */
    public function setCategory($category) {
        $this->category = $category;
    }

    /**
     * @return mixed
     */
    public function getCategory() {
        return $this->category;
    }

    /**
     * @param mixed $games
     */
    public function setGames($games) {
        $this->games = $games;
    }

    public  function addGame($game){
        $this->games[] = $game;
    }

    /**
     * @return mixed
     */
    public function getGames() {
        return $this->games;
    }

    /**
     * @param mixed $type
     */
    public function setType($type) {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getType() {
        return $this->type;
    }
} 