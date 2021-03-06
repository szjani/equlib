<?php
namespace Equ\Form\ElementCreator;

use Equ\Form\OptionFlags;

/**
  * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
  * @link        $Link$
  * @since       0.1
  * @version     $Revision$
  * @author      Szurovecz János <szjani@szjani.hu>
  */
abstract class AbstractFactory implements IFactory
{

    /**
      * @var string
      */
    private $namespace = '';

    /**
      * HTML5 and DOJO supports it...
      *
      * @var boolean
      */
    private $usePlaceHolders = false;

    /**
      * @param string $ns
      * @return AbstractFactory
      */
    public function setNamespace($ns)
    {
        $this->namespace = $ns;
        return $this;
    }

    /**
      * @param type $nsPart
      * @return AbstractFactory
      */
    public function appendNamespacePart($nsPart)
    {
        $this->namespace .= '/' . trim($nsPart, '/');
        return $this;
    }

        /**
      * @param boolean $use
      * @return AbstractFactory
      */
    public function usePlaceHolders($use = true)
    {
        $this->usePlaceHolders = (boolean)$use;
        return $this;
    }

    /**
      * @return boolean
      */
    public function isUsedPlaceHolders()
    {
        return $this->usePlaceHolders;
    }

    /**
      * @return string
      */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
      * Retrieves a creator by $type
      *
      * @param  string $type
      * @return AbstractCreator
      */
    public function createElement($type, $fieldName, OptionFlags $optionFlags = null)
    {
        $method = 'create' . ucfirst(strtolower($type)) . 'Element';
        if (!method_exists($this, $method)) {
            throw new Exception\InvalidArgumentException("$type is not a valid type");
        }
        return $this->$method($fieldName, $optionFlags);
    }

    public function createForm(OptionFlags $optionFlags = null)
    {
        return new \Zend_Form();
    }
    
    public function createSubForm(OptionFlags $optionFlags = null)
    {
        return new \Zend_Form_SubForm();
    }
}