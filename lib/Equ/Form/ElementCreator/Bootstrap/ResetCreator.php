<?php
namespace Equ\Form\ElementCreator\Bootstrap;

class ResetCreator extends BaseCreator
{

    protected function buildElement($fieldName)
    {
        $element = new \Zend_Form_Element_Reset($fieldName);
        $element->setAttrib('class', 'btn');
        return $element;
    }

}