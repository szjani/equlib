<?php
namespace Equ\Controller\Action\Helper;
use
    Zend_Controller_Router_Rewrite,
    Zend_Controller_Action_Helper_Abstract;

/**
  * Use this helper before you redirect the user. It encodes and puts the current URL into the router.
  * If there is such parameter in request object and client has sent a POST request,
  * client is going to be redirected.
  * Usefull for login action: after successfully logging in the user is going to be redirectedú
  * to the originally requested page.
  *
  * @category    Equ
  * @package     Controller
  * @subpackage  Action\Helper
  * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
  * @author      Szurovecz János <szjani@szjani.hu>
  * @see         ErrorController::errorAction()
  */
class RedirectHereAfterPost extends Zend_Controller_Action_Helper_Abstract
{

    const DEFAULT_KEY = 'redirect';

    /**
      * @var boolean
      */
    protected $hasUrl = false;

    /**
      * @var boolean
      */
    protected $autoRedirect = true;

    /**
      * @var string
      */
    private $key = self::DEFAULT_KEY;

    /**
      * @param type $key
      * @return \Equ\Controller\Action\Helper\RedirectHereAfterPost
      */
    public function setKey($key)
    {
        $this->key = $key;
        return $this;
    }

    /**
      * @return string
      */
    public function getKey()
    {
        return $this->key;
    }

    public function direct()
    {
        $this->saveCurrentUrl();
    }

    /**
      * @return boolean
      */
    public function hasUrl()
    {
        return $this->hasUrl;
    }

    /**
      * @param boolean $autoRedirect
      * @return \Equ\Controller\Action\Helper\RedirectHereAfterPost
      */
    public function setAutoRedirect($autoRedirect = true)
    {
        $this->autoRedirect = $autoRedirect;
        return $this;
    }

    public function saveCurrentUrl()
    {
        $router = $this->getActionController()->getFrontController()->getRouter();
        if ($router instanceof Zend_Controller_Router_Rewrite) {
            $router->setGlobalParam($this->getKey(), base64_encode($this->getRequest()->getRequestUri()));
            $this->hasUrl = true;
        }
    }

    public function preDispatch()
    {
        $encodedRedirectUrl = $this->getRequest()->getParam($this->getKey());
        $this->hasUrl = isset($encodedRedirectUrl);
    }

    public function postDispatch()
    {
        if ($this->autoRedirect && $this->getRequest()->isPost()) {
            $encodedRedirectUrl = $this->getRequest()->getParam($this->getKey());
            if (isset($encodedRedirectUrl)) {
                $this->getActionController()->getHelper('redirector')->gotoUrlAndExit(base64_decode($encodedRedirectUrl));
            }
        }
    }

}
