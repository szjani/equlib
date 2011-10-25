<?php

namespace Equ\Controller\Action\Helper;

class LayoutTimer extends \Zend_Controller_Action_Helper_Abstract {
  
  /**
   * @var \ArrayObject
   */
  protected $changes;
  
  public function __construct() {
    $this->changes = new \ArrayObject();
  }
  
  public function addChangeDate(\DateTime $dateTime, $layout) {
    $this->changes[$layout] = $dateTime;
  }
  
  protected function change($layout) {
    $this->getActionController()->getHelper('layout')->setLayout($layout);
  }
  
  public function direct() {
    $this->autoSwitch();
  }
  
  public function autoSwitch() {
    $now = new \DateTime();
    $correctLayout = null;
    foreach ($this->changes as $layout => $dateTime) {
      if ($dateTime <= $now) {
        $correctLayout = $layout;
      }
    }
    if ($correctLayout !== null) {
      $this->change($correctLayout);
    }
  }
  
}