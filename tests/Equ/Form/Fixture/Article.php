<?php
namespace Form\Fixture;
use
  Equ\Object\Validator,
  Equ\Object\Validatable;

class Article {
  
  private $text;
  
  private $comments = array();
  
  public function __construct($text) {
    $this->setText($text);
  }
  
  public function setText($text) {
    $this->text = $text;
  }
  
  public function getText() {
    return $this->text;
  }
  
  public function getComments() {
    return $this->comments;
  }
  
  public function setComments(array $comments) {
    $this->comments = $comments;
  }
  
}