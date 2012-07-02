<?php
namespace Equ\Validate;
use
    Zend_Validate_Abstract,
    Doctrine\ORM\EntityRepository;

class DbUnique extends Zend_Validate_Abstract
{
    
    /**
      * @var EntityRepository
      */
    private $repo;
    
    /**
      * @var string
      */
    private $fieldName;
    
    const INVALID  = 'exists';
    
    /**
      * @var array
      */
    protected $_messageTemplates = array(
        self::INVALID  => "Value is not unique, already exists in database",
    );
    
    /**
      * @param EntityRepository $repo
      * @param string $fieldName 
      */
    public function __construct(EntityRepository $repo, $fieldName)
    {
        $this->repo      = $repo;
        $this->fieldName = $fieldName;
    }
    
    public function isValid($value)
    {
        $res = (bool)$this->repo->findOneBy(array($this->fieldName => $value));
        if ($res) {
            $this->_error(self::INVALID);
        }
        return !$res;
    }
    
}