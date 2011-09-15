<?php
namespace Equ\Navigation;

interface Item {
  
  /**
   * @return Item
   */
  public function getParent();
  
  /**
   * @return \Zend_Navigation_Page
   */
  public function getNavigationPage();
  
}