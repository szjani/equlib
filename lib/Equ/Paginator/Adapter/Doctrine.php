<?php
namespace Equ\Paginator\Adapter;

use DoctrineExtensions\Paginate\PaginationAdapter;
use Doctrine\ORM\Query;

class Doctrine extends PaginationAdapter {

  /**
   * Gets the current page of items
   *
   * @param string $offset
   * @param string $itemCountPerPage
   * @return void
   * @author David Abdemoulaie
   */
  public function getItems($offset, $itemCountPerPage) {
    $ids = $this->createLimitSubquery($offset, $itemCountPerPage)->getScalarResult();
    $ids = array_map(
      function ($e) {
        return current($e);
      },
      $ids
    );
    $ids = array_unique($ids);
    $ids = array_values($ids);
    if ($this->arrayResult) {
      return $this->createWhereInQuery($ids)->getArrayResult();
    } else {
      return $this->createWhereInQuery($ids)->getResult();
    }
  }

}