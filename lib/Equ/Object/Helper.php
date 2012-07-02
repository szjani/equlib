<?php
namespace Equ\Object;

class Helper
{

    /**
      * @var object
      */
    private $object = null;

    /**
      * @var string
      */
    private $type = null;
    
    public function __construct($object)
    {
        if (is_object($object)) {
            $this->setObject($object);
        } elseif (class_exists($object))
{
            $this->setType($object);
        } else {
            throw new Exception\InvalidArgumentException('$object has to be an object or a class name');
        }
    }
    
    /**
      * Retrieves the class name of the object
      * 
      * @return string
      */
    public function getType()
    {
        if ($this->type === null) {
            $this->type = get_class($this->object);
        }
        return $this->type;
    }

    /**
      * Use $type to sync form data
      * 
      * @param  string $type
      * @return Mapper 
      */
    public function setType($type)
    {
        $this->type   = $type;
        $this->object = null;
        return $this;
    }

    /**
      * Get the object
      * 
      * @return object
      */
    public function getObject()
    {
        if ($this->object === null) {
            $this->object = unserialize(sprintf('O:%d:"%s":0:{}', strlen($this->getType()), $this->getType()));
        }
        return $this->object;
    }
    
    /**
      * Use $object to sync form data
      * 
      * @param  object $object
      * @return Mapper 
      */
    public function setObject($object)
    {
        $this->object = $object;
        $this->type   = null;
        return $this;
    }
    
    private function getMethod($method)
    {
        $object = $this->getObject();
        if (!in_array($method, get_class_methods($object)))
{
            throw new Exception\InvalidArgumentException("Class '{$this->getType()}' doesn't have method '{$method}'");
        }
        return $method;
    }
    
    private function getGetter($field)
    {
        return $this->getMethod('get' . ucfirst($field));
    }
    
    private function getIsser($field)
    {
        return $this->getMethod('is' . ucfirst($field));
    }
    
    private function getSetter($field)
    {
        return $this->getMethod('set' . ucfirst($field));
    }
    
    /**
      *
      * @param string $property
      * @return mixed
      */
    public function get($property)
    {
        $method = null;
        try {
            $method = $this->getGetter($property);
        } catch (Exception\InvalidArgumentException $e) {
            $method = $this->getIsser($property);
        }
        $object = $this->getObject();
        return $object->$method();
    }
    
    /**
      *
      * @param string $property
      * @param mixed $value
      * @return ObjectHelper
      */
    public function set($property, $value)
    {
        $method = $this->getSetter($property);
        $object = $this->getObject();
        $object->$method($value);
        return $this;
    }
    
}
