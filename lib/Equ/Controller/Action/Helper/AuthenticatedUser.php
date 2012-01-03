<?php
namespace Equ\Controller\Action\Helper;
use Equ\Auth\AuthenticatedUserStorage;

class AuthenticatedUser extends \Zend_Controller_Action_Helper_Abstract {
  
  /**
   * @var AuthenticatedUserStorage
   */
  private $storage;
  
  /**
   * @param AuthenticatedUserStorage $storage 
   */
  public function __construct(AuthenticatedUserStorage $storage) {
    $this->storage = $storage;
  }
  
  public function direct() {
    return $this->storage->getAuthenticatedUser();
  }
  
  public function preDispatch() {
    $user = $this->storage->getAuthenticatedUser();
    if ($this->_actionController->getRequest()->isXmlHttpRequest() && method_exists($user, 'toArray')) {
      $this->_actionController->view->authenticatedUser = $user->toArray();
    } else {
      $this->_actionController->view->authenticatedUser = $user;
    }
  }
  
}