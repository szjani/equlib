<?php
namespace Form\Fixture;
use
  Equ\Object\Validator,
  Equ\Object\Validatable;

class Article {
  
  private $text;
  
  private $author;
  
  private $comments = null;
  
  public function __construct($text, Author $author) {
    $this->setText($text);
    $this->setAuthor($author);
  }
  
  public function getAuthor() {
    return $this->author;
  }

  public function setAuthor(Author $author) {
    $this->author = $author;
  }

  public function setText($text) {
    $this->text = $text;
  }
  
  public function getText() {
    return $this->text;
  }
  
  public function getComments() {
    if ($this->comments === null) {
      $this->setComments(new \ArrayObject());
    }
    return $this->comments;
  }
  
  public function setComments(\ArrayObject $comments) {
    $this->comments = $comments;
  }
  
}