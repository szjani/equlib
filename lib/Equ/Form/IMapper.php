<?php
namespace Equ\Form;
use
  Zend_Form as Form,
  Zend_Controller_Request_Http as Request,
  Equ\Object\Helper as ObjectHelper;

interface IMapper {
  
  public function __construct(Form $form, ObjectHelper $objectHelper, array $propertyClassMap);
  
  public function isValid(Request $request);
  
  public function map();
  
}