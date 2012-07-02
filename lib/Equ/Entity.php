<?php
namespace Equ;
use
    Equ\Exception\RuntimeException,
    Equ\Object\Validatable,
    Equ\Object\Validator,
    Equ\Crud\SortableEntity,
    Equ\Crud\DisplayableEntity,
    Doctrine\ORM\Mapping as ORM;

/**
  * Abstract entity class
  *
  * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
  * @link        $Link$
  * @version     $Revision$
  * @author      Szurovecz János <szjani@szjani.hu>
  * 
  * @ORM\MappedSuperclass
  * @ORM\HasLifecycleCallbacks
  */
abstract class Entity implements \ArrayAccess, SortableEntity, DisplayableEntity
{

    /**
      * @var Validator 
      */
    private $validator;
    
    /**
      * Retrieves the class name,
      * usefull for parameter of $em->getRepository() method
      * 
      * @return string
      */
    public final static function className()
    {
        return get_called_class();
    }
    
    /**
      * @param Validator $validator
      * @return Entity 
      */
    public function setValidator(Validator $validator)
    {
        $this->validator = $validator;
        return $this;
    }
    
    /**
      * @return type 
      */
    public function getValidator()
    {
        return $this->validator;
    }
    
    /**
      * @ORM\PrePersist @ORM\PreUpdate
      */
    public function validate()
    {
        if ($this instanceof Validatable && is_object($this->validator)) {
            if (!$this->validator->isValid($this)) {
                throw new RuntimeException(implode(PHP_EOL, $this->validator->getMessages()));
            }
        }
    }

    public function offsetExists($offset)
    {
        $objectHelper = new \Equ\Object\Helper($this);
        try {
            $objectHelper->get($offset);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function offsetGet($offset)
    {
        $objectHelper = new \Equ\Object\Helper($this);
        try {
            return $objectHelper->get($offset);
        } catch (\Exception $e) {
            return null;
        }
    }

    public function offsetSet($offset, $value)
    {
        throw new BadMethodCallException("ArrayAccess readonly!");
    }

    public function offsetUnset($offset)
    {
        throw new BadMethodCallException("ArrayAccess readonly!");
    }
    
    public static function getSortField()
    {
        return static::getDisplayField();
    }
    
    public static function getDisplayField()
    {
        return 'id';
    }

}
