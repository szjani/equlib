<?php
namespace Equ\Form\ElementCreator\Bootstrap;
use
    Equ\Form\OptionFlags;

class CaptchaCreator extends BaseCreator
{

    private $pubkey;
    private $privkey;

    public function setPubKey($pubKey)
    {
        $this->pubkey = $pubKey;
        return $this;
    }

    public function setPrivKey($privKey)
    {
        $this->privkey = $privKey;
        return $this;
    }

    protected function buildElement($fieldName)
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

        if ($this->getOptionFlags()->hasFlag(OptionFlags::HORIZONTAL)) {
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

}