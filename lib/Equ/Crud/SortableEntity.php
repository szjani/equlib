<?php
namespace Equ\Crud;

interface SortableEntity {
  
  public function __toString();
  
  public static function getSortField();
  
}