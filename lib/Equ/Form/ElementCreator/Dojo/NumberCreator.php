<?php
namespace Equ\Form\ElementCreator\Dojo;

class NumberCreator extends \Equ\Form\ElementCreator\AbstractCreator {

  protected function buildElement($fieldName) {
    return new \Zend_Dojo_Form_Element_NumberTextBox($fieldName);
  }

  /**
   * @param \Zend_Form_Element $element
   * @return AbstractCreator
   */
  protected function createPlaceholder(\Zend_Form_Element $element) {
    parent::createPlaceholder($element);
    $element->setDijitParam('placeHolder', \Zend_Form::getDefaultTranslator()->translate($this->getPlaceHolder()));
    return $this;
  }

}