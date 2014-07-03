<?php
/**
 * Created by PhpStorm.
 * User: julfiker
 * Date: 6/2/14
 * Time: 12:57 PM
 */

namespace Tdom\AdminBundle\Validator;
use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * */
class UniqueValue extends Constraint {
    public $message = 'This name already used by other, please use search to add this page';
    public $entity;
    public $property;

    public function validatedBy() {
        return 'validator.unique';
    }

    public function getTargets() {
        return self::CLASS_CONSTRAINT;
    }

    /**
     * @param string $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }


} 