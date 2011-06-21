<?php
namespace Form\Fixture;

class ArticleForm extends \Zend_Form_SubForm {
  
  public function init() {
    
    $this->addElement(new \Zend_Form_Element_Text('text'));
    $this->addSubForm(new AuthorForm(), 'author');
    $this->addSubForm(new CommentForm(), 'comments[0]');
    $this->addSubForm(new CommentForm(), 'comments[1]');
    
  }
  
}