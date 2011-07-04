<?php
namespace Equ\Controller\Action\Helper;
use
  Equ\Form\Builder as FormBuilder,
  Equ\Form\IMappedType,
  Equ\Form\ElementCreator\IFactory as ElementCreatorFactory;

class CreateFormBuilder extends \Zend_Controller_Action_Helper_Abstract {
  
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
    $container    = $this->getActionController()->getHelper('serviceContainer')->getContainer();
    $factory      = $factory      ?: $container->get('form.elementcreator.factory');
    $formClass    = $formClass    ?: $container->getParameter('form.formClass');
    $subFormClass = $subFormClass ?: $container->getParameter('form.subFormClass');
    $formBuilder  = new FormBuilder($object, $factory);
    $formBuilder
      ->setSubFormClass($subFormClass)
      ->setFormClass($formClass);
    $mappedType->buildForm($formBuilder);
    return $formBuilder;
  }
  
}