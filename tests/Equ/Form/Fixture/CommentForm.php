<?php
namespace Form\Fixture;

class CommentForm extends \Zend_Form {
  
  public function init() {
    
    $this->setElementsBelongTo('comment');
    
    $this->addElement(new \Zend_Form_Element_Text('text'));
    $this->addSubForm(new AuthorForm(), 'author');
    
  }
  
}