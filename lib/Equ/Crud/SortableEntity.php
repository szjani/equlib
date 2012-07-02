<?php
namespace Equ\Crud;

/**
  * @category    Equ
  * @package     Crud
  * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
  * @author      Szurovecz János <szjani@szjani.hu>
  */
interface SortableEntity
{

    /**
      * @return string Name of the default sortable field
      */
    public static function getSortField();
    
}