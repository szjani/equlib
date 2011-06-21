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
interface IBuilder extends IOptionFlaggable {
 
  /**
   * @param mixed $object
   * @param ElementCreator\IFactory $elementCreatorFactory
   * @param \ArrayObject $objectHelpers
   */
  public function __construct($object, ElementCreator\IFactory $elementCreatorFactory, \ArrayObject $objectHelpers = null, $key = null);
  
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
  
}