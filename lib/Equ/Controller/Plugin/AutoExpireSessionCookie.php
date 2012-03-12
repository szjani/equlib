<?php
namespace Equ\Controller\Plugin;
use
  Zend_Controller_Plugin_Abstract,
  Zend_Session;

class AutoExpireSessionCookie extends Zend_Controller_Plugin_Abstract {
  
  public function dispatchLoopShutdown() {
    if (Zend_Session::getIterator()->count() == 0) {
      Zend_Session::expireSessionCookie();
    }
  }
  
}
