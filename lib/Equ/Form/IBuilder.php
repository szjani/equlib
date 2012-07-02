<?php
namespace Equ\Form;

/**
  * Interface to buid form object for an POPO object
  *
  * @category    Equ
  * @package     Form
  * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
  * @author      Szurovecz János <szjani@szjani.hu>
  */
interface IBuilder extends IOptionFlaggable
{
  
    /**
      * @return IBuilder
      */
    public function add($field, $type = null);
    
    /**
      * @return IBuilder
      */
    public function addSub($field, IMappedType $type, $collection = false);
    
    /**
      * @return \Zend_Form
      */
    public function getForm();
    
    /**
      * @param  \Zend_Form $form
      * @return $this
      */
    public function setForm(\Zend_Form $form);
    
    /**
      * @return IMapper
      */
    public function getMapper();
    
    /**
      * @return ElementCreator\IFactory
      */
    public function getElementCreatorFactory();
    
    /**
      * @param  ElementCreator\IFactory $factory
      * @return Builder
      */
    public function setElementCreatorFactory(ElementCreator\IFactory $factory);
    
    public function getFormClass();
    
    public function setFormClass($class);
    
    public function getSubFormClass();
    
    public function setSubFormClass($class);
    
}