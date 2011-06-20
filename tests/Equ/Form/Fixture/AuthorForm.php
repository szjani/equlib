<?php
namespace Form\Fixture;

class AuthorForm extends \Zend_Form_SubForm {
  
  public function init() {
    
    $this->addElement(new \Zend_Form_Element_Text('name'));
    $this->addElement(new \Zend_Form_Element_Text('email'));
    
  }
  
}