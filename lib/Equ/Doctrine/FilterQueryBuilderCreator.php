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
      if ($relatedObject instanceof \Doctrine\ORM\Proxy\Proxy) {
        $this->entityManager->refresh($relatedObject);
      }
      $subMetadata = $this->entityManager->getClassMetadata(get_class($relatedObject));
      $valueIds[]  = $subMetadata->getFieldValue($relatedObject, $subMetadata->getSingleIdentifierFieldName());
    }
    $queryBuilder->add('where', $queryBuilder->expr()->in("m." . $field, $valueIds));
  }
  
  protected function addAndWhereForObject(QueryBuilder $queryBuilder, $field, $value) {
    $queryBuilder
      ->andWhere("m.{$field} = :$field")
      ->setParameter($field, $value);
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
   * @param  ObjectHelper $objectHelper
   * @param  mixed  $filters
   * @param  string $sort
   * @param  string $order
   * @return QueryBuilder
   */
  public function create(ObjectHelper $objectHelper, $filters, $sort = null, $order = 'ASC') {
    $entityName = $objectHelper->getType();
    $order = (strtoupper($order) == 'ASC' ? 'ASC' : 'DESC');
    $metadata = $this->entityManager->getClassMetadata($entityName);

    $queryBuilder = new QueryBuilder($this->entityManager);
    $queryBuilder
      ->select('m')
      ->from($entityName, 'm');

    foreach ($filters as $property => $value) {
      try {
        if (!in_array($value, array('', null), true) && array_key_exists($property, $metadata->fieldMappings)) {
          $this->addAndWhere($queryBuilder, $property, $objectHelper->get($property));
        }
        if (array_key_exists($property, $metadata->associationMappings)) {
          $value = $objectHelper->get($property);
          $this->addAndWhere($queryBuilder, $metadata->associationMappings[$property]['fieldName'], $value);
        }
      } catch (\InvalidArgumentException $e) {}
    }

    if ($sort !== null) {
      if (array_key_exists($sort, $metadata->fieldMappings)) {
        $queryBuilder->orderBy('m.' . $sort, $order);
      }
      elseif (array_key_exists($sort, $metadata->associationMappings)) {
        $targetEntity = $metadata->associationMappings[$sort]['targetEntity'];
        if (method_exists($targetEntity, 'getSortField')) {
          $queryBuilder
            ->select("m, $sort")
            ->leftJoin("m.$sort", $sort)
            ->orderBy("$sort." . $targetEntity::getSortField(), $order);
        }
      }
    }

    return $queryBuilder;
  }
  
}