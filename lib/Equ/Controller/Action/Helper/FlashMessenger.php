<?php
namespace Equ\Controller\Action\Helper;
use Equ\Message;

class FlashMessenger extends \Zend_Controller_Action_Helper_FlashMessenger {
  
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
   * @param boolean $translate
   * @return FlashMessenger
   */
  public function addMessage($message, $type = Message::SUCCESS, $namespace = 'default', $translate = true) {
    parent::setNamespace($namespace);
    $messageObj = null;
    if ($message instanceof \Exception) {
      $messageObj = new Message($message->getMessage(), Message::ERROR);
    } else {
      $messageObj = new Message($message, $type);
    }
    $messageObj->setTranslate($translate);
    return parent::addMessage($messageObj);
  }
  
}