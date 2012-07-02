<?php
namespace Equ\Controller\Plugin;
use
    Zend_Cache_Core,
    Zend_Navigation,
    Equ\Navigation\Item as NavigationItem,
    Equ\Navigation\ItemRepository as NavigationItemRepository,
    Zend_Controller_Request_Abstract,
    Zend_Controller_Plugin_Abstract;

class Navigation extends Zend_Controller_Plugin_Abstract
{

    const KEY = 'navigation';

    /**
      * @var Zend_Navigation
      */
    private $navigation;

    /**
      *
      * @var NavigationItemRepository
      */
    private $itemRepo;

    /**
      *
      * @var Zend_Cache_Core
      */
    private $cache;

    /**
      * @var boolean
      */
    private $autoDisableForAjaxRequest = true;

    /**
      * @param Zend_Navigation $navigation
      * @param NavigationItemRepository $itemRepo
      * @param Zend_Cache_Core $cache
      */
    public function __construct(Zend_Navigation $navigation, NavigationItemRepository $itemRepo, Zend_Cache_Core $cache)
    {
        $this->navigation  = $navigation;
        $this->itemRepo    = $itemRepo;
        $this->cache       = $cache;
    }

    /**
      *
      * @param boolean $disable
      * @return Navigation
      */
    public function setAutoDisableForAjaxRequest($disable = true)
    {
        $this->autoDisableForAjaxRequest = $disable;
        return $this;
    }

    /**
      * @return boolean
      */
    public function isAutoDisableForAjaxRequest()
    {
        return $this->autoDisableForAjaxRequest;
    }

    /**
      * @param Zend_Controller_Request_Abstract $request
      * @return void
      */
    public function routeShutdown(Zend_Controller_Request_Abstract $request)
    {
        if ($this->autoDisableForAjaxRequest && $request->isXmlHttpRequest()) {
            return;
        }
        $navigation = $this->navigation;
        /* @var $navigation \Zend_Navigation_Container */
        $navigationCache = $this->cache->load(self::KEY);
        if (false !== $navigationCache) {
            $navigation->setPages($navigationCache);
        } else {
            /* @var $item NavigationItem */
            foreach ($this->itemRepo->getNavigationItems() as $item) {
                if (false !== strpos((string)$item->getNavigationPage()->getResource(), 'update')) {
                    $item->getNavigationPage()->setVisible(false);
                }
                $parentNav = $item->getParent() ? $item->getParent()->getNavigationPage() : $navigation;
                $parentNav->addPage($item->getNavigationPage());
            }
            $this->cache->save($navigation->getPages(), self::KEY);
        }
    }

}