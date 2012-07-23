<?php
namespace Equ\Validate\Object;

use Doctrine\ORM\Mapping\ClassMetadata;
use Equ\Validate\DoctrineImplicit;

/**
 * Doctrine entity validator based on mapping rules such as nullable and length parameters.
 * It uses accessor methods for validation.
 *
 * @category    Equ
 * @package     Equ\Validate
 * @subpackage  Object
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @author      Szurovecz János <szjani@szjani.hu>
  */
class Entity extends ObjectWithAccessors
{
    /**
     * @var \Doctrine\ORM\Mapping\ClassMetadata 
     */
    protected $metadata;
    
    /**
     * @param \Doctrine\ORM\Mapping\ClassMetadata $metadata of the entity
     */
    public function __construct(ClassMetadata $metadata)
    {
        $this->metadata = $metadata;
        parent::__construct($metadata->name);
    }
    
    /**
     * Returns true if and only if $value meets the validation requirements
     *
     * If $value fails validation, then this method returns false, and
     * getMessages() will return an array of messages that explain why the
     * validation failed.
     * 
     * @param object $value Instance of $this->className
     * @return boolean
     * @throws Equ\Validate\Exception\InvalidArgumentException $value is not instance of $this->className
     * 
     * @param  mixed $value
     * @return boolean
     * @throws Zend_Validate_Exception $value is not instance of $this->className
     */
    public function isValid($value)
    {
        $this->checkIsValidValueType($value);
        foreach ($this->metadata->fieldNames as $field) {
            $this->add($field, new DoctrineImplicit($this->metadata, $field));
        }
        return parent::isValid($value);
    }
}