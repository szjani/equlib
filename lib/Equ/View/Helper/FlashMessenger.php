<?php
namespace Equ\View\Helper;
use
    Equ\Message,
    Equ\Controller\Action\Helper\FlashMessenger as ActionHelperFlashMessenger,
    Equ\View\InvalidArgumentException;

/**
  * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
  * @link        $Link$
  * @since       0.1
  * @version     $Revision$
  * @author      Szurovecz János <szjani@szjani.hu>
  */
class FlashMessenger extends \Zend_View_Helper_Abstract
{

    /**
      * @param string $namespace
      * @return string rendered HTML
      */
    public function flashMessenger($namespace = 'default')
    {
        $flashMessenger = \Zend_Controller_Action_HelperBroker::getStaticHelper('flashMessenger');
        if (!($flashMessenger instanceof ActionHelperFlashMessenger)) {
            throw new InvalidArgumentException("Action helper flashMessenger has to be an instance of 'Equ\Controller\Action\Helper\FlashMessenger'");
        }
        $flashMessenger->setNamespace($namespace);
        $this->view->messageTypes = $flashMessenger->collectMessages();
        return $this->view->render('message.phtml');
    }

}