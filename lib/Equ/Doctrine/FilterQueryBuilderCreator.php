<?php
namespace Equ\Doctrine;
use
  Equ\Object\Helper as ObjectHelper,
  Doctrine\ORM\EntityManager,
  Doctrine\ORM\Query,
  Doctrine\ORM\QueryBuilder,
  Doctrine\ORM\Proxy\Proxy,
  Doctrine\Common\Collections\ArrayCollection;

class FilterQueryBuilderCreator implements IQueryBuilderCreator {

  /**
   * @var EntityManager
   */
  protected $entityManager;
  
  /**
   * @param EntityManager $em 
   */
  public function __construct(EntityManager $em) {
    $this->entityManager = $em;
  }
  
  protected function addAndWhereForArray(QueryBuilder $queryBuilder, $field, $value) {
    $queryBuilder
      ->andWhere("m.{$field} IN (:{$field})")
      ->setParameter($field, implode(',', $value));
  }
  
  protected function addAndWhereForNumeric(QueryBuilder $queryBuilder, $field, $value) {
    $queryBuilder
      ->andWhere("m.{$field} = :{$field}")
      ->setParameter($field, $value);
  }
  
  protected function addAndWhereForBoolean(QueryBuilder $queryBuilder, $field, $value) {
    $queryBuilder
      ->andWhere("m.{$field} = :{$field}")
      ->setParameter($field, $value);
  }
  
  protected function addAndWhereForString(QueryBuilder $queryBuilder, $field, $value) {
    $queryBuilder
      ->andWhere("m.{$field} LIKE :{$field}")
      ->setParameter($field, '%'.$value.'%');
  }
  
  protected function addAndWhereForCollection(QueryBuilder $queryBuilder, $field, ArrayCollection $value) {
    $valueIds = array();
    foreach ($value as $relatedObject) {
      $subMetadata = $this->entityManager->getClassMetadata(get_class($relatedObject));
      $valueIds[]  = $subMetadata->getFieldValue($relatedObject, $subMetadata->getSingleIdentifierFieldName());
    }
    $queryBuilder->add('where', $queryBuilder->expr()->in("m." . $field, $valueIds));
  }
  
  protected function addAndWhereForObject(QueryBuilder $queryBuilder, $field, $value) {
    $subMetadata = $this->entityManager->getClassMetadata(get_class($value));
    $valueId = $subMetadata->getFieldValue($value, $subMetadata->getSingleIdentifierFieldName());
    $queryBuilder
      ->andWhere("m.{$field} = :$field")
      ->setParameter($field, $valueId);
  }
  
  
  /**
   * @param QueryBuilder $queryBuilder
   * @param string $field
   * @param mixed  $value 
   */
  protected function addAndWhere(QueryBuilder $queryBuilder, $field, $value) {
    $type = gettype($value);
    switch ($type) {
      case 'array':
        $this->addAndWhereForArray($queryBuilder, $field, $value);
        break;
      case 'double':
      case 'integer':
        $this->addAndWhereForNumeric($queryBuilder, $field, $value);
        break;
      case 'boolean':
        $this->addAndWhereForBoolean($queryBuilder, $field, $value);
        break;
      case 'string':
        $this->addAndWhereForString($queryBuilder, $field, $value);
        break;
      case 'object':
        if ($value instanceof ArrayCollection) {
          $this->addAndWhereForCollection($queryBuilder, $field, $value);
        } else {
          $this->addAndWhereForObject($queryBuilder, $field, $value);
        }
        break;
      default:
        throw new Exception\InvalidArgumentException("'$type' is an unsupported type for create filter query");
    }
  }

  /**
   * @param  object $object
   * @param  string $sort
   * @param  string $order
   * @return QueryBuilder
   */
  public function create($object, $sort = null, $order = 'ASC') {
    $objectHelper = new ObjectHelper($object);
    
    $metadata = $this->entityManager->getClassMetadata($objectHelper->getType());

    $queryBuilder = new QueryBuilder($this->entityManager);
    $queryBuilder
      ->select('m')
      ->from($objectHelper->getType(), 'm');

    foreach ($metadata->fieldMappings as $field => $map) {
      try {
        $value = $objectHelper->get($field);
        if (!in_array($value, array('', null), true)) {
          $this->addAndWhere($queryBuilder, $map['fieldName'], $value);
        }
      } catch (\InvalidArgumentException $e) {}
    }
    
    foreach ($metadata->associationMappings as $field => $map) {
      try {
        $value = $objectHelper->get($field);
        $this->addAndWhere($queryBuilder, $map['fieldName'], $value);
      } catch (\InvalidArgumentException $e) {}
    }

    if ($sort !== null) {
      $queryBuilder->orderBy('m.' . $sort, $order == 'ASC' ? 'ASC' : 'DESC');
    }

    return $queryBuilder;
  }
  
}