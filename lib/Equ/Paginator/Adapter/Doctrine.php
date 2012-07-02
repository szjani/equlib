<?php
namespace Equ\Paginator\Adapter;

use Doctrine\ORM\Query;
use Zend_Paginator_Adapter_Interface;
use Doctrine\ORM\Tools\Pagination\Paginator;

class Doctrine implements Zend_Paginator_Adapter_Interface
{

    private $query;
    
    private $doctrinePaginator;
    
    public function __construct(Query $query)
    {
        $this->query = $query;
        $this->doctrinePaginator = new Paginator($query, true);
    }
    
    public function count()
    {
        return count($this->doctrinePaginator);
    }
    
    public function useArrayResult($use = true)
    {
        if ($use) {
            $this->query->setHydrationMode(Query::HYDRATE_ARRAY);
        }
    }
    
    /**
      * Gets the current page of items
      *
      * @param string $offset
      * @param string $itemCountPerPage
      * @return \ArrayIterator
      */
    public function getItems($offset, $itemCountPerPage)
    {
        $this->query
            ->setFirstResult($offset)
            ->setMaxResults($itemCountPerPage);
        return $this->doctrinePaginator->getIterator();
    }

}