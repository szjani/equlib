<?php
namespace Equ\Form\ElementCreator\Builtin;

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
        return $element;
    }
    
}