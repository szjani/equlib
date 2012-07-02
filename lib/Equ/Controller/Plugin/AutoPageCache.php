<?php
namespace Equ\Controller\Plugin;
use
    Zend_Controller_Plugin_Abstract,
    Zend_Session,
    Zend_Cache_Frontend_Page,
    Zend_Controller_Request_Abstract;

class AutoPageCache extends Zend_Controller_Plugin_Abstract
{

    private $cache;

    public function __construct(Zend_Cache_Frontend_Page $cache)
    {
        $this->cache = $cache;
    }

    public function start()
    {
        Zend_Session::start();
        if (Zend_Session::getIterator()->count() == 0) {
            $this->cache->start();
        }
    }

    public function __destruct()
    {
        if (null !== $this->getResponse() && $this->getResponse()->isRedirect()) {
            $this->cache->cancel();
        }
        if (null !== $this->getResponse() && $this->getResponse()->isException()) {
            $this->cache->cancel();
        }
    }

    public function dispatchLoopShutdown()
    {
        if (0 < Zend_Session::getIterator()->count()) {
            $this->cache->cancel();
        }
    }

}
