<?php

namespace Tdom\UserBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * UserConnectRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UserConnectRepository extends EntityRepository
{

    /**
     * Find existing user connects based on sourceUser and targetUser
     *
     * @param int $targetUserId
     * @param int $sourceUserId
     * @return result
     */
    public function checkExistingUserConnect($targetUserId, $sourceUserId){

        return $this->getEntityManager()->createQuery("select uc from TdomUserBundle:UserConnect uc
                    where (uc.targetUser =:targetUser and uc.sourceUser =:sourceUser)
                    or (uc.targetUser = :sourceUser and uc.sourceUser =:targetUser)")
            ->setParameter('sourceUser', $sourceUserId)
            ->setParameter('targetUser', $targetUserId)
            ->getOneOrNullResult();
    }
}