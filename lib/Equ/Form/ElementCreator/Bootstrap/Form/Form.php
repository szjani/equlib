<?php
namespace Equ\Form\ElementCreator\Bootstrap\Form;

class Form extends \Zend_Form
{

    /**
      * Load the default decorators
      *
      * @return void
      */
    public function loadDefaultDecorators()
    {
        if ($this->loadDefaultDecoratorsIsDisabled()) {
            return $this;
        }

        $decorators = $this->getDecorators();
        if (empty($decorators)) {
            $this->addDecorator('FormElements')
                ->addDecorator('Form');
        }
        return $this;
    }

}