<?php
namespace Equ\Form\ElementCreator;
use
    Equ\Form\OptionFlags,
    Equ\Form\ElementCreator\Exception\InvalidArgumentException,
    Equ\Validate as Validator,
    Equ\Form\IOptionFlaggable;

/**
  * Abstract form element creator class.
  *
  * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
  * @link        $Link$
  * @since       0.1
  * @version     $Revision$
  * @author      Szurovecz János <szjani@szjani.hu>
  */
abstract class AbstractCreator implements IOptionFlaggable
{

    /**
      * @var array
      */
    protected $values;

    /**
      * @var string
      */
    private $namespace = '';

    /**
      *
      * @var string
      */
    private $label = null;

    /**
      * @var string
      */
    private $placeholder = null;

    /**
      * @var Validator
      */
    private $validator;

    /**
      * @var OptionFlags
      */
    private $optionFlags = null;

    /**
      * @param int $flags
      * @param string $namespace
      */
    public function __construct($namespace = '', OptionFlags $flags = null)
    {
        $this->setNamespace($namespace);
        $this->optionFlags = $flags;
    }

    protected function validatorAdded(\Zend_Form_Element $element, \Zend_Validate_Abstract $validator)
    {
    }

    /**
      * @return \Zend_Form_Element
      */
    protected abstract function buildElement($fieldName);

    /**
      * @param OptionFlags $flags
      * @return AbstractCreator
      */
    public function setOptionFlags(OptionFlags $flags)
    {
        $this->optionFlags = $flags;
        return $this;
    }

    /**
      * @return OptionFlags
      */
    public function getOptionFlags()
    {
        if (null === $this->optionFlags) {
            $this->optionFlags = new OptionFlags();
        }
        return $this->optionFlags;
    }

    /**
      * @param string $namespace
      * @return AbstractCreator
      */
    public function setNamespace($namespace)
    {
        $this->namespace = \rtrim($namespace, '/');
        return $this;
    }

    /**
      * @return string
      */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
      * @param string $label
      * @return AbstractCreator
      */
    public function setLabel($label)
    {
        $this->label = $label;
        return $this;
    }

    /**
      * @return string
      */
    public function getLabel()
    {
        return $this->label;
    }

    /**
      * @param string $string
      * @return AbstractCreator
      */
    public function setPlaceHolder($string)
    {
        $this->placeholder = $string;
        return $this;
    }

    /**
      * @return string
      */
    public function getPlaceHolder()
    {
        return $this->placeholder;
    }

    /**
      * @param Validator $validator
      * @return AbstractCreator
      */
    public function setValidator(Validator $validator)
    {
        $this->validator = $validator;
        return $this;
    }

    /**
      * @return Validator
      */
    public function getValidator()
    {
        return $this->validator;
    }

    public function initDecorators(\Zend_Form_Element $element)
    {
    }

    /**
      * You should pass $values from
      * $em->getClassMetadata($className)->fieldMappings
      *
      * @param string $fieldName
      * @param array $values field mapping array of entity
      * @return \Zend_Form_Element
      */
    public function createElement($fieldName, array $values = array())
    {
        $this->values = $values;
        $element = null;
        if ($fieldName instanceof \Zend_Form_Element) {
            $element = $fieldName;
        } else {
            $element = $this->buildElement($fieldName);
        }
        if ($this->getOptionFlags()->hasFlag(OptionFlags::IMPLICIT_VALIDATORS)) {
            $this->createImplicitValidators($element);
        }
        if ($this->getOptionFlags()->hasFlag(OptionFlags::EXPLICIT_VALIDATORS)) {
            $this->createExplicitValidators($element);
        }
        if ($this->getOptionFlags()->hasFlag(OptionFlags::LABEL)) {
            $this->createLabel($element);
        }
        if ($this->getOptionFlags()->hasFlag(OptionFlags::PLACEHOLDER)) {
            $this->createPlaceholder($element);
        }
        $this->initDecorators($element);
        return $element;
    }

    /**
      * @throws InvalidArgumentException
      * @param \Zend_Form_Element $element
      * @return AbstractCreator
      */
    protected function createImplicitValidators(\Zend_Form_Element $element)
    {
        if (is_object($this->getValidator())) {
            foreach ($this->getValidator() as $validator) {
                if (!($validator instanceof \Zend_Validate_Interface)) {
                    throw new InvalidArgumentException("Validator object must implements \Zend_Validate_Interface");
                }
                $element->addValidator($validator, true);
                if ($validator instanceof \Zend_Validate_NotEmpty) {
                    $element->setRequired();
                }
                $this->validatorAdded($element, $validator);
            }
        }
        return $this;
    }

    /**
      * @param \Zend_Form_Element $element
      * @return AbstractCreator
      */
    protected function createExplicitValidators(\Zend_Form_Element $element)
    {
        if (array_key_exists('nullable', $this->values) && !$this->values['nullable']) {
            $element->setRequired();
            $validator = new \Zend_Validate_NotEmpty();
            $element->addValidator($validator);
            $this->validatorAdded($element, $validator);
        }
        if (array_key_exists('length', $this->values) && \is_numeric($this->values['length'])) {
            $validator = new \Zend_Validate_StringLength();
            $validator->setMax($this->values['length']);
            $element->addValidator($validator);
            $this->validatorAdded($element, $validator);
        }
        return $this;
    }

    /**
      * @param \Zend_Form_Element $element
      * @return AbstractCreator
      */
    protected function createLabel(\Zend_Form_Element $element)
    {
        if ($this->getLabel() === null) {
            $this->setLabel(ltrim($this->getNamespace() . '/' . $element->getName(), '/'));
        }
        $element->setLabel($this->getLabel());
        return $this;
    }

    /**
      * @param \Zend_Form_Element $element
      * @return AbstractCreator
      */
    protected function createPlaceholder(\Zend_Form_Element $element)
    {
        if ($this->getPlaceHolder() === null) {
            $this->setPlaceHolder(ltrim($this->getNamespace() . '/' . $element->getName(), '/'));
        }
    }
}