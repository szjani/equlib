<?php
namespace Equ\Doctrine;

use
  Equ\Paginator\Adapter\Doctrine as DoctrinePaginatorAdapter,
  Doctrine\ORM\QueryBuilder,
  Zend_Paginator,
  Equ\Object\Helper as ObjectHelper;

class PaginatorCreator implements IPaginatorCreator {

  /**
   * @var IQueryBuilderCreator 
   */
  private $queryBuilderCreator;
  
  /**
   * @param IQueryBuilderCreator $queryBuilderCreator 
   */
  public function __construct(IQueryBuilderCreator $queryBuilderCreator) {
    $this->queryBuilderCreator = $queryBuilderCreator;
  }
  
  /**
   * @param ObjectHelper $objectHelper
   * @param mixed $filters
   * @param int $page
   * @param int $itemPerPage
   * @param string $sort database field name
   * @param string $order order direction (ASC/DESC)
   * @param QueryBuilder $queryBuilder
   * @param boolean $useArrayResult
   * @return Zend_Paginator
   */
  public function createPaginator(ObjectHelper $objectHelper, $filters, $page = 1, $itemPerPage = 10, $sort = null, $order = 'ASC', QueryBuilder $queryBuilder = null, $useArrayResult = false) {
    if ($queryBuilder === null) {
      $queryBuilder = $this->queryBuilderCreator->create($objectHelper, $filters, $sort, $order);
    }
//    var_dump($queryBuilder->getQuery()->getDQL(), $queryBuilder->getQuery()->getParameters());
    $adapter = new DoctrinePaginatorAdapter($queryBuilder->getQuery());
    $adapter->useArrayResult($useArrayResult);
    $paginator = new Zend_Paginator($adapter);
    $paginator
      ->setCurrentPageNumber($page)
      ->setItemCountPerPage($itemPerPage);
    return $paginator;
  }
  
}
