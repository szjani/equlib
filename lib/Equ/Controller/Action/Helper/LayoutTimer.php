<?php
namespace Equ\Controller\Action\Helper;
use
    Zend_Controller_Action_Helper_Abstract,
    ArrayObject,
    DateTime;

/**
  * You can define which layout should be used in which time.
  * You should define the datetimes in chronological order.
  * It is usefull for contest sites, or when you want to use a layout temporarily.
  *
  * @category    Equ
  * @package     Controller
  * @subpackage  Action\Helper
  * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
  * @author      Szurovecz János <szjani@szjani.hu>
  */
class LayoutTimer extends Zend_Controller_Action_Helper_Abstract
{
    
    /**
      * @var ArrayObject
      */
    protected $changes;
    
    public function __construct()
    {
        $this->changes = new ArrayObject();
    }
    
    /**
      * @param DateTime $dateTime
      * @param string $layout 
      */
    public function addChangeDate(DateTime $dateTime, $layout)
    {
        $this->changes[$layout] = $dateTime;
    }
    
    /**
      * @param string $layout 
      */
    protected function change($layout)
    {
        $this->getActionController()->getHelper('layout')->setLayout($layout);
    }
    
    public function direct()
    {
        $this->autoSwitch();
    }
    
    public function autoSwitch()
    {
        $now = new DateTime();
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