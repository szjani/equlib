<?php
namespace Equ\Controller\Action\Helper;
use Zend_Controller_Action_Helper_Abstract;

/**
 * Container can be accessed from action controllers.
 * Default container is Zend_Registry
 *
 * @category    Equ
 * @package     Controller
 * @subpackage  Action\Helper
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @author      Szurovecz János <szjani@szjani.hu>
 */
class ServiceContainer extends Zend_Controller_Action_Helper_Abstract {

  /**
   * @var object
   */
  protected $container;

  /**
   * @param string $name
   * @return mixed
   */
  public function direct($name) {
    return $this->getContainer()->get($name);
  }
  
  /**
   * @param object $container
   * @return \Equ\Controller\Action\Helper\ServiceContainer 
   */
  public function setContainer($container) {
    $this->container = $container;
    return $this;
  }

  /**
   * @return object
   */
  public function getContainer() {
    if ($this->container === null) {
      $this->container = $this->getActionController()->getInvokeArg('bootstrap')->getContainer();
    }
    return $this->container;
  }

}