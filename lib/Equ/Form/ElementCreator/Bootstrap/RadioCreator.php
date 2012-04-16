<?php
namespace Equ\Form\ElementCreator\Bootstrap;

class RadioCreator extends BaseCreator {

  protected function buildElement($fieldName) {
    $element = new \Zend_Form_Element_Radio($fieldName);
    $element = $this->initDecorators($element);
    return $element;
  }

}