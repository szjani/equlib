<?php
namespace Equ\Controller\Action\Helper;
use
    Zend_Controller_Action_Helper_Abstract,
    Zend_Translate;

/**
  * This helper simple puts the available languages into view. It is usefull for the language chooser menu.
  *
  * @category    Equ
  * @package     Controller
  * @subpackage  Action\Helper
  * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
  * @author      Szurovecz János <szjani@szjani.hu>
  */
class AvailableLanguages extends Zend_Controller_Action_Helper_Abstract
{

    /**
      * @var Zend_Translate 
      */
    protected $translate;
    
    /**
      * @param Zend_Translate $translate 
      */
    public function __construct(Zend_Translate $translate)
    {
        $this->translate = $translate;
    }
    
    public function preDispatch()
    {
        $languages = $this->translate->getAdapter()->getList();
        sort($languages);
        $this->_actionController->view->languages = $languages;
    }
    
}