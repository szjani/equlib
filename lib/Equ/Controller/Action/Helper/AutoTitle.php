<?php
namespace Equ\Controller\Action\Helper;

class AutoTitle extends \Zend_Controller_Action_Helper_Abstract {
  
  public function direct() {
    $view = $this->_actionController->view;
    $request = $this->getRequest();
    
    $title = $view->pageTitle =
      $view->translate(
        "Navigation/{$request->getParam('module')}/{$request->getParam('controller')}/{$request->getParam('action')}/label"
      );
    $view->headTitle($title);
  }
  
}