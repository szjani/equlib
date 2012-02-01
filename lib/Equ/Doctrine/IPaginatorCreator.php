<?php
namespace Equ\Doctrine;
use
  Doctrine\ORM\QueryBuilder,
  Equ\Object\Helper as ObjectHelper;

interface IPaginatorCreator {
  
  /**
   * @param ObjectHelper $objectHelper
   * @param mixed $filters
   * @param int $page
   * @param int $itemPerPage
   * @param string $sort database field name
   * @param string $order order direction (ASC/DESC)
   * @param QueryBuilder $queryBuilder
   * @return \Zend_Paginator
   */
  public function createPaginator(ObjectHelper $objectHelper, $filters, $page = 1, $itemPerPage = 10, $sort = null, $order = 'ASC', QueryBuilder $queryBuilder = null);
  
}