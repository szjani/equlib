<?php
namespace Equ\Form\ElementCreator;

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

    /**
      * Retrieves a creator by $type
      * 
      * @param  string $type
      * @return AbstractCreator
      */
    public function createCreator($type);
    
    /**
      * @return AbstractCreator
      */
    public function createStringCreator();

    /**
      * @return AbstractCreator
      */
    public function createIntegerCreator();
    
    /**
      * @return AbstractCreator
      */
    public function createSmallintCreator();

    /**
      * @return AbstractCreator
      */
    public function createBigintCreator();

    /**
      * @return AbstractCreator
      */
    public function createDecimalCreator();

    /**
      * @return AbstractCreator
      */
    public function createFloatCreator();
    
    /**
      * @return AbstractCreator
      */
    public function createBooleanCreator();
    
    /**
      * @return AbstractCreator
      */
    public function createDateCreator();

    /**
      * @return AbstractCreator
      */
    public function createTimeCreator();

    /**
      * @return AbstractCreator
      */
    public function createDateTimeCreator();
    
    /**
      * @return AbstractCreator
      */
    public function createTextCreator();

    /**
      * @return AbstractCreator
      */
    public function createObjectCreator();

    /**
      * @return AbstractCreator
      */
    public function createArrayCreator();
    
    /**
      * @return AbstractCreator
      */
    public function createSubmitCreator();
    
    /**
      * @return AbstractCreator
      */
    public function createPasswordCreator();

}