<?php
namespace Equ\Controller\Action\Helper;
use entities\User;

class AuthenticatedUser extends \Zend_Controller_Action_Helper_Abstract {
  
  public function direct() {
    return $this->getAuthenticatedUser();
  }
  
  /**
   * @return User
   */
  private function getAuthenticatedUser() {
    /* @var $em \Doctrine\ORM\EntityManager */
    $em = $this->getActionController()->getHelper('serviceContainer')->getContainer()->get('doctrine.entitymanager');
    return $em->getRepository(User::className())->getAuthenticatedUser();
  }
  
  public function preDispatch() {
    $this->getActionController()->view->authenticatedUser = $this->getAuthenticatedUser();
  }
  
}