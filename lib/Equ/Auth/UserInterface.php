<?php
namespace Equ\Auth;
use Zend_Acl_Role_Interface;

interface UserInterface extends Zend_Acl_Role_Interface {
  
  public function isLoggedIn();
  
  public function toArray();
  
}