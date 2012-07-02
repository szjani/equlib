<?php
namespace Equ\Object;
use Doctrine\ORM\EntityManager;

/**
  * Validate objects
  *
  * @category    Equ
  * @package     Object
  * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
  * @author      Szurovecz János <szjani@szjani.hu>
  */
class Validator implements \IteratorAggregate
{

    /**
      * @var array
      */
    private $fieldValidators = array();

    /**
      * @var EntityManager
      */
    private $em;

    /**
      * @var string
      */
    private $class;

    /**
      * @param string $class
      * @param EntityManager $em
      */
    public function __construct($class, EntityManager $em)
    {
        if (!method_exists($class, 'loadValidators')) {
            throw new Exception\InvalidArgumentException("$class has to implement the Validatable interface");
        }
        $this->em    = $em;
        $this->class = $class;
        $class::loadValidators($this);
    }

    /**
      * @param  string $field
      * @param  \Zend_Validate_Abstract $validator
      * @return Validator
      */
    public function add($field, \Zend_Validate_Abstract $validator)
    {
        $this->getFieldValidate($field)->addValidator($validator);
        return $this;
    }

    /**
      * @return \ArrayIterator
      */
    public function getIterator()
    {
        return new \ArrayIterator($this->fieldValidators);
    }

    /**
      * @param  string $field
      * @return \Equ\Validate
      */
    public function getFieldValidate($field)
    {
        if (!array_key_exists($field, $this->fieldValidators)) {
            $this->fieldValidators[$field] = new \Equ\Validate();
        }
        return $this->fieldValidators[$field];
    }

    /**
      * @param  Validatable $object
      * @return boolean
      */
    public function isValid(Validatable $object)
    {
        $valid = true;
        try {
            $metadata = $this->em->getClassMetadata($class);
            foreach ($metadata->fieldMappings as $field => $map) {
                if (array_key_exists($field, $this->fieldValidators)) {
                    $value = $metadata->getFieldValue($object, $field);
                    /* @var $validator \Zend_Validate_Abstract */
                    $valid |= $this->fieldValidators[$field]->isValid($value);
                }
            }
        } catch (\Doctrine\ORM\Mapping\MappingException $e) {
            $objectHelper = new Helper($this->class);
            foreach ($this->fieldValidators as $name => $validate) {
                try {
                    $valid |= $validate->isValid($objectHelper->get($name));
                } catch (\InvalidArgumentException $e) {
                }
            }
        }
        return $valid;
    }

    /**
      * @return array
      */
    public function getMessages()
    {
        $messages = array();
        foreach ($this->fieldValidators as $name => $validate) {
            $messages = array_merge($messages, $validate->getMessages());
        }
        return $messages;
    }

}
