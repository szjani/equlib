<?php
namespace Equ\Controller\Action\Helper;

use Zend_Controller_Action_Helper_Abstract;
use Equ\Controller\Exception\UnexpectedValueException;

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
class ServiceContainer extends Zend_Controller_Action_Helper_Abstract
{
    /**
     * @var object
     */
    protected $container;

    public function __construct($container)
    {
        if (!method_exists($container, 'get')) {
            throw new UnexpectedValueException(
                sprintf("Container object ('%s') must contains a public get() method!", get_class($container))
            );
        }
        $this->container = $container;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function direct($name)
    {
        return $this->getContainer()->get($name);
    }

    /**
     * @param object $container
     * @return \Equ\Controller\Action\Helper\ServiceContainer
     */
    public function setContainer($container)
    {
        $this->container = $container;
        return $this;
    }

    /**
     * @return object
     */
    public function getContainer()
    {
        return $this->container;
    }

}