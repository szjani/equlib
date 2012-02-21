<?php
namespace Equ\Controller\Action\Helper;
use
  Equ\Form\Builder as FormBuilder,
  Equ\Form\IMappedType,
  Equ\Form\ElementCreator\IFactory as ElementCreatorFactory,
  Doctrine\ORM\EntityManager;

class CreateFormBuilder extends \Zend_Controller_Action_Helper_Abstract {
  
  protected $defaultElementCreatorFactory;
  
  protected $defaultFormClass;
  
  protected $defaultSubFormClass;
  
  protected $entityManager;
  
  /**
   * @param EntityManager $em
   * @param ElementCreatorFactory $factory
   * @param string $formClass
   * @param string $subFormClass 
   */
  public function __construct(EntityManager $em, ElementCreatorFactory $factory, $formClass, $subFormClass) {
    $this->defaultElementCreatorFactory = $factory;
    $this->defaultFormClass             = $formClass;
    $this->defaultSubFormClass          = $subFormClass;
    $this->entityManager                = $em;
  }
  
  /**
   * @param IMappedType $mappedType
   * @param object $object
   * @param ElementCreatorFactory $factory
   * @param string $formClass
   * @param string $subFormClass
   * @return FormBuilder 
   */
  public function direct(IMappedType $mappedType, $object, ElementCreatorFactory $factory = null, $formClass = null, $subFormClass = null) {
    return $this->create($mappedType, $object, $factory, $formClass, $subFormClass);
  }
  
  /**
   * @param IMappedType $mappedType
   * @param object $object
   * @param ElementCreatorFactory $factory
   * @param string $formClass
   * @param string $subFormClass
   * @return FormBuilder 
   */
  public function create(IMappedType $mappedType, $object, ElementCreatorFactory $factory = null, $formClass = null, $subFormClass = null) {
    $factory      = $factory      ?: $this->defaultElementCreatorFactory;
    $formClass    = $formClass    ?: $this->defaultFormClass;
    $subFormClass = $subFormClass ?: $this->defaultSubFormClass;
    $formBuilder  = new FormBuilder($object, $this->entityManager, $factory);
    $formBuilder
      ->setSubFormClass($subFormClass)
      ->setFormClass($formClass);
    $mappedType->buildForm($formBuilder);
    return $formBuilder;
  }
  
}