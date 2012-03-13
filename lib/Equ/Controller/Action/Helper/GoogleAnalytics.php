<?php
namespace Equ\Controller\Action\Helper;
use Zend_Controller_Action_Helper_Abstract;

class GoogleAnalytics extends Zend_Controller_Action_Helper_Abstract {
  
  protected $code;
  
  protected $enabled;
  
  /**
   * @param string $code
   * @param boolean $enabled 
   */
  public function __construct($code, $enabled = true) {
    $this->code    = $code;
    $this->enabled = $enabled;
  }
  
  public function preDispatch() {
    if ($this->enabled) {
      $view = $this->getActionController()->view;
      $view->headScript()->appendScript("
        var _gaq = _gaq || [];
        _gaq.push(['_setAccount', '{$this->code}']);
        _gaq.push(['_trackPageview']);

        (function() {
          var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
          ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
          var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
        })();
      ");
    }
  }
  
}