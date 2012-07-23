<?php
namespace Equ\Validate\Object;

use Zend_Validate_Interface;
use Zend_Validate;
use IteratorAggregate;

/**
 * Object validator interface.
 *
 * @category    Equ
 * @package     Equ\Validate
 * @subpackage  Object
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @author      Szurovecz János <szjani@szjani.hu>
  */
interface ObjectInterface extends Zend_Validate_Interface, IteratorAggregate
{
    /**
     * Add a validator for $memberName.
     * 
     * @param string $memberName
     * @param Zend_Validate_Interface $validator
     * @throws Equ\Validate\Exception\InvalidArgumentException
     */
    public function add($memberName, Zend_Validate_Interface $validator);
    
    /**
     * Get added validators for $memberName
     * 
     * @param string $memberName
     * @return Zend_Validate
     * @throws Equ\Validate\Exception\InvalidArgumentException
     */
    public function get($memberName);
    
}