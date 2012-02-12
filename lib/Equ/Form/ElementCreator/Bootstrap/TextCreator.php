<?php
namespace Equ\Form\ElementCreator\Bootstrap;

class TextCreator extends BaseCreator {

  protected function buildElement($fieldName) {
    $element = new \Zend_Form_Element_Textarea($fieldName);
    $this->initDecorators($element);
    return $element;
  }

}