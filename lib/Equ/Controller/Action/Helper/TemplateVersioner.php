<?php
namespace Equ\Controller\Action\Helper;
use
    Zend_Controller_Action_Helper_Abstract,
    ArrayObject,
    DateTime;

/**
  * You can relate template versions and DateTimes.
  * It is usefull when you want to automatically change
  * to the required template file.
  *
  * @category    Equ
  * @package     Controller
  * @subpackage  Action\Helper
  * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
  * @author      Szurovecz János <szjani@szjani.hu>
  * @see         Equ\Controller\Action\Helper\PageVersionViewRenderer
  */
class TemplateVersioner extends Zend_Controller_Action_Helper_Abstract
{

    /**
      * @var ArrayObject
      */
    protected $versionChanges;

    public function __construct()
    {
        $this->versionChanges = new ArrayObject();
    }

    /**
      * @param DateTime $dateTime
      * @param string $newVersion
      * @return \Equ\Controller\Action\Helper\TemplateVersioner
      */
    public function addVersionChangeDate(DateTime $dateTime, $newVersion)
    {
        $this->versionChanges[$newVersion] = $dateTime;
        return $this;
    }

    /**
      * @param string $version
      * @return \Equ\Controller\Action\Helper\TemplateVersioner
      */
    public function change($version)
    {
        $this->getRequest()->setParam(PageVersionViewRenderer::PAGE_VERSION, $version);
        return $this;
    }

    public function init()
    {
        if (null !== $this->getRequest()->getParam(PageVersionViewRenderer::PAGE_VERSION, null)) {
            return;
        }
        $now = new DateTime();
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