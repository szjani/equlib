<?php
namespace Equ\Form\ElementCreator\Dojo;

class Factory extends \Equ\Form\ElementCreator\AbstractFactory {
  
	public function createArrayCreator() {
    return new ArrayCreator($this->getNamespace());
  }

  public function createSubmitCreator() {
    return new SubmitCreator($this->getNamespace());
  }
  
  /**
   * @return \Zend_Form_Element
   */
  public function createStringCreator() {
    return new StringCreator($this->getNamespace());
  }

  /**
   * @return \Zend_Form_Element
   */
  public function createIntegerCreator() {
    return new NumberCreator($this->getNamespace());
  }
  
  /**
   * @return \Zend_Form_Element
   */
  public function createSmallintCreator() {
    return new NumberCreator($this->getNamespace());
  }

  /**
   * @return \Zend_Form_Element
   */
  public function createBigintCreator() {
    return new NumberCreator($this->getNamespace());
  }

  /**
   * @return \Zend_Form_Element
   */
  public function createDecimalCreator() {
    return new StringCreator($this->getNamespace());
  }

  /**
   * @return \Zend_Form_Element
   */
  public function createFloatCreator() {
    return new NumberCreator($this->getNamespace());
  }
  
  /**
   * @return \Zend_Form_Element
   */
  public function createBooleanCreator() {
    return new CheckboxCreator($this->getNamespace());
  }
  
  /**
   * @return \Zend_Form_Element
   */
  public function createDateCreator() {
    return new DateCreator($this->getNamespace());
  }

  /**
   * @return \Zend_Form_Element
   */
  public function createTimeCreator() {
    return new TimeCreator($this->getNamespace());
  }

  /**
   * @return \Zend_Form_Element
   */
  public function createDateTimeCreator() {
    return new StringCreator($this->getNamespace());
  }
  
  /**
   * @return \Zend_Form_Element
   */
  public function createTextCreator() {
    return new TextCreator($this->getNamespace());
  }

  /**
   * @return \Zend_Form_Element
   */
  public function createObjectCreator() {
    return new StringCreator($this->getNamespace());
  }
  
  /**
   * @return \Zend_Form_Element
   */
  public function createPasswordCreator() {
    return new PasswordCreator($this->getNamespace());
  }

  public function createMultiCreator() {
    return new MultiCreator($this->getNamespace());
  }

}