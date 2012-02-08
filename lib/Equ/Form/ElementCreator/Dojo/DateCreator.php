<?php
namespace Equ\Form\ElementCreator\Dojo;

class DateCreator extends \Equ\Form\ElementCreator\AbstractCreator {

  protected function buildElement($fieldName) {
    return new \Zend_Dojo_Form_Element_DateTextBox($fieldName);
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