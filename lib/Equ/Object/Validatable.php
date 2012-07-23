<?php
namespace Equ\Object;

use Equ\Validate\Object\ObjectInterface as ObjectValidator;

interface Validatable
{

    /**
      * @param Validator $validator
      */
    public static function loadValidators(ObjectValidator $validator);

}