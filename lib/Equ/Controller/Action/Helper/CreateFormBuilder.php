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
    $formBuilder = new FormBuilder(
      $object,
      $this->getActionController()->getHelper('serviceContainer')->getContainer()->get('form.elementcreator.factory')
    );
    $mappedType->buildForm($formBuilder);
    return $formBuilder;
  }
  
}