<?php
namespace Equ\Controller\Action\Helper;
use
    Zend_Filter_Word_CamelCaseToSeparator,
    Zend_Controller_Action_Helper_Abstract,
    Symfony\Component\DependencyInjection\Container;

/**
  * This helper automatically sets the public members of the current action controller,
  * which are available with the same name in the DI container.
  * 
  * For instance, if you have a public $em member variable and there is a service named 'em'
  * in the service container, this helper injects this service object into the mentioned variable.
  * 
  * Naming convention
  *  - camelcase member variables will be transitioned to dot separated name:
  *    * $contoller->entityManager = $container->get('entity.manager')
  *
  * @category    Equ
  * @package     Controller
  * @subpackage  Action\Helper
  * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
  * @author      Szurovecz János <szjani@szjani.hu>
  */
class ServiceInjector extends Zend_Controller_Action_Helper_Abstract
{
    
    /**
      * @var Container
      */
    protected $container;
    
    /**
      * @param Container $container 
      */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }
    
    public function init()
    {
        $filter = new Zend_Filter_Word_CamelCaseToSeparator('.');
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