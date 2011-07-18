<?php
namespace Equ\Form\ElementCreator\Factory;

class PasswordCreator extends \Equ\Form\ElementCreator\AbstractCreator {

  protected function buildElement($fieldName) {
    return new \Zend_Form_Element_Password($fieldName);
  }
  
}