<?php
namespace Equ\Form\ElementCreator\Builtin;

class TextCreator extends \Equ\Form\ElementCreator\AbstractCreator {

  protected function buildElement($fieldName) {
    return new \Zend_Form_Element_Textarea($fieldName);
  }

}