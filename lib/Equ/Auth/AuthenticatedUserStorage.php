<?php
namespace Equ\Auth;

interface AuthenticatedUserStorage {
  
  /**
   * @return UserInterface
   */
  public function getAuthenticatedUser();
  
}
