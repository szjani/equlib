<?php
namespace Equ\Form\ElementCreator;

use Equ\Form\OptionFlags;

/**
  * FormBuilder objects use an implementation
  * of this interface to create form elements.
  *
  * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
  * @link        $Link$
  * @since       0.1
  * @version     $Revision$
  * @author      Szurovecz János <szjani@szjani.hu>
  */
interface IFactory
{

    /**
      * @param string $ns
      * @return IFactory
      */
    public function setNamespace($ns);

    /**
      * @param string $nsPart
      * @return IFactory
      */
    public function appendNamespacePart($nsPart);

    /**
      * @param boolean $use
      * @return Factory
      */
    public function usePlaceHolders($use = true);

    /**
      * @return boolean
      */
    public function isUsedPlaceHolders();
    
    public function createForm(OptionFlags $optionFlags = null);
    
    public function createSubForm(OptionFlags $optionFlags = null);

    /**
      * Retrieves a creator by $type
      *
      * @param  string $type
      * @return Zend_Form_Element
      */
    public function createElement($type, $fieldName, OptionFlags $optionFlags = null);

    /**
      * @return Zend_Form_Element
      */
    public function createStringElement($fieldName, OptionFlags $optionFlags = null);

    /**
      * @return Zend_Form_Element
      */
    public function createIntegerElement($fieldName, OptionFlags $optionFlags = null);

    /**
      * @return Zend_Form_Element
      */
    public function createSmallintElement($fieldName, OptionFlags $optionFlags = null);

    /**
      * @return Zend_Form_Element
      */
    public function createBigintElement($fieldName, OptionFlags $optionFlags = null);

    /**
      * @return Zend_Form_Element
      */
    public function createDecimalElement($fieldName, OptionFlags $optionFlags = null);

    /**
      * @return Zend_Form_Element
      */
    public function createFloatElement($fieldName, OptionFlags $optionFlags = null);

    /**
      * @return Zend_Form_Element
      */
    public function createBooleanElement($fieldName, OptionFlags $optionFlags = null);

    /**
      * @return Zend_Form_Element
      */
    public function createDateElement($fieldName, OptionFlags $optionFlags = null);

    /**
      * @return Zend_Form_Element
      */
    public function createTimeElement($fieldName, OptionFlags $optionFlags = null);

    /**
      * @return Zend_Form_Element
      */
    public function createDateTimeElement($fieldName, OptionFlags $optionFlags = null);

    /**
      * @return Zend_Form_Element
      */
    public function createTextElement($fieldName, OptionFlags $optionFlags = null);

    /**
      * @return Zend_Form_Element
      */
    public function createObjectElement($fieldName, OptionFlags $optionFlags = null);

    /**
      * @return Zend_Form_Element
      */
    public function createArrayElement($fieldName, OptionFlags $optionFlags = null);

    /**
      * @return Zend_Form_Element
      */
    public function createSubmitElement($fieldName, OptionFlags $optionFlags = null);

    /**
      * @return Zend_Form_Element
      */
    public function createPasswordElement($fieldName, OptionFlags $optionFlags = null);

}