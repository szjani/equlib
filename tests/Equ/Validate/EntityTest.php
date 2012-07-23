<?php
namespace Equ\Validate;

use Validate\Fixture\Asset;
use Equ\Validate\Object\Entity as EntityValidator;

require_once 'BaseTestClass.php';

class EntityTest extends BaseTestClass
{
    public function testIsValid()
    {
        $validator = new EntityValidator($this->em->getClassMetadata(self::TEST_ENTITY_CLASS));
        Asset::loadValidators($validator);
        $object = new Asset();
        $object->setFile(implode('', array_fill(0, 256, 'a')));
        self::assertFalse($validator->isValid($object));
        self::assertTrue(array_key_exists(\Zend_Validate_StringLength::TOO_LONG, $validator->getMessages()));
        self::assertTrue(array_key_exists(\Zend_Validate_StringLength::INVALID, $validator->getMessages()));
    }
}