<?php
namespace Equ\Form\ElementCreator\Bootstrap;

class CheckboxCreator extends BaseCreator
{

    protected function buildElement($fieldName)
    {
        $element = new \Zend_Form_Element_Checkbox($fieldName);
        $this->initDecorators($element);
        return $element;
    }

}