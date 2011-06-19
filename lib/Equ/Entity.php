<?php
namespace Equ;
use
  Equ\Entity\IFormBase,
  Equ\Entity\IEntityVisitor,
  Equ\Exception\RuntimeException,
  Equ\Exception\BadMethodCallException;

/**
 * Abstract entity class
 *
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link        $Link$
 * @version     $Revision$
 * @author      Szurovecz János <szjani@szjani.hu>
 * 
 * @MappedSuperclass
 * @HasLifecycleCallbacks
 */
abstract class Entity implements IFormBase, \ArrayAccess {

  private $fieldValidators = array();
  
  public function __construct() {
    $this->init();
  }
  
  public function init() {}
  
  /**
   * @see Equ\Entity.IVisitable::accept()
   */
  public function accept(IEntityVisitor $visitor) {
    $visitor->visitEntity($this);
  }
  
  /**
   * @param string $fieldName
   * @param \Zend_Validate_Abstract $validator
   * @return Entity
   */
  public function addFieldValidator($fieldName, \Zend_Validate_Abstract $validator) {
    if (!array_key_exists($fieldName, $this->fieldValidators)) {
      $this->fieldValidators[$fieldName] = array();
    }
    $this->fieldValidators[$fieldName][] = $validator;
    return $this;
  }
  
  /**
   * @param string $fieldName
   * @return Entity
   */
  public function clearFieldValidators($fieldName) {
    if (array_key_exists($fieldName, $this->fieldValidators)) {
      unset($this->fieldValidators[$fieldName]);
    }
    return $this;
  }

  /**
   * @see Equ\Entity.FormBase::getFieldValidators()
   * @param string $fieldName
   * @return \Zend_Validate_Abstract[]
   */
  public function getFieldValidators($fieldName) {
    if (array_key_exists($fieldName, $this->fieldValidators)) {
      return $this->fieldValidators[$fieldName];
    }
    return array();
  }
  
  /**
   * @PrePersist @PreUpdate
   */
  public function validate() {
    foreach ($this as $field => $value) {
      /* @var $validator \Zend_Validate_Abstract */
      foreach ($this->getFieldValidators($field) as $validator) {
        if (!$validator->isValid($this->$field)) {
          throw new RuntimeException(implode(PHP_EOL, $validator->getMessages()));
        }
      }
    }
  }

  public function offsetExists($offset) {
    $method = 'get' . \ucfirst($offset);
    return \method_exists($this, $method);
  }

  public function offsetGet($offset) {
    $method = 'get' . \ucfirst($offset);
    if (\method_exists($this, $method)) {
      return $this->$method();
    }
  }

  public function offsetSet($offset, $value) {
    throw new BadMethodCallException("ArrayAccess readonly!");
  }

  public function offsetUnset($offset) {
    throw new BadMethodCallException("ArrayAccess readonly!");
  }

  /**
   * @param ClassMetadata $metadata
   */
  public static function loadValidatorMetadata(ClassMetadata $metadata) {
//    $metadata->addPropertyValidator('name', new \Zend_Validate_NotEmpty());
  }

}