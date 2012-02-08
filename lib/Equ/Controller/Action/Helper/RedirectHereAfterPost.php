<?php
namespace Equ\Controller\Action\Helper;

/**
 * Use this helper before you redirect the user. It encodes and puts the current URL into the router.
 * If there is such parameter in request object and client has sent a POST request,
 * client is going to be redirected.
 * 
 * Usefull for authentication.
 *
 * @category   Equ
 * @package    Equ_Controller
 */
class RedirectHereAfterPost extends \Zend_Controller_Action_Helper_Abstract {
  
  const DEFAULT_KEY = 'redirect';
  
  private $key = self::DEFAULT_KEY;
  
  public function setKey($key) {
    $this->key = $key;
  }
  
  public function getKey() {
    return $this->key;
  }
  
  public function direct() {
    $this->saveCurrentUrl();
  }
  
  public function saveCurrentUrl() {
    $router = $this->getActionController()->getFrontController()->getRouter();
    if ($router instanceof \Zend_Controller_Router_Rewrite) {
      $router->setGlobalParam($this->getKey(), base64_encode($this->getRequest()->getRequestUri()));
    }
  }
  
  public function postDispatch() {
    if ($this->getRequest()->isPost()) {
      $encodedRedirectUrl = $this->getRequest()->getParam($this->getKey());
      if (isset($encodedRedirectUrl)) {
        $this->getActionController()->getHelper('redirector')->gotoUrlAndExit(base64_decode($encodedRedirectUrl));
      }
    }
  }
  
}
