<?php
namespace Equ\Doctrine;

use
  Equ\Paginator\Adapter\Doctrine as DoctrinePaginatorAdapter,
  Doctrine\ORM\QueryBuilder,
  Zend_Paginator;

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
   * @param mixed $filters
   * @param int $page
   * @param int $itemPerPage
   * @param string $sort database field name
   * @param string $order order direction (ASC/DESC)
   * @param QueryBuilder $queryBuilder
   * @return Zend_Paginator
   */
  public function createPaginator($filters, $page = 1, $itemPerPage = 10, $sort = null, $order = 'ASC', QueryBuilder $queryBuilder = null) {
    if ($queryBuilder === null) {
      $queryBuilder = $this->queryBuilderCreator->create($filters, $sort, $order);
    }
//    var_dump($queryBuilder->getQuery()->getDQL(), $queryBuilder->getQuery()->getParameters());
    $paginator = new Zend_Paginator(new DoctrinePaginatorAdapter($queryBuilder->getQuery()));
    $paginator
      ->setCurrentPageNumber($page)
      ->setItemCountPerPage($itemPerPage);
    return $paginator;
  }
  
}
