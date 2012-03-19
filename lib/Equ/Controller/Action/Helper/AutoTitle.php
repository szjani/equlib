<?php
namespace Equ\Controller\Action\Helper;
use Zend_Navigation;

class AutoTitle extends \Zend_Controller_Action_Helper_Abstract {
  
  private $navigation;
  
  private $title;
  
  public function __construct(Zend_Navigation $nav) {
    $this->navigation = $nav;
  }
  
  public function setTitle($title) {
    $this->title = $title;
    return $this;
  }
  
  public function direct($title = null) {
    return $this->setTitle($title);
  }
  
  public function postDispatch() {
    $request = $this->getRequest();
    $view    = $this->_actionController->view;
    $title   = $this->title;
    if (null === $title) {
      $currentPage = $this->navigation->findOneBy('active', true);
      $title = $currentPage
        ? $currentPage->getTitle()
        : "Navigation/{$request->getParam('module')}/{$request->getParam('controller')}/{$request->getParam('action')}/label";
    }
    $view->headTitle($title)->enableTranslation();
  }
}
