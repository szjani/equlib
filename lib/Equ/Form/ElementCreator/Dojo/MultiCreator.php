<?php
namespace Equ\Form\ElementCreator\Dojo;

class MultiCreator extends \Equ\Form\ElementCreator\AbstractCreator
{

    protected function buildElement($fieldName)
    {
        return new \Zend_Form_Element_Multiselect($fieldName);
    }

}