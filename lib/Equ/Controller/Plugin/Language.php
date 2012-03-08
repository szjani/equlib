<?php
namespace Equ\Controller\Plugin;
use Equ\Controller\Exception\UnexpectedValueException;

class Language extends \Zend_Controller_Plugin_Abstract {

  private $translate;
  
  private $locale;
  
  public function __construct(\Zend_Translate $translate, \Zend_Locale $locale) {
    $this->translate = $translate;
    $this->locale    = $locale;
  }
  
  /**
   * (non-PHPdoc)
   * @see \Zend_Controller_Plugin_Abstract::routeStartup()
   */
  public function routeStartup(\Zend_Controller_Request_Abstract $request) {
    $locale = $this->locale;
    $front  = \Zend_Controller_Front::getInstance();
    $router = $front->getRouter();

    // lang route
    $routeLang = new \Zend_Controller_Router_Route(
      ':lang',
      array(
        'lang' => $locale->getLanguage(),
      ),
      array('lang' => '[a-z]{2}(_[a-z]{2})?')
    );

    // default routes
    $router->addDefaultRoutes();

    // create chain routes
    foreach ($router->getRoutes() as $name => $route) {
      $chain = new \Zend_Controller_Router_Route_Chain();
      $router->addRoute(
        $name . 'lang',
        $chain->chain($routeLang)->chain($route)
      );
    }

    // add simple lang route
//    $router->addRoute('lang', $routeLang);
  }

  /**
   * (non-PHPdoc)
   * @see \Zend_Controller_Plugin_Abstract::routeShutdown()
   */
  public function routeShutdown(\Zend_Controller_Request_Abstract $request) {
    $origLang  = $request->getParam('lang');
    $pos       = strpos($origLang, '_');
    $lang      = $pos ? substr($origLang, 0, $pos) : $origLang;
    $translate = $this->translate;

    // Change language if available
    if ($translate->isAvailable($origLang)) {
      $this->locale->setLocale($origLang);
      $this->translate->setLocale($origLang);
      $this->getResponse()->setHeader('Content-Language', $origLang);
    } elseif ($translate->isAvailable($lang)) {
      $this->locale->setLocale($origLang);
      $this->translate->setLocale($lang);
      $this->getResponse()->setHeader('Content-Language', $lang);
    } else {
      // Otherwise get default language
      $locale = $translate->getLocale();
      $lang = ($locale instanceof \Zend_Locale) ? $locale->getLanguage() : $locale;

      // there is az invalid lang param in request
      if (isset($origLang)) {
        throw new UnexpectedValueException("Invalid language '$origLang'");
      }
    }
    
    $router = \Zend_Controller_Front::getInstance()->getRouter();
    /* @var $router Zend_Controller_Router_Rewrite */
    if (false !== \strpos($router->getCurrentRouteName(), 'lang')) {
      $router->setGlobalParam('lang', $origLang);
    }
    $request->setParam('lang', $origLang);
  }

}