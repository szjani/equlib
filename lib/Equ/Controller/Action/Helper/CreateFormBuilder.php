<?php
namespace Equ\Controller\Action\Helper;
use
  Zend_Controller_Action_Helper_Abstract,
  Equ\Form\Builder as FormBuilder,
  Equ\Form\IMappedType,
  Equ\Form\ElementCreator\IFactory as ElementCreatorFactory,
  Doctrine\ORM\EntityManager;

/**
 * It creates a FormBuilder object which is usefull for generating Zend_Form object
 * based on the given Equ\Form\IMappedType object. The second parameter of direct() method
 * has to be an entity or the class name of an entity.
 * In the first case the generated form elements will be filled with the values of the entity,
 * in the second case the form will be empty. You can define a default ElementCreatorFactory object
 * which can be overridden by a parameter in direct() method.
 * The generated form layout will depend on this factory object.
 *
 * @category    Equ
 * @package     Controller
 * @subpackage  Action\Helper
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @author      Szurovecz János <szjani@szjani.hu>
 */
class CreateFormBuilder extends Zend_Controller_Action_Helper_Abstract {
  
  /**
   * @var ElementCreatorFactory 
   */
  protected $defaultElementCreatorFactory;
  
  /**
   * @var string
   */
  protected $defaultFormClass;
  
  /**
   * @var string
   */
  protected $defaultSubFormClass;
  
  /**
   * @var EntityManager 
   */
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