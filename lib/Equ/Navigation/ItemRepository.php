<?php
namespace Equ\Navigation;

interface ItemRepository {
  
  /**
   * @return array of Item
   */
  public function getNavigationItems();
  
}