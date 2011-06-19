<?php
namespace Equ\Form;
use
  Zend_Form as Form,
  Zend_Form_SubForm as SubForm,
  Zend_Controller_Request_Http as Request,
  Equ\Form\Exception\InvalidArgumentException,
  Equ\Object\Helper as ObjectHelper;

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
  
  private $objectHelper;
  
  private $propertyClassMap;
  
  /**
   * @var EntityManager
   */
  protected $entityManager = null;
  
  /**
   *
   * @param Form $form
   * @param mixed $objectHelper Object or the class name of an object
   */
  public function __construct(Form $form, ObjectHelper $objectHelper, array $propertyClassMap) {
    $this->form = $form;
    $this->objectHelper = $objectHelper;
    $this->propertyClassMap = $propertyClassMap;
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
  
  public function getObject() {
    return $this->objectHelper->getObject();
  }
  
  /**
   * Validates $request on form
   * 
   * @param  Request $request
   * @return boolean
   */
  public function isValid(Request $request) {
    $valid = false;
    $namespace = $this->form->getElementsBelongTo();
    if ($this->form->getMethod() == Form::METHOD_POST) {
      $valid = $this->form->isValid($request->getPost($namespace, array()));
    } else {
      $valid = $this->form->isValid($request->getParam($namespace, array()));
    }
    if ($valid) {
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
        if (array_key_exists($field, $this->propertyClassMap)) {
          if (!empty($value)) {
            $value = $this->getEntityManager()->getReference($this->propertyClassMap[$field], $element->getValue());
            $this->objectHelper->set($field, $value);
          }
        } else {
          $this->objectHelper->set($field, $value);
        }
      } catch (\InvalidArgumentException $e) {}
    }
    
    foreach ($this->form->getSubForms() as $name => $subForm) {
      $this->subMap($name, $subForm);
    }
    return $this;
  }

  /**
   * Map subform into a relation
   * 
   * @param string  $relation
   * @param SubForm $subForm 
   */
  private function subMap($relation, SubForm $subForm) {
    $subMapper = new self($subForm, new ObjectHelper($this->propertyClassMap[$relation]), $this->propertyClassMap);
    $subMapper->map();
    $this->objectHelper->set($relation, $subMapper->getObject());
  }
}