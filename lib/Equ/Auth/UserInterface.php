<?php
namespace Equ\Auth;
use
  Zend_Acl_Role_Interface,
  Equ\Object\Arrayable;

interface UserInterface extends Zend_Acl_Role_Interface, Arrayable {
  
  public function isLoggedIn();
  
}