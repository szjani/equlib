<?php
namespace Equ\Form\ElementCreator\Bootstrap\Form;

class SubForm extends \Zend_Form_SubForm {

  /**
   * Load the default decorators
   *
   * @return void
   */
  public function loadDefaultDecorators() {
    if ($this->loadDefaultDecoratorsIsDisabled()) {
      return $this;
    }

    $decorators = $this->getDecorators();
    if (empty($decorators)) {
      $this->addDecorator('FormElements')
        ->addDecorator('Fieldset');
    }
    return $this;
  }
  
}