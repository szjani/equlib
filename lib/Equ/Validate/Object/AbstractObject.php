<?php
namespace Equ\Validate\Object;

use Zend_Validate;
use ArrayIterator;
use Zend_Validate_Interface;
use Equ\Validate\Exception\InvalidArgumentException;

/**
 * Abstract object validator class.
 *
 * @category    Equ
 * @package     Equ\Validate
 * @subpackage  Object
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @author      Szurovecz János <szjani@szjani.hu>
  */
abstract class AbstractObject implements ObjectInterface
{
    /**
     * Class name of the object which will be validated.
     * 
     * @var string
     */
    protected $className;
    
    /**
     * Validator objects for members. Each validator is a chain.
     * 
     * @var array of Zend_Validate
     */
    protected $memberValidators = array();
    
    /**
     * @param string $className
     */
    public function __construct($className)
    {
        $this->className = $className;
    }
    
    /**
     * Should be called from isValue() method.
     * 
     * @param mixed $value
     * @throws InvalidArgumentException
     */
    protected function checkIsValidValueType($value)
    {
        if (!is_a($value, $this->className)) {
            throw new InvalidArgumentException("\$value has to a(n) '{$this->className}' instance!");
        }
    }
    
    /**
     * Factory method, can be overridden in derived classes.
     * 
     * @param string $memberName
     * @return \Zend_Validate
     */
    protected function createMemberValidator($memberName)
    {
        return new Zend_Validate();
    }
    
    /**
     * @param string $memberName
     * @param Zend_Validate_Interface $validator
     * @throws InvalidArgumentException
     */
    public function add($memberName, Zend_Validate_Interface $validator)
    {
        $this->get($memberName)->addValidator($validator);
    }
    
    /**
     * @param string $memberName
     * @return Zend_Validate
     */
    public function get($memberName)
    {
        if (!array_key_exists($memberName, $this->memberValidators)) {
            $this->memberValidators[$memberName] = $this->createMemberValidator($memberName);
        }
        return $this->memberValidators[$memberName];
    }
    
    /**
     * Returns an array of messages that explain why the most recent isValid()
     * call returned false. The array keys are validation failure message identifiers,
     * and the array values are the corresponding human-readable message strings.
     *
     * If isValid() was never called or if the most recent isValid() call
     * returned true, then this method returns an empty array.
     *
     * @return array
     */
    public function getMessages()
    {
        $ret = array();
        /* @var $validator Zend_Validate */
        foreach ($this->memberValidators as $validator) {
            $ret = array_merge($ret, $validator->getMessages());
        }
        return $ret;
    }
    
    /**
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->memberValidators);
    }
}