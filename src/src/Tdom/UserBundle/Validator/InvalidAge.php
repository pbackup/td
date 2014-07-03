<?php
namespace Tdom\UserBundle\Validator;
use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * */
class InvalidAge extends Constraint {

    public $entity;
    public $property;

    public function validatedBy() {
        return 'validator.age';
    }

    public function getTargets() {
        return self::CLASS_CONSTRAINT;
    }
} 