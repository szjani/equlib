<?php
namespace Equ\Controller\Action\Helper;
use Zend_Translate;

class AvailableLanguages extends \Zend_Controller_Action_Helper_Abstract {
  
  private $translate;
  
  public function __construct(Zend_Translate $translate) {
    $this->translate = $translate;
  }
  
  public function preDispatch() {
    $languages = $this->translate->getAdapter()->getList();
    sort($languages);
    $this->_actionController->view->languages = $languages;
  }
  
}