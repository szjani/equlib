<?php
namespace Equ\Form;
use
    Zend_Form as Form,
    Zend_Controller_Request_Http as Request,
    Equ\Object\Helper as ObjectHelper;

interface IMapper
{

    public function __construct(Form $form, $key, \ArrayObject $objectHelpers);

    public function isValid(Request $request, $autoMapping = true);

    public function map();

}