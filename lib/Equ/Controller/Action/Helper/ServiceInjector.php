<?php
namespace Equ\Controller\Action\Helper;
use \Symfony\Component\DependencyInjection\Container;

class ServiceInjector extends \Zend_Controller_Action_Helper_Abstract {
  
  /**
   * @var Container
   */
  private $container;
  
  /**
   * @param Container $container 
   */
  public function __construct(Container $container) {
    $this->container = $container;
  }
  
  public function init() {
    $filter = new \Zend_Filter_Word_CamelCaseToSeparator('.');
    foreach (get_object_vars($this->_actionController) as $property => $variable) {
      if (!isset($variable)) {
        $serviceId = $filter->filter($property);
        if ($this->container->has($serviceId)) {
          $this->_actionController->$property = $this->container->get($serviceId);
        }
      }
    }
  }
  
}