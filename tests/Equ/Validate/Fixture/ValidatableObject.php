<?php
namespace Equ\Validate\Fixture;

use Equ\Object\Validatable;
use Equ\Validate\Object\ObjectInterface as ObjectValidator;

class ValidatableObject implements Validatable
{
    protected $accessable;
    
    protected $nonAccessable;
    
    public function getAccessable()
    {
        return $this->accessable;
    }
    
    public function setAccessable($value)
    {
        $this->accessable = $value;
    }
    
    public static function loadValidators(ObjectValidator $validator)
    {
        $validator->add('accessable', new \Zend_Validate_StringLength(8, 8));
    }
}