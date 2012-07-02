<?php
namespace Equ\Controller\Action\Helper;
use
    Zend_Controller_Action_Helper_Abstract,
    Equ\Auth\AuthenticatedUserStorage;

/**
  * This helper pass the authenticated user to the view from the given storage object
  *
  * @category    Equ
  * @package     Controller
  * @subpackage  Action\Helper
  * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
  * @author      Szurovecz János <szjani@szjani.hu>
  * @see         entities\UserRepository
  */
class AuthenticatedUser extends Zend_Controller_Action_Helper_Abstract
{
    
    /**
      * @var AuthenticatedUserStorage
      */
    private $storage;
    
    /**
      * @param AuthenticatedUserStorage $storage 
      */
    public function __construct(AuthenticatedUserStorage $storage)
    {
        $this->storage = $storage;
    }
    
    public function direct()
    {
        return $this->storage->getAuthenticatedUser();
    }
    
    public function preDispatch()
    {
        $user = $this->storage->getAuthenticatedUser();
        if ($this->_actionController->getRequest()->getParam('format') == 'json') {
            $this->_actionController->view->authenticatedUser = $user->toArray();
        } else {
            $this->_actionController->view->authenticatedUser = $user;
        }
    }
    
}