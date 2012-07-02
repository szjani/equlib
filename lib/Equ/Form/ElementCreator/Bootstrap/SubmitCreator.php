<?php
namespace Equ\Form\ElementCreator\Bootstrap;
use
    Equ\Form\OptionFlags;

class SubmitCreator extends BaseCreator
{

    public function initDecorators(\Zend_Form_Element $element)
    {
        $element
            ->removeDecorator('DtDdWrapper');
        if ($this->getOptionFlags()->hasFlag(OptionFlags::HORIZONTAL)) {
            $element->addDecorator(array('form-actions' => 'HtmlTag'), array('tag' => 'div', 'class' => 'form-actions'));
        }
        return $element;
    }
    
    protected function buildElement($fieldName)
    {
        $element = new \Zend_Form_Element_Submit($fieldName);
        $element->setAttrib('class', 'btn btn-primary');
        $this->initDecorators($element);
        return $element;
    }

}