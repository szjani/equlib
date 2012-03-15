<?php
namespace Equ\Controller\Action\Helper;
use Zend_Navigation;

class AutoTitle extends \Zend_Controller_Action_Helper_Abstract {
  
  private $navigation;
  
  public function __construct(Zend_Navigation $nav) {
    $this->navigation = $nav;
  }
  
  public function direct() {
    $request = $this->getRequest();
    $view    = $this->_actionController->view;
    $currentPage = $this->navigation->findOneBy('active', true);
    $title = $currentPage
      ? $currentPage->getTitle()
      : "Navigation/{$request->getParam('module')}/{$request->getParam('controller')}/{$request->getParam('action')}/label";
    $view->headTitle($title)->enableTranslation();
  }
}
