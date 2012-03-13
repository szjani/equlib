<?php
namespace Equ\Controller\Action\Helper;
use Zend_Navigation;

class AutoTitle extends \Zend_Controller_Action_Helper_Abstract {
  
  private $navigation;
  
  public function __construct(Zend_Navigation $nav) {
    $this->navigation = $nav;
  }
  
  public function direct() {
    $currentPage = $this->navigation->findOneBy('active', true);
    $view = $this->_actionController->view;
    $view->headTitle($view->translate($currentPage->getTitle()));
  }
  
}