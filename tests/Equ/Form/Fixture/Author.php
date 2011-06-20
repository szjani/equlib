<?php
namespace Form\Fixture;
use
  Equ\Object\Validator,
  Equ\Object\Validatable;

class Author implements Validatable {
  
  private $name;
  
  private $email;
  
  public function __construct($name) {
    $this->setName($name);
  }
  
  public function getName() {
    return $this->name;
  }

  public function setName($name) {
    $this->name = $name;
  }
  
  public function getEmail() {
    return $this->email;
  }

  public function setEmail($email) {
    $this->email = $email;
  }

  public static function loadValidators(Validator $validator) {
    $validator
      ->add('email', new \Zend_Validate_NotEmpty())
      ->add('email', new \Zend_Validate_EmailAddress());
      
  }
  
}
