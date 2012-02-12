<?php
namespace Equ\Form\ElementCreator\Bootstrap;

class ArrayCreator extends BaseCreator {

  protected function buildElement($fieldName) {
    $element = new \Zend_Form_Element_Select($fieldName);
    $element = $this->initDecorators($element);
    return $element;
  }

}