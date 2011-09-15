<?php
namespace Equ\Controller\Plugin;
use
  Zend_Cache_Core,
  Zend_Navigation,
  Equ\Navigation\Item as NavigationItem,
  Equ\Navigation\ItemRepository as NavigationItemRepository;

class Navigation extends \Zend_Controller_Plugin_Abstract {

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
   * @param Zend_Navigation $navigation
   * @param NavigationItemRepository $itemRepo
   * @param Zend_Cache_Core $cache 
   */
  public function __construct(Zend_Navigation $navigation, NavigationItemRepository $itemRepo, Zend_Cache_Core $cache) {
    $this->navigation = $navigation;
    $this->itemRepo   = $itemRepo;
    $this->cache      = $cache;
  }
  
  public function routeShutdown(\Zend_Controller_Request_Abstract $request) {
    if ($request->getParam('format') == 'ajax') {
      return;
    }
    /* @var $item NavigationItem */
    foreach ($this->itemRepo->getNavigationItems() as $item) {
      if (false !== strpos((string)$item->getNavigationPage()->getResource(), 'update')) {
        $item->getNavigationPage()->setVisible(false);
      }
      $parentNav = $item->getParent() ? $item->getParent()->getNavigationPage() : $this->navigation;
      $parentNav->addPage($item->getNavigationPage());
    }
    $this->cache->save($this->navigation, 'navigation');
  }

}