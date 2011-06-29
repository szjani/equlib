<?php
namespace Equ\Form\ElementCreator\Dojo;

class PasswordCreator extends \Equ\Form\ElementCreator\AbstractCreator {

  protected function validatorAdded(\Zend_Form_Element $element, \Zend_Validate_Abstract $validator) {
    if ($validator instanceof \Zend_Validate_StringLength) {
      $element->setRegExp('\w{' . $validator->getMin() . ',' . $validator->getMax() . '}');
    }
  }
  
  protected function buildElement($fieldName) {
    return new \Zend_Dojo_Form_Element_PasswordTextBox($fieldName);
  }
  
  /**
   * @param \Zend_Form_Element $element
   * @return AbstractCreator
   */
  protected function createPlaceholder(\Zend_Form_Element $element) {
    parent::createPlaceholder($element);
    if (\Zend_Form::hasDefaultTranslator()) {
      $element->setDijitParam('placeHolder', \Zend_Form::getDefaultTranslator()->translate($this->getPlaceHolder()));
    } else {
      $element->setDijitParam('placeHolder', $this->getPlaceHolder());
    }
    return $this;
  }
  
}