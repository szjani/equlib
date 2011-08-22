<?php

namespace Equ\Controller\Action\Helper;

class TemplateVersioner extends \Zend_Controller_Action_Helper_Abstract {
  
  /**
   * @var \ArrayObject
   */
  protected $versionChanges;
  
  public function __construct() {
    $this->versionChanges = new \ArrayObject();
  }
  
  public function addVersionChangeDate(\DateTime $dateTime, $newVersion) {
    $this->versionChanges[$newVersion] = $dateTime;
  }
  
  public function change($version) {
    $this->getRequest()->setParam(PageVersionViewRenderer::PAGE_VERSION, $version);
  }
  
  public function init() {
    if (null !== $this->getRequest()->getParam(PageVersionViewRenderer::PAGE_VERSION, null)) {
      return;
    }
    $now = new \DateTime();
    $this->versionChanges->ksort();
    $correctVersion = 0;
    foreach ($this->versionChanges as $version => $dateTime) {
      if ($dateTime <= $now) {
        $correctVersion = $version;
      }
    }
    $this->change($correctVersion);
  }
  
}