<?php
namespace Equ;

class Validate extends \Zend_Validate implements \Iterator
{

    private $current = false;

    /**
      * @return \Zend_Validate_Interface
      */
    public function current()
    {
        return $this->current['instance'];
    }

    public function key()
    {
        return key($this->_validators);
    }

    public function next()
    {
        $this->current = next($this->_validators);
    }

    public function rewind()
    {
        $this->current = reset($this->_validators);
    }

    public function valid()
    {
        return $this->current !== false;
    }

}