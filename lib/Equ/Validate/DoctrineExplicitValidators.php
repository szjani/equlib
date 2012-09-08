<?php
namespace Equ\Validate;

use Doctrine\ORM\Mapping\ClassMetadata;
use Zend_Validate_StringLength;
use Zend_Validate_NotEmpty;
use Zend_Validate;

class DoctrineExplicitValidators extends Zend_Validate
{
    private $metadata;
    
    private $fieldName;
    
    private $required = false;
    
    public function __construct(ClassMetadata $metadata, $fieldName)
    {
        $this->setMetadata($metadata);
        $this->fieldName = $fieldName;
        $this->setValidators();
    }
    
    public function setMetadata(ClassMetadata $metadata)
    {
        $this->metadata = $metadata;
    }
    
    public function isRequired()
    {
        return $this->required;
    }
    
    private function setValidators()
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
        
        if (array_key_exists('nullable', $restrictions) && !$restrictions['nullable']) {
            $this->required = true;
            $validator = new Zend_Validate_NotEmpty();
            $this->addValidator($validator);
        }
        if (array_key_exists('length', $restrictions) && is_numeric($restrictions['length'])) {
            $validator = new Zend_Validate_StringLength();
            $validator->setMax($restrictions['length']);
            $this->addValidator($validator);
        }
    }
    
}