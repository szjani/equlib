<?php
namespace Equ\Form;
use
  IteratorIterator,
  RecursiveIterator,
  ArrayIterator;

/**
 * Iterate over form and it's subform hierarchy
 *
 * @category    Equ
 * @package     Form
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @author      Szurovecz János <szjani@szjani.hu>
 */
class RecursiveFormIterator extends IteratorIterator implements RecursiveIterator {
  
  /**
   * @param array $forms 
   */
  public function __construct(array $forms) {
    parent::__construct(new ArrayIterator($forms));
  }

  /**
   * @return RecursiveFormIterator 
   */
  public function getChildren() {
    return new self($this->current()->getSubForms());
  }

  /**
   * @return boolean
   */
  public function hasChildren() {
    return 0 < count($this->current()->getSubForms());
  }
  
}