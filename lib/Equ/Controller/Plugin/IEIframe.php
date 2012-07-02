<?php
namespace Equ\Controller\Plugin;
use Zend_Controller_Plugin_Abstract;

/**
  * Use cookies inside an iframe in Internet Explorer
  *
  * @category    Equ
  * @package     Controller
  * @subpackage  Plugin
  * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
  * @author      Szurovecz János <szjani@szjani.hu>
  */
class IEIframe extends Zend_Controller_Plugin_Abstract
{
    
    public function dispatchLoopShutdown()
    {
        $this->getResponse()->setHeader('P3P', 'CP="IDC DSP COR ADM DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT"');
    }
    
}