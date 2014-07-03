<?php
/**
 * Created by PhpStorm.
 * User: julfiker
 * Date: 5/27/14
 * Time: 12:46 PM
 */

namespace Tdom\AdminBundle\Entity;

use Doctrine\ORM\EntityRepository;


class GameRepository extends  EntityRepository {

    /**
     * Find user games
     *
     * @param $userId
     * @return array
     */
    public function findUserGames($userId){

        $qb = $this->createQueryBuilder('g')
            ->innerJoin("g.users", "u")
            ->where('u.id =:userId')->setParameter('userId',$userId)
            ->andWhere('g.isActive = :isActive')->setParameter('isActive', true);

            $qb->orderBy('g.category');

        return $qb->getQuery()->getResult();
    }

    /**
     * Find by game name using like operator
     *
     * @param array $excluding
     * @param string $name
     * @return array
     */
    public function findByName($name, array $excluding = array()) {

        $qb = $this->createQueryBuilder('g')
            ->where('g.name LIKE :name')->setParameter('name',$name."%")
            ->andWhere('g.isActive = :isActive')->setParameter('isActive', true);


            if($excluding) {
                $qb->andWhere('g.id NOT IN (:excluding)')->setParameter('excluding', $excluding);
            }

            $qb->orderBy('g.name');

        return $qb->getQuery()->getResult();
    }

} 