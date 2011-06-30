<?php
namespace Equ\Controller\Action\Helper;
use
  Equ\Form\Builder as FormBuilder,
  Equ\Form\IMappedType;

class CreateFormBuilder extends \Zend_Controller_Action_Helper_Abstract {
  
  public function direct(IMappedType $mappedType, $object) {
    return $this->create($mappedType, $object);
  }
  
  public function create(IMappedType $mappedType, $object) {
    $container = $this->getActionController()->getHelper('serviceContainer')->getContainer();
    $formBuilder = new FormBuilder($object, $container->get('form.elementcreator.factory'));
    $formBuilder
      ->setSubFormClass($container->getParameter('form.subFormClass'))
      ->setFormClass($container->getParameter('form.formClass'));
    $mappedType->buildForm($formBuilder);
    return $formBuilder;
  }
  
}