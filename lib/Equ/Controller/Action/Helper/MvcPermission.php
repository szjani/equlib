<?php
namespace Equ\Controller\Action\Helper;
use
    Zend_Controller_Action_Helper_Abstract,
    Zend_Controller_Plugin_ErrorHandler,
    Equ\Controller\Exception\PermissionException,
    Equ\Auth\AuthenticatedUserStorage,
    Zend_Acl;

/**
  * Check that user have or don't have permission to view current page
  *
  * @category    Equ
  * @package     Controller
  * @subpackage  Action\Helper
  * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
  * @author      Szurovecz János <szjani@szjani.hu>
  */
class MvcPermission extends Zend_Controller_Action_Helper_Abstract
{

    /**
      * @var AuthenticatedUserStorage
      */
    protected $storage;
    
    /**
      *
      * @var Zend_Acl
      */
    protected $acl;
    
    /**
      * @param AuthenticatedUserStorage $storage
      * @param Zend_Acl $acl
      */
    public function __construct(AuthenticatedUserStorage $storage, Zend_Acl $acl)
    {
        $this->storage = $storage;
        $this->acl     = $acl;
    }
    
    public function preDispatch()
    {
        $request = $this->getRequest();
        try {
            $user = $this->storage->getAuthenticatedUser();
            $resource = 'mvc:'.$request->getModuleName().'.'.$request->getControllerName().'.'.$request->getActionName();
            if (!$this->acl->isAllowed($user, $resource, 'list')) {
                throw new PermissionException("You don't have permission to view this page!");
            }
        } catch (RuntimeException $e) {
            $request
                ->setModuleName('index')
                ->setControllerName('error')
                ->setActionName('error');

            $error = new Zend_Controller_Plugin_ErrorHandler();
            $error->type = Zend_Controller_Plugin_ErrorHandler::EXCEPTION_OTHER;
            $error->request = clone $request;
            $error->exception = $e;
            $request->setParam('error_handler', $error);

        }
    }
}