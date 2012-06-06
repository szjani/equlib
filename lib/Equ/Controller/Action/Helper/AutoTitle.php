<?php
namespace Equ\Controller\Action\Helper;
use
  Zend_Controller_Action_Helper_Abstract,
  Zend_Navigation;

/**
 * You can manage the title of HTML pages automatically.
 * This helper tries to find the active page in the given Zend_Navigation object
 * and uses its title attribute. If there is no active item,
 * it creates the title from request parameters which can be translated by Zend_Translate
 *
 * @category    Equ
 * @package     Controller
 * @subpackage  Action\Helper
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @author      Szurovecz János <szjani@szjani.hu>
 */
class AutoTitle extends Zend_Controller_Action_Helper_Abstract {
  
  /**
   * @var Zend_Navigation
   */
  protected $navigation;
  
  /**
   * @var string
   */
  protected $title;
  
  /**
   * @param Zend_Navigation $nav 
   */
  public function __construct(Zend_Navigation $nav) {
    $this->navigation = $nav;
  }
  
  /**
   * @param string $title
   * @return AutoTitle 
   */
  public function setTitle($title) {
    $this->title = (string)$title;
    return $this;
  }
  
  /**
   * @param string $title
   * @return AutoTitle 
   */
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
        : "Navigation/{$request->getParam('module')}/{$request->getParam('controller')}/{$request->getParam('action')}/title";
    }
    $view->headTitle($title)->enableTranslation();
  }
}
