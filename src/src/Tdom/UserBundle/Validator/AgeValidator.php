<?php
namespace Tdom\UserBundle\Validator;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class AgeValidator extends ConstraintValidator
{
    private $entityManager;

    public function __construct( $entityManager) {
        $this->entityManager = $entityManager;
    }

    public function validate($entity, Constraint $constraint) {
        $massage = "You are too young! Please check your birthday.";
        $user = $this->entityManager->getRepository("TdomUserBundle:User")->findOneBy(array('email' => $entity->getEmail()));

        if ((!$entity->getId()) && $user) {
            $this->context->addViolationAt('email',
                'A user already exists with this email address, please check your email.',
                array('%string%' => $entity)
            );
        }

        if ($entity->getAge() <= 10) {
            $this->context->addViolationAt('birthDay',
                $massage,
                array('%string%' => $entity)
            );
        }
    }
}