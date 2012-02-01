<?php
namespace Equ\Doctrine;
use
  Doctrine\ORM\QueryBuilder,
  Equ\Object\Helper as ObjectHelper;

interface IQueryBuilderCreator {
  
  /**
   * @param  ObjectHelper $objectHelper
   * @param  mixed  $filters
   * @param  string $sort
   * @param  string $order
   * @return QueryBuilder
   */
  public function create(ObjectHelper $objectHelper, $filters, $sort = null, $order = 'ASC');
}
