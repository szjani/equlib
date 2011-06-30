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
  
  private $formKey = null;
  
  /**
   * array(propertyName => className, ...)
   * 
   * @var \ArrayObject
   */
  private $objectHelpers;
  
  /**
   *
   * @var \Equ\Object\Validator
   */
  private $objectValidator = null;
  
  /**
   * @var OptionFlags
   */
  private $optionFlags = null;
  
  private $formClass = '\Zend_Form';
  
  private $subFormClass = '\Zend_Form_SubForm';

  /**
   * @param mixed $object
   * @param ElementCreator\IFactory $elementCreatorFactory
   * @param \ArrayObject $objectHelpers 
   */
  public function __construct($object, ElementCreator\IFactory $elementCreatorFactory, \ArrayObject $objectHelpers = null, $key = null) {
    $this->objectHelper = new ObjectHelper($object);
    $this->formKey = $key;
    if (null === $objectHelpers) {
      $this->objectHelpers = new \ArrayObject(array(
        $this->getFormKey() => $this->objectHelper
      ));
    } else {
      $this->objectHelpers = $objectHelpers;
    }
    $this->setElementCreatorFactory($elementCreatorFactory);
  }

  /**
   * @return string
   */
  public function getFormClass() {
    return $this->formClass;
  }

  /**
   * @return string
   */
  public function getSubFormClass() {
    return $this->subFormClass;
  }
  
  /**
   * @param string $class
   * @return Builder 
   */
  public function setFormClass($class) {
    $this->formClass = $class;
    return $this;
  }
  
  /**
   * @param string $class
   * @return Builder 
   */
  public function setSubFormClass($class) {
    $this->subFormClass = $class;
    return $this;
  }

  
  /**
   * @return \ArrayObject 
   */
  public function getObjectHelpers() {
    return $this->objectHelpers;
  }
  
  /**
   * @return ObjectHelper 
   */
  public function getObjectHelper() {
    return $this->objectHelper;
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
  protected function createForeignElement($elementName, array $def, $type = 'array') {
    if ($type === null) {
      $type = 'array';
    }
    $elementCreator = $this->getElementCreatorFactory()->createCreator($type);
    $elementCreator->setOptionFlags($this->getOptionFlags());
    $select = $elementCreator->createElement($elementName);
    if ($type === 'array') {
      $select->addMultiOption('0', '');
    }
    $targetMetaData = $this->getEntityManager()->getClassMetadata($def['targetEntity']);
    foreach ($this->getEntityManager()->getRepository($def['targetEntity'])->findAll() as $entity) {
      $select->addMultiOption(
        $targetMetaData->getFieldValue(
          $entity,
          $targetMetaData->getSingleIdentifierFieldName()),
        (string)$entity
      );
    }

//    $value = $this->getEntityManager()->getClassMetadata($this->objectHelper->getType())
//      ->getFieldValue($this->objectHelper->getObject(), $elementName);
//    
//    if ($value instanceof $def['targetEntity']) {
//      $select->setValue($targetMetaData->getFieldValue($value, $targetMetaData->getSingleIdentifierFieldName()));
//    }
    return $select;
  }
  
  /**
   * @param \Zend_Form_Element $element
   * @param type $elementName
   * @param array $def 
   */
  protected function fillForeignElement(\Zend_Form_Element $element, $elementName, array $def) {
    $targetMetaData = $this->getEntityManager()->getClassMetadata($def['targetEntity']);
    $value = $this->getEntityManager()->getClassMetadata($this->objectHelper->getType())
      ->getFieldValue($this->objectHelper->getObject(), $elementName);
    
    if ($value instanceof $def['targetEntity']) {
      $element->setValue($targetMetaData->getFieldValue($value, $targetMetaData->getSingleIdentifierFieldName()));
    }
  }
  
  /**
   * Add a field
   * 
   * @param  string $field
   * @param  strig $type
   * @return Builder 
   */
  public function add($field, $type = null) {
    $fieldValue = null;
    try {
      $fieldValue = $this->objectHelper->get($field);
    } catch (\InvalidArgumentException $e) {}
    $element = null;
    try {
      $metadata = $this->getEntityManager()->getClassMetadata($this->objectHelper->getType());
      
      // $field property is a foreign-key/ID
      if ($metadata->hasAssociation($field)
        /*&& array_key_exists('isOwningSide', $metadata->associationMappings[$field])
        && $metadata->associationMappings[$field]['isOwningSide']*/) {
        
        if ($type instanceof \Zend_Form_Element) {
          $element = $type;
        } else {
          $element = $this->createForeignElement($field, $metadata->associationMappings[$field], $type);
        }
        $this->fillForeignElement($element, $field, $metadata->associationMappings[$field]);
        $this->objectHelpers[$field] = new ObjectHelper($metadata->associationMappings[$field]['targetEntity']);
      }
      else {
        if (null === $type) {
          $type = $metadata->fieldMappings[$field]['type'];
        }
        $elementCreator = $this->createElementCreator($type);
        $elementCreator->setOptionFlags($this->getOptionFlags());
        $this->addValidatorsFromObjectClass($elementCreator, $field);
        if ($type instanceof \Zend_Form_Element) {
          $element = $type;
        } else {
          if (array_key_exists($field, $metadata->fieldMappings)) {
            $element = $elementCreator->createElement($field, $metadata->fieldMappings[$field]);
          } else {
            $element = $elementCreator->createElement($field);
          }
        }
        $element->setValue((string)$fieldValue);
      }
    } catch (\Doctrine\ORM\Mapping\MappingException $e) {
      if (null === $type) {
        $type = 'text';
      }
      $elementCreator = $this->createElementCreator($type);
      $elementCreator->setOptionFlags($this->getOptionFlags());
      $this->addValidatorsFromObjectClass($elementCreator, $field);
      if ($type instanceof \Zend_Form_Element) {
        $element = $type;
      } else {
        $element = $elementCreator->createElement($field);
      }
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
  private function buildSubForm($subObject, $field, IMappedType $type, $index = '') {
    $subFormKey  = $this->getFormKey() . '-' . $field . ($index === '' ? $index : ('[' . $index . ']'));
    $subFormName = $field . ($index === '' ? $index : ('[' . $index . ']'));
    $builder     = new self($subObject, $this->getElementCreatorFactory(), $this->objectHelpers, $subFormKey);
    $builder->setEntityManager($this->getEntityManager());
    $class = $this->getSubFormClass();
    $subForm = new $class;
    $subForm->setElementsBelongTo($subFormName);
    $this->getForm()->addSubForm($subForm, $subFormName);
    $this->objectHelpers[$subFormKey] = $builder->getObjectHelper();
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
  
  private function getFormKey() {
    if (null === $this->formKey) {
      $nameArray = explode('\\', $this->objectHelper->getType());
      $this->formKey = lcfirst(array_pop($nameArray));
    }
    return $this->formKey;
  }

    /**
   * @return \Zend_Form
   */
  public function getForm() {
    if ($this->form === null) {
      $class = $this->getFormClass();
      $this->form = new $class;
      if ($this->getOptionFlags()->hasFlag(OptionFlags::ARRAY_ELEMENTS)) {
        $this->form->setElementsBelongTo($this->getFormKey());
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
      $this->mapper = new Mapper($this->getForm(), $this->getFormKey(), $this->objectHelpers);
    }
    return $this->mapper;
  }
}