<?php
namespace Tdom\AdminBundle\Validator;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UniqueValidator extends ConstraintValidator
{
    private $entityManager;

    public function __construct( $entityManager) {
        $this->entityManager = $entityManager;
    }

    public function validate($value, Constraint $constraint) {
        /* @var $gameRepo \Tdom\AdminBundle\Entity\GameRepository ; */
        $gameRepo = $this->entityManager->getRepository("TdomAdminBundle:Game");
        $existingGame = $gameRepo->findOneBy(array('name' => $value->getName()));
        $massage = "You can't add this game since it was added already.";
        if ($existingGame and !($value->getId())) {
            $this->context->addViolationAt('name',
                $massage,
                array('%string%' => $value)
            );
        }
    }
}