<?php
namespace Equ\Validate;

use Equ\Validate\DoctrineImplicit;

require_once 'BaseTestClass.php';

class DoctrineImplicitTest extends BaseTestClass
{
    public function testValidatorLengthRestriction()
    {
        $validator = new DoctrineImplicit($this->em->getClassMetadata(self::TEST_ENTITY_CLASS), 'originalFilename');
        self::assertTrue($validator->isValid(implode('', array_fill(0, 255, 'a'))));
        self::assertFalse($validator->isValid(implode('', array_fill(0, 256, 'a'))));
        
        $validator = new DoctrineImplicit($this->em->getClassMetadata(self::TEST_ENTITY_CLASS), 'fileHash');
        self::assertTrue($validator->isValid(null));
    }
    
    public function testValidatorBothRestriction()
    {
        $validator = new DoctrineImplicit($this->em->getClassMetadata(self::TEST_ENTITY_CLASS), 'file');
        self::assertTrue($validator->isValid(null));
        self::assertTrue($validator->isValid(''));
        self::assertTrue($validator->isValid('test'));
        self::assertTrue($validator->isValid(implode('', array_fill(0, 255, 'a'))));
        self::assertFalse($validator->isValid(implode('', array_fill(0, 256, 'a'))));
    }

}