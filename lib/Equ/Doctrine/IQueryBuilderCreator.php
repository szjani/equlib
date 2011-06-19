<?php
namespace Equ\Doctrine;
use Doctrine\ORM\QueryBuilder;

interface IQueryBuilderCreator {
  
  /**
   * @param  object $object
   * @param  string $sort
   * @param  string $order
   * @return QueryBuilder
   */
  public function create($object, $sort = null, $order = 'ASC');
}
