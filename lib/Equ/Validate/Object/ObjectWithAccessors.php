<?php
namespace Equ\Validate\Object;

use Zend_Validate_Interface;
use Zend_Validate;
use Equ\Object\Helper as ObjectHelper;
use Equ\Validate\Exception\InvalidArgumentException;

/**
 * Validate object with getter, setter and isser methods.
 *
 * @category    Equ
 * @package     Equ\Validate
 * @subpackage  Object
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @author      Szurovecz János <szjani@szjani.hu>
  */
class ObjectWithAccessors extends AbstractObject
{
    /**
     * @var Equ\Object\Helper 
     */
    private $objectHelper;
    
    /**
     * @param string $className
     */
    public function __construct($className)
    {
        parent::__construct($className);
        $this->objectHelper = new ObjectHelper($className);
    }
    
    /**
     * Check whether the $memberName is accessable or not.
     * 
     * @param string $memberName
     * @throws InvalidArgumentException
     */
    private function checkMember($memberName)
    {
        if (!$this->objectHelper->hasGetter($memberName) && !$this->objectHelper->hasIsser($memberName)) {
            throw new InvalidArgumentException("Member '$memberName' of class '{$this->className}' is not accessable.");
        }
    }
    
    /**
     * @param string $memberName
     * @param Zend_Validate_Interface $validator
     * @throws InvalidArgumentException Member is not accessable
     */
    public function add($memberName, Zend_Validate_Interface $validator)
    {
        $this->checkMember($memberName);
        parent::add($memberName, $validator);
    }
    
    /**
     * @param string $memberName
     * @return Zend_Validate
     * @throws InvalidArgumentException Member is not accessable
     */
    public function get($memberName)
    {
        $this->checkMember($memberName);
        return parent::get($memberName);
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
     * @throws InvalidArgumentException $value is not instance of $this->className
     * 
     * @param  mixed $value
     * @return boolean
     * @throws Zend_Validate_Exception $value is not instance of $this->className
     */
    public function isValid($value)
    {
        $this->checkIsValidValueType($value);
        $this->objectHelper->setObject($value);
        $valid = true;
        /* @var $validator Zend_Validate */
        foreach ($this as $memberName => $validator) {
            $valid = $validator->isValid($this->objectHelper->get($memberName)) && $valid;
        }
        return $valid;
    }
    
}