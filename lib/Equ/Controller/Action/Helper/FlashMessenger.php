<?php
namespace Equ\Controller\Action\Helper;
use
  Equ\Message,
  Zend_Controller_Action_Helper_FlashMessenger;

/**
 * This helper extends the builtin FlashMessenger helper.
 * You can use Equ\Message object to distinguish message types
 * and you can pass an Exception object for addMessage() method.
 * The messages will be automatically forwarded to the view.
 *
 * @category    Equ
 * @package     Controller
 * @subpackage  Action\Helper
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @author      Szurovecz János <szjani@szjani.hu>
 */
class FlashMessenger extends Zend_Controller_Action_Helper_FlashMessenger {
  
  /**
   * @param string|\Exception $message
   * @param string $type
   * @param string $namespace
   * @param boolean $translate
   * @return FlashMessenger 
   */
  public function direct($message, $type = Message::SUCCESS, $namespace = 'default', $translate = true) {
    return $this->addMessage($message, $type, $namespace, $translate);
  }
  
  /**
   * @return array
   */
  public function collectMessages() {
    $messages = $this->getMessages() + $this->getCurrentMessages();
    $this->clearMessages();
    $this->clearCurrentMessages();
    $viewTypes = array();
    foreach ($messages as $message) {
      if ($message instanceof Message) {
        if (!array_key_exists($message->getType(), $viewTypes)) {
          $viewTypes[$message->getType()] = array();
        }
        $viewTypes[$message->getType()][] = $message->getMessage();
      }
    }
    return $viewTypes;
  }
  
  public function postDispatch() {
    $controller = $this->getActionController();
    if ($controller->getRequest()->isXmlHttpRequest()) {
      $controller->view->messages = $this->collectMessages();
    }
    parent::postDispatch();
  }
  
  /**
   * Add a message object to flashmessenger
   *
   * @param string|Exception $message
   * @param string $type
   * @param string $namespace
   * @return FlashMessenger
   */
  public function addMessage($message, $type = Message::SUCCESS, $namespace = 'default') {
    parent::setNamespace($namespace);
    $messageObj = null;
    if ($message instanceof \Exception) {
      $messageObj = new Message($message->getMessage(), Message::ERROR);
    } else {
      $messageObj = new Message($message, $type);
    }
    return parent::addMessage($messageObj);
  }
  
}