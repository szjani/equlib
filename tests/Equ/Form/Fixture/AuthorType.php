<?php
namespace Form\Fixture;
use
  Equ\Form\IMappedType,
  Equ\Form\IBuilder;

class AuthorType implements IMappedType {
  
  public function buildForm(IBuilder $builder) {
    $builder->add('name');
    $builder->add('email');
  }
  
  public function getObjectClass() {
    return 'Form\Fixture\Author';
  }
  
}