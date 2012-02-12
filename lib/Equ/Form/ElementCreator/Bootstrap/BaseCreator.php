<?php
namespace Equ\Form\ElementCreator\Bootstrap;
use
  Equ\Form\OptionFlags;

abstract class BaseCreator extends \Equ\Form\ElementCreator\AbstractCreator {

  public function initDecorators(\Zend_Form_Element $element) {
    $element
      ->removeDecorator('HtmlTag')
      ->removeDecorator('Label');
    
    if ($this->getOptionFlags()->hasFlag(OptionFlags::HORIZONTAL)) {
      $element
        ->addDecorator(array('controls' => 'HtmlTag'), array('tag' => 'div', 'class' => 'controls'))
        ->addDecorator('Label')
        ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => array('callback' => function($decorator) {
          return count($decorator->getElement()->getMessages()) !== 0
            ? 'control-group error'
            : 'control-group';
        })));
    } else {
      $element->setAttrib('class', 'input-small');
    }
    return $element;
  }
  
  /**
   * @param \Zend_Form_Element $element
   * @return AbstractCreator
   */
  protected function createPlaceholder(\Zend_Form_Element $element) {
    parent::createPlaceholder($element);
    if (\Zend_Form::hasDefaultTranslator()) {
      $element->setAttrib('placeholder', \Zend_Form::getDefaultTranslator()->translate($this->getPlaceHolder()));
    } else {
      $element->setAttrib('placeholder', $this->getPlaceHolder());
    }
    return $this;
  }
  
}