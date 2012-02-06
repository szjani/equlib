<?php
namespace Equ\Controller\Plugin;
use
  Zend_Cache_Core,
  Zend_Navigation,
  Zend_Acl,
  Zend_View,
  Equ\Navigation\Item as NavigationItem,
  Equ\Navigation\ItemRepository as NavigationItemRepository,
  Symfony\Component\DependencyInjection\Container;

class Navigation extends \Zend_Controller_Plugin_Abstract {

  const KEY = 'navigation';
  
  /**
   * @var Container
   */
  private $container;
  
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
   * @var Zend_Acl 
   */
  private $acl;
  
  /**
   * @var Zend_View 
   */
  private $view;
  
  /**
   * @param Container $container
   * @param NavigationItemRepository $itemRepo
   * @param Zend_Cache_Core $cache
   * @param Zend_Acl $acl
   * @param Zend_View $view 
   */
  public function __construct(
    Container $container,
    NavigationItemRepository $itemRepo,
    Zend_Cache_Core $cache,
    Zend_Acl $acl,
    Zend_View $view
  ) {
    $this->container  = $container;
    $this->itemRepo   = $itemRepo;
    $this->cache      = $cache;
    $this->acl        = $acl;
    $this->view       = $view;
  }
  
  public function routeShutdown(\Zend_Controller_Request_Abstract $request) {
    if ($request->getParam('format') == 'ajax') {
      return;
    }
    $navigation = $this->container->get(self::KEY);
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
    
    $navigationHelper = $this->view->getHelper('navigation');
    $navigationHelper
      ->setContainer($navigation)
      ->setAcl($this->acl)
      ->setRole(\Zend_Auth::getInstance()->getIdentity());
  }

}