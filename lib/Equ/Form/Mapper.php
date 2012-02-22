<?php
namespace Equ\Form;
use
  Zend_Form as Form,
  Zend_Form_SubForm as SubForm,
  Zend_Controller_Request_Http as Request,
  Equ\Form\Exception\InvalidArgumentException,
  Equ\Object\Helper as ObjectHelper,
  Doctrine\ORM\EntityManager;

/**
 * Map form data into object
 *
 * @category    Equ
 * @package     Form
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @author      Szurovecz János <szjani@szjani.hu>
 */
class Mapper implements IMapper {
  
  /**
   * @var Form
   */
  private $form;
  
  /**
   * @var ObjectHelper
   */
  private $objectHelper;
  
  /**
   * @var \ArrayObject
   */
  private $objectHelpers;
  
  /**
   * @var EntityManager
   */
  protected $entityManager = null;
  
  private $key;
  
  /**
   * You should care about that if a field is existing as a key
   * in $objectHelpers than it handle it as a foreign-key/ID
   * 
   * @param Form $form
   * @param type $key
   * @param \ArrayObject $objectHelpers 
   */
  public function __construct(Form $form, $key, \ArrayObject $objectHelpers, EntityManager $em = null) {
    $this->form = $form;
    $this->objectHelper  = $objectHelpers[$key];
    $this->objectHelpers = $objectHelpers;
    $this->key = $key;
    $this->entityManager = $em;
  }
  
  /**
   * @return EntityManager $em
   */
  public function getEntityManager() {
    if (null === $this->entityManager) {
      $this->entityManager = \Zend_Controller_Front::getInstance()->getParam('bootstrap')
        ->getContainer()->get('doctrine.entitymanager');
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
   * @return object
   */
  public function getObject() {
    return $this->objectHelper->getObject();
  }
  
  /**
   * Validates $request on form
   * 
   * @param  Request $request
   * @return boolean
   */
  public function isValid(Request $request, $autoMapping = true) {
    $valid = false;
    $namespace = $this->form->getElementsBelongTo();
    if ($this->form->getMethod() == Form::METHOD_POST) {
      $valid = $this->form->isValid($request->getPost($namespace, array()));
    } else {
      $valid = $this->form->isValid($request->getParam($namespace, array()));
    }
    if ($valid && $autoMapping) {
      $this->map();
    }
    return $valid;
  }

  /**
   * Map form data to $object with relations
   * 
   * @return Mapper 
   */
  public function map() {
    /* @var $element \Zend_Form_Element */
    foreach ($this->form->getElements() as $field => $element) {
      try {
        $value = $element->getValue();
        // property $field is a relation, $value is probably an ID
        if ($this->objectHelpers->offsetExists($field)) {
          if (!empty($value)) {
            if (is_array($value)) { // multiselect, etc
              $existingFields = $this->objectHelper->get($field);
              if (null === $existingFields) {
                $existingFields = new \Doctrine\Common\Collections\ArrayCollection();
                $this->objectHelper->set($field, $existingFields);
              }
              $existingFields->clear();
              foreach ($value as $id) {
                $rel = $this->getEntityManager()->getReference(
                  $this->objectHelpers[$field]->getType(),
                  $id
                );
                $existingFields->add($rel);
              }
            } else {
              $value = $this->getEntityManager()->getReference(
                $this->objectHelpers[$field]->getType(),
                $element->getValue()
              );
              $this->objectHelper->set($field, $value);
            }
          }
        } else {
          $this->objectHelper->set($field, $value);
        }
      } catch (\InvalidArgumentException $e) {}
    }
    
    // map all subforms
    $relations = array();
    
    /* @var $subForm \Zend_Form_Subform */
    foreach ($this->form->getSubForms() as $name => $subForm) {
      $subMapper = $this->createSubMapper($name, $subForm);
      // subform collection
      if (preg_match('#^(.+)\[(.+)\]$#', $name, $matches)) {
        if (!array_key_exists($matches[1], $relations)) {
          $relations[$matches[1]] = array();
        }
        $relations[$matches[1]][$matches[2]] = $subMapper;
      } else {
        $relations[$name] = $subMapper;
      }
    }
    
    foreach ($relations as $name => $subMapper) {
      if (!is_array($subMapper)) {
        $this->objectHelper->set($name, $subMapper->getObject());
      } else {
        $existingFields = $this->objectHelper->get($name);
        if (null === $existingFields) {
          $existingFields = new \Doctrine\Common\Collections\ArrayCollection();
        }
        foreach ($subMapper as $key => $manySubMapper) {
          $existingFields[$key] = $manySubMapper->getObject();
        }
      }
    }
    return $this;
  }

  /**
   * Map subform into a relation
   * 
   * @param string  $relation
   * @param SubForm $subForm 
   */
  private function createSubMapper($relation, SubForm $subForm) {
    $subMapper = new self($subForm, $this->key . '-' . $relation, $this->objectHelpers);
    return $subMapper->map();
  }
}