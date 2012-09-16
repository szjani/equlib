<?php
namespace Form\Fixture;

class Comment implements \Equ\Object\Validatable {
  
  private $author = null;
  
  private $text = null;
  
  public function __construct(Author $author) {
    $this->setAuthor($author);
  }
  
  public function getText() {
    return $this->text;
  }

  public function setText($text) {
    $this->text = $text;
  }

  /**
   * @return Author
   */
  public function getAuthor() {
    return $this->author;
  }

  public function setAuthor($author) {
    $this->author = $author;
  }

    public static function loadValidators(\Equ\Object\Validator $validator)
    {
        
    }
  
}
