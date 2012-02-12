<?php
namespace Equ\Form\ElementCreator\Bootstrap;

class PasswordCreator extends BaseCreator {

  protected function buildElement($fieldName) {
    $element = new \Zend_Form_Element_Password($fieldName);
    $this->initDecorators($element);
    return $element;
  }
  
}