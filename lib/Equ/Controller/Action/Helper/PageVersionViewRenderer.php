<?php
namespace Equ\Controller\Action\Helper;

class PageVersionViewRenderer extends \Zend_Controller_Action_Helper_ViewRenderer {
  
  const PAGE_VERSION = 'page_version';
  
  private $version = 0;
  
  public function preDispatch() {
    parent::preDispatch();
    $this->version = $this->getRequest()->getParam(self::PAGE_VERSION, 0);
  }
  
  public function render($action = null, $name = null, $noController = null) {
    $action = $action ?: ($this->_scriptAction ?: $this->getRequest()->getActionName());
    try {
      $suffix = $this->version ? ('-' . $this->version) : '';
      return parent::render($action . $suffix, $name, $noController);
    } catch (\Zend_View_Exception $e) {
      return parent::render($action, $name, $noController);
    }
  }
  
  public function getName() {
    return 'ViewRenderer';
  }
  
}