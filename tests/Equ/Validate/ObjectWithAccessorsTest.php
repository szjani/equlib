<?php
namespace Equ\Validate;
use Equ\Validate\Object\ObjectWithAccessors as ObjectValidate;

use PHPUnit_Framework_TestCase;

require_once 'Fixture/ValidatableObject.php';

class ObjectWithAccessorsTest extends PHPUnit_Framework_TestCase
{
    /**
     * @expectedException Equ\Validate\Exception\InvalidArgumentException
     */
    public function testNonAccessable()
    {
        $object = new Fixture\ValidatableObject();
        $objectValidate = new ObjectValidate(get_class($object));
        Fixture\ValidatableObject::loadValidators($objectValidate);
        $objectValidate->add('nonAccessable', new \Zend_Validate_NotEmpty());
    }
    
    /**
     * @expectedException Zend_Validate_Exception
     */
    public function testInvalidObject()
    {
        $object = new Fixture\ValidatableObject();
        $objectValidate = new ObjectValidate(get_class($object));
        Fixture\ValidatableObject::loadValidators($objectValidate);
        $objectValidate->isValid(new \stdClass());
    }
    
    public function testIsValid()
    {
        $object = new Fixture\ValidatableObject();
        $objectValidate = new ObjectValidate(get_class($object));
        Fixture\ValidatableObject::loadValidators($objectValidate);
        
        $object->setAccessable('1234567');
        self::assertFalse($objectValidate->isValid($object));
        self::assertEquals(1, count($objectValidate->getMessages()));
        
        $object->setAccessable('12345678');
        self::assertTrue($objectValidate->isValid($object));
        self::assertEquals(0, count($objectValidate->getMessages()));
    }
}