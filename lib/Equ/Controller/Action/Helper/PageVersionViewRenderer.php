<?php
namespace Equ\Controller\Action\Helper;
use
  Zend_View_Exception,
  Zend_Controller_Action_Helper_ViewRenderer;

/**
 * You can use versioned template files.
 * The version number can be defined by request parameter,
 * which key must be the constant defined below.
 * 
 * Template file's name should looks like this: template-0.phtml,
 * where 0 is the version number
 * 
 * @category    Equ
 * @package     Controller
 * @subpackage  Action\Helper
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @author      Szurovecz János <szjani@szjani.hu>
 */
class PageVersionViewRenderer extends Zend_Controller_Action_Helper_ViewRenderer {
  
  const PAGE_VERSION = 'page_version';
  
  /**
   * @var int
   */
  protected $version = 0;
  
  public function preDispatch() {
    parent::preDispatch();
    $this->version = $this->getRequest()->getParam(self::PAGE_VERSION, 0);
  }
  
  /**
   * Render a view based on path specifications
   *
   * Renders a view based on the view script path specifications.
   *
   * @param  string  $action
   * @param  string  $name
   * @param  boolean $noController
   * @return void
   */
  public function render($action = null, $name = null, $noController = null) {
    $action = $action ?: ($this->_scriptAction ?: $this->getRequest()->getActionName());
    try {
      $suffix = $this->version ? ('-' . $this->version) : '';
      return parent::render($action . $suffix, $name, $noController);
    } catch (Zend_View_Exception $e) {
      return parent::render($action, $name, $noController);
    }
  }
  
  /**
   * @return string
   */
  public function getName() {
    return 'ViewRenderer';
  }
  
}