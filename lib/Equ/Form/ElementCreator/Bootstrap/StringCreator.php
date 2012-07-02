<?php
namespace Equ\Form\ElementCreator\Bootstrap;

class StringCreator extends BaseCreator
{

    protected function buildElement($fieldName)
    {
        $element = new \Zend_Form_Element_Text($fieldName);
        $this->initDecorators($element);
        return $element;
    }

}