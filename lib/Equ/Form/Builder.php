<?php
namespace Equ\Form;

use
  Doctrine\ORM\EntityManager,
  Equ\Object\Helper as ObjectHelper;

/**
 * Build form from object
 *
 * @category    Equ
 * @package     Form
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @author      Szurovecz János <szjani@szjani.hu>
 */
class Builder implements IBuilder {
  
  /**
   * @var ObjectHelper
   */
  private $objectHelper;
  
  /**
   * @var ElementCreator\IFactory
   */
  private $elementCreatorFactory = null;
  
  /**
   * @var EntityManager
   */
  protected $entityManager = null;
  
  /**
   * You can map form values to object with this mapper
   * 
   * @var Mapper
   */
  protected $mapper = null;
  
  /**
   *
   * @var \Zend_Form
   */
  private $form = null;
  
  /**
   * array(propertyName => className, ...)
   * 
   * @var array
   */
  private $propertyClassMap = array();
  
  /**
   *
   * @var \Equ\Object\Validator
   */
  private $objectValidator = null;
  
  /**
   * @var OptionFlags
   */
  private $optionFlags = null;

  /**
   * @param mixed $object Object or type
   */
  public function __construct($object, ElementCreator\IFactory $elementCreatorFactory) {
    $this->objectHelper = new ObjectHelper($object);
    $this->setElementCreatorFactory($elementCreatorFactory);
  }
  
  /**
   * @return OptionFlags
   */
  public function getOptionFlags() {
    if (null === $this->optionFlags) {
      $this->setOptionFlags(new OptionFlags(OptionFlags::ALL));
    }
    return $this->optionFlags;
  }

  /**
   * @param OptionFlags $flags
   * @return Builder 
   */
  public function setOptionFlags(OptionFlags $flags) {
    $this->optionFlags = $flags;
    return $this;
  }

  
  /**
   * @return EntityManager $em
   */
  public function getEntityManager() {
    if (null === $this->entityManager) {
      $this->entityManager = \Zend_Controller_Front::getInstance()->getParam('bootstrap')->getContainer()->get('doctrine.entitymanager');
    }
    return $this->entityManager;
  }
  
  /**
   * @param  EntityManager $em
   * @return Builder 
   */
  public function setEntityManager(EntityManager $em) {
    $this->entityManager = $em;
    return $this;
  }
  
  /**
   * @return ElementCreator\IFactory
   */
  public function getElementCreatorFactory() {
    return $this->elementCreatorFactory;
  }
  
  /**
   * @param  ElementCreator\IFactory $factory
   * @return Builder
   */
  public function setElementCreatorFactory(ElementCreator\IFactory $factory) {
    $this->elementCreatorFactory = $factory;
    return $this;
  }
  
  /**
   * @param  string $type
   * @return AbstractCreator
   */
  protected function createElementCreator($type) {
    return $this->getElementCreatorFactory()->createCreator($type);
  }
  
  /**
   * @param string $elementName
   * @param array  $def
   * @return \Zend_Form_Element
   */
  protected function createForeignElement($elementName, array $def) {
    $elementCreator = $this->getElementCreatorFactory()->createArrayCreator();
    $elementCreator->setOptionFlags($this->getOptionFlags());
    $select = $elementCreator->createElement($elementName);
    $select->addMultiOption('0', '');
    $targetMetaData = $this->getEntityManager()->getClassMetadata($def['targetEntity']);
    foreach ($this->getEntityManager()->getRepository($def['targetEntity'])->findAll() as $entity) {
      $select->addMultiOption(
        $targetMetaData->getFieldValue(
          $entity,
          $targetMetaData->getSingleIdentifierFieldName()),
        (string)$entity
      );
    }

    $value = $this->getEntityManager()->getClassMetadata($this->objectHelper->getType())
      ->getFieldValue($this->objectHelper->getObject(), $elementName);
    
    if ($value instanceof $def['targetEntity']) {
      $select->setValue($targetMetaData->getFieldValue($value, $targetMetaData->getSingleIdentifierFieldName()));
    }
    return $select;
  }
  
  /**
   * Add a field
   * 
   * @param  string $field
   * @param  strig $type
   * @return Builder 
   */
  public function add($field, $type = null) {
    if (null === $type) {
      $type = 'text';
    }
    $fieldValue = null;
    try {
      $fieldValue = $this->objectHelper->get($field);
    } catch (\InvalidArgumentException $e) {}
    $element = null;
    try {
      $metadata = $this->getEntityManager()->getClassMetadata($this->objectHelper->getType());
      if ($metadata->hasAssociation($field)
        && array_key_exists('isOwningSide', $metadata->associationMappings[$field])
        && $metadata->associationMappings[$field]['isOwningSide']) {
        
        $element = $this->createForeignElement($field, $metadata->associationMappings[$field]);
        $this->propertyClassMap[$field] = $metadata->associationMappings[$field]['targetEntity'];
      }
      else {
        $elementCreator = $this->createElementCreator($type);
        $elementCreator->setOptionFlags($this->getOptionFlags());
        $this->addValidatorsFromObjectClass($elementCreator, $field);
        if (array_key_exists($field, $metadata->fieldMappings)) {
          $element = $elementCreator->createElement($field, $metadata->fieldMappings[$field]);
        } else {
          $element = $elementCreator->createElement($field);
        }
        $element->setValue((string)$fieldValue);
      }
    } catch (\Doctrine\ORM\Mapping\MappingException $e) {
      $elementCreator = $this->createElementCreator($type);
      $elementCreator->setOptionFlags($this->getOptionFlags());
      $this->addValidatorsFromObjectClass($elementCreator, $field);
      $element = $elementCreator->createElement($field);
      $element->setValue((string)$fieldValue);
    }
    $this->getForm()->addElement($element);
    return $this;
  }
  
  private function addValidatorsFromObjectClass($elementCreator, $field) {
    try {
      $elementCreator->setValidator($this->getObjectValidator()->getFieldValidate($field));
    } catch (\Equ\Object\Exception\InvalidArgumentException $e) {}
  }
  
  /**
   * @return \Equ\Object\Validator 
   */
  private function getObjectValidator() {
    if (null == $this->objectValidator) {
      $this->objectValidator = new \Equ\Object\Validator($this->objectHelper->getType(), $this->entityManager);
    }
    return $this->objectValidator;
  }

  /**
   * Add a subform
   * 
   * @param  string $field
   * @param  IMappedType $type
   * @param  boolean $collection
   * @return Builder 
   */
  public function addSub($field, IMappedType $type, $collection = false) {
    $fieldValue = $this->objectHelper->get($field);
    if (null === $fieldValue) {
      if ($collection) {
        throw new Exception\InvalidArgumentException("'$field' has to be not-empty if use \$collection = true");
      }
      $fieldValue = $type->getObjectClass();
    }
    
    if ($collection) {
      if (!is_array($fieldValue) && !($fieldValue instanceof \Traversable)) {
        throw new Exception\InvalidArgumentException("'$field' has to be a Traversable object or an array()");
      }
      $i = 0;
      foreach ($fieldValue as $subObject) {
        $this->buildSubForm($subObject, $field, $type, $i++);
      }
    } else {
      $this->buildSubForm($fieldValue, $field, $type);
    }
    return $this;
  }
  
  /**
   *
   * @param mixed $subObject
   * @param string $field
   * @param IMappedType $type
   * @param int $index 
   */
  private function buildSubForm($subObject, $field, IMappedType $type, $index = null) {
    $builder = new self($subObject, $this->getElementCreatorFactory());
    $subForm = new \Zend_Form_SubForm();
    if (null !== $index) {
      $this->getForm()->addSubForm($subForm, $field . $index);
      $this->propertyClassMap[$field . $index] = $type->getObjectClass();
      $subForm->setElementsBelongTo($field . '[' . $index . ']');
    } else {
      $this->getForm()->addSubForm($subForm, $field);
      $this->propertyClassMap[$field] = $type->getObjectClass();
    }
    $builder->setForm($subForm);
    $type->buildForm($builder);
  }

  /**
   * @param  \Zend_Form $form
   * @return Builder
   */
  public function setForm(\Zend_Form $form) {
    $this->form = $form;
    return $this;
  }

    /**
   * @return \Zend_Form
   */
  public function getForm() {
    if ($this->form === null) {
      $this->form = new \Zend_Form();
      if ($this->optionFlags->hasFlag(OptionFlags::ARRAY_ELEMENTS)) {
        $nameArray = explode('\\', $this->objectHelper->getType());
        $this->form->setElementsBelongTo(lcfirst(array_pop($nameArray)));
      }
      $submit = $this->getElementCreatorFactory()->createSubmitCreator()->createElement('OK');
      $submit->setOrder('999');
      $this->form->addElement($submit);
    }
    return $this->form;
  }

  /**
   * Retrieves a mapper to sync datas from form to object
   * 
   * @return Mapper
   */
  public function getMapper() {
    if (null === $this->mapper) {
      $this->mapper = new Mapper($this->getForm(), $this->objectHelper, $this->propertyClassMap);
    }
    return $this->mapper;
  }
}