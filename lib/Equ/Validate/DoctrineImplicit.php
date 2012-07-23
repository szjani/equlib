<?php
namespace Equ\Validate;

use Doctrine\ORM\Mapping\ClassMetadata;
use Zend_Validate_StringLength;
use Zend_Validate_NotEmpty;
use Zend_Validate;

class DoctrineImplicit extends Zend_Validate
{
    private $metadata;
    
    private $fieldName;
    
    protected $nullValidator;
    
    /**
     * @param \Doctrine\ORM\Mapping\ClassMetadata $metadata
     * @param string $fieldName
     */
    public function __construct(ClassMetadata $metadata, $fieldName)
    {
        $this->setMetadata($metadata);
        $this->fieldName = $fieldName;
    }
    
    public function setMetadata(ClassMetadata $metadata)
    {
        $this->metadata = $metadata;
    }
    
    private function setValidators($value)
    {
        $fieldName = $this->fieldName;
        $restrictions = array();
        $metadata = $this->metadata;
        
        if ($metadata->hasAssociation($fieldName)
                && array_key_exists('joinColumns', $metadata->associationMappings[$fieldName])) {
            $restrictions = $metadata->associationMappings[$fieldName]['joinColumns'][0];
        } elseif ($metadata->hasField($fieldName)) {
            $restrictions = $metadata->fieldMappings[$fieldName];
        }
        
        $nullable = array_key_exists('nullable', $restrictions) && !$restrictions['nullable'];
        if ($nullable) {
            $validator = new Zend_Validate_NotEmpty();
            $this->addValidator($validator);
        }
        
        if (null !== $value && array_key_exists('length', $restrictions) && is_numeric($restrictions['length'])) {
            $validator = new Zend_Validate_StringLength(0, $restrictions['length']);
            $this->addValidator($validator);
        }
    }
    
    /**
     * Returns true if and only if $value passes all validations in the chain
     *
     * Validators are run in the order in which they were added to the chain (FIFO).
     *
     * @param  mixed $value
     * @return boolean
     */
    public function isValid($value)
    {
        $this->_validators = array();
        $this->setValidators($value);
        return parent::isValid($value);
    }
}