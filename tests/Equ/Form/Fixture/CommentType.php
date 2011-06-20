<?php
namespace Form\Fixture;
use
  Equ\Form\IMappedType,
  Equ\Form\IBuilder;

class CommentType implements IMappedType {
  
  public function buildForm(IBuilder $builder) {
    $builder->add('text');
    $builder->addSub('author', new AuthorType());
  }
  
  public function getObjectClass() {
    return 'Form\Fixture\Comment';
  }

  
}