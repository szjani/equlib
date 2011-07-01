<?php
namespace Equ\Form\ElementCreator\Factory;

class TextCreator extends \Equ\Form\ElementCreator\AbstractCreator {

  protected function buildElement($fieldName) {
    return new \Zend_Form_Element_Textarea($fieldName);
  }

}