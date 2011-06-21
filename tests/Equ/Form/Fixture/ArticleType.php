<?php
namespace Form\Fixture;
use
  Equ\Form\IMappedType,
  Equ\Form\IBuilder;

class ArticleType implements IMappedType {
  
  public function buildForm(IBuilder $builder) {
    $builder->add('text');
    $builder->addSub('author', new AuthorType());
    $builder->addSub('comments', new CommentType(), true);
  }
  
  public function getObjectClass() {
    return 'Form\Fixture\Article';
  }
  
}