<?php
namespace Equ\Form\ElementCreator\Dojo;

class ArrayCreator extends \Equ\Form\ElementCreator\AbstractCreator {

  protected function buildElement($fieldName) {
    return new \Zend_Dojo_Form_Element_FilteringSelect($fieldName);
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