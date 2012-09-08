<?php
namespace Equ\Form\ElementCreator\Bootstrap;

use Equ\Form\OptionFlags;

class Factory extends \Equ\Form\ElementCreator\AbstractFactory
{
    private $pubkey;
    private $privkey;

    public function setCaptchaPubKey($pubKey)
    {
        $this->pubkey = $pubKey;
        return $this;
    }

    public function setCaptchaPrivKey($privKey)
    {
        $this->privkey = $privKey;
        return $this;
    }
    
    private function initDecorators(\Zend_Form_Element $element, OptionFlags $optionFlags = null)
    {
        $translateKey = ltrim($this->getNamespace() . '.' . $element->getName(), '.');
        if (\Zend_Form::getDefaultTranslator()->isTranslated($translateKey)) {
            if ($element->getLabel() === null && $optionFlags->hasFlag(OptionFlags::LABEL)) {
                $element->setLabel($translateKey);
            }
        } else {
            $element->setLabel($element->getName());
        }
        if ($optionFlags->hasFlag(OptionFlags::PLACEHOLDER)) {
            $element->setAttrib('placeholder', $element->getLabel());
        }
        
        $element
            ->removeDecorator('HtmlTag')
            ->removeDecorator('Label');

        if (null !== $optionFlags && $optionFlags->hasFlag(OptionFlags::HORIZONTAL)) {
            $element
                ->addDecorator(array('controls' => 'HtmlTag'), array('tag' => 'div', 'class' => 'controls'))
                ->addDecorator('Label')
                ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => array('callback' => function($decorator)
                {
                    $id = $decorator->getElement()->getId();
                    return count($decorator->getElement()->getMessages()) !== 0
                        ? 'control-group error ' . $id
                        : 'control-group ' . $id;
                })));
        } else {
            $element->setAttrib('class', 'input-small');
        }
        return $element;
    }
    
    protected function createDefaultElement($fieldName, OptionFlags $optionFlags = null)
    {
        $element = new \Zend_Form_Element_Text($fieldName);
        $this->initDecorators($element, $optionFlags);
        return $element;
    }

    public function createStringElement($fieldName, OptionFlags $optionFlags = null)
    {
        return $this->createDefaultElement($fieldName, $optionFlags);
    }

    public function createIntegerElement($fieldName, OptionFlags $optionFlags = null)
    {
        return $this->createDefaultElement($fieldName, $optionFlags);
    }

    public function createSmallintElement($fieldName, OptionFlags $optionFlags = null)
    {
        return $this->createDefaultElement($fieldName, $optionFlags);
    }

    public function createBigintElement($fieldName, OptionFlags $optionFlags = null)
    {
        return $this->createDefaultElement($fieldName, $optionFlags);
    }

    public function createBooleanElement($fieldName, OptionFlags $optionFlags = null)
    {
        $element = new \Zend_Form_Element_Checkbox($fieldName);
        $this->initDecorators($element, $optionFlags);
        return $element;
    }

    public function createDecimalElement($fieldName, OptionFlags $optionFlags = null)
    {
        return $this->createDefaultElement($fieldName, $optionFlags);
    }

    public function createArrayElement($fieldName, OptionFlags $optionFlags = null)
    {
        $element = new \Zend_Form_Element_Select($fieldName);
        $this->initDecorators($element, $optionFlags);
        return $element;
    }

    public function createRadioElement($fieldName, OptionFlags $optionFlags = null)
    {
        $element = new \Zend_Form_Element_Radio($fieldName);
        $this->initDecorators($element, $optionFlags);
        return $element;
    }

    public function createDateElement($fieldName, OptionFlags $optionFlags = null)
    {
        return $this->createDefaultElement($fieldName, $optionFlags);
    }

    public function createDateTimeElement($fieldName, OptionFlags $optionFlags = null)
    {
        return $this->createDefaultElement($fieldName, $optionFlags);
    }

    public function createFloatElement($fieldName, OptionFlags $optionFlags = null)
    {
        return $this->createDefaultElement($fieldName, $optionFlags);
    }

    public function createObjectElement($fieldName, OptionFlags $optionFlags = null)
    {
        return $this->createDefaultElement($fieldName, $optionFlags);
    }

    public function createTextElement($fieldName, OptionFlags $optionFlags = null)
    {
        $element = new \Zend_Form_Element_Textarea($fieldName);
        $element->setAttrib('rows', 6);
        $this->initDecorators($element, $optionFlags);
        return $element;
    }

    public function createTimeElement($fieldName, OptionFlags $optionFlags = null)
    {
        return $this->createDefaultElement($fieldName, $optionFlags);
    }

    public function createResetElement($fieldName, OptionFlags $optionFlags = null)
    {
        $element = new \Zend_Form_Element_Reset($fieldName);
        $element->setAttrib('class', 'btn');
        return $element;
    }

    public function createSubmitElement($fieldName, OptionFlags $optionFlags = null)
    {
        $element = new \Zend_Form_Element_Submit($fieldName);
        $element->setAttrib('class', 'btn btn-primary');
        $element
            ->removeDecorator('DtDdWrapper');
        if ($optionFlags->hasFlag(OptionFlags::HORIZONTAL)) {
            $element->addDecorator(array('form-actions' => 'HtmlTag'), array('tag' => 'div', 'class' => 'form-actions'));
        }
        $translateKey = ltrim($this->getNamespace() . '.' . $element->getName(), '.');
        if (\Zend_Form::getDefaultTranslator()->isTranslated($translateKey)) {
            $element->setLabel($translateKey);
        }
        return $element;
    }

    public function createPasswordElement($fieldName, OptionFlags $optionFlags = null)
    {
        $element = new \Zend_Form_Element_Password($fieldName);
        $this->initDecorators($element, $optionFlags);
        return $element;
    }

    public function createCaptchaElement($fieldName, OptionFlags $optionFlags = null)
    {
        $reCaptcha = new \Zend_Service_ReCaptcha($this->pubkey, $this->privkey, array('ssl' => true));
        $reCaptcha->setOption('theme', 'custom');
        $element = new \Zend_Form_Element_Captcha($fieldName, array(
            'captcha' => 'ReCaptcha',
            'captchaOptions' => array('captcha' => 'ReCaptcha', 'service' => $reCaptcha),
        ));
        $element
            ->removeDecorator('HtmlTag')
            ->removeDecorator('Label');

        if ($optionFlags->hasFlag(OptionFlags::HORIZONTAL)) {
            $element
                ->addDecorator(new \Zend_Form_Decorator_Callback(array(
                    'placement' => 'prepend',
                    'callback' => function($content, $element)
                    {
                        return '<div id="recaptcha_image"></div>' .
                            $element->getView()->formText(
                                $element->getBelongsTo() . '[recaptcha_response_field]',
                                null,
                                array('id' => 'recaptcha_response_field')
                            );
                    }
                )))
                ->addDecorator(array('controls' => 'HtmlTag'), array('tag' => 'div', 'class' => 'controls'))
                ->addDecorator('Label')
                ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => array('callback' => function($decorator)
                {
                    return count($decorator->getElement()->getMessages()) !== 0
                        ? 'control-group error'
                        : 'control-group';
                })));
        } else {
            $element->setAttrib('class', 'input-small');
        }

        return $element;
    }
    
    public function createForm(OptionFlags $optionFlags = null)
    {
        $form = new Form\Form();
        if ($optionFlags !== null) {
            $form->setAttrib('class', $optionFlags->hasFlag(OptionFlags::HORIZONTAL) ? 'form-horizontal' : 'form-vertical');
        }
        return $form;
    }
    
    public function createSubForm(OptionFlags $optionFlags = null)
    {
        $form = new Form\SubForm();
        return $form;
    }
}