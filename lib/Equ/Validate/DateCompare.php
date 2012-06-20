<?php
namespace Equ\Validate;
use
  Zend_Validate_Abstract,
  Zend_Date,
  DateTime;

class DateCompare extends Zend_Validate_Abstract {
  
  private $refDate;
  
  private $operator;
  
  const INVALID  = 'invalid';
  
  /**
   * @var array
   */
  protected $_messageTemplates = array(
    self::INVALID  => "'%value%' is invalid, set a proper date",
  );
  
  /**
   * @param Zend_Date $refDate
   * @param string $operator 
   */
  public function __construct(Zend_Date $refDate, $operator = '<') {
    $this->refDate  = $refDate;
    $this->operator = $operator;
  }
  
  public function isValid($value) {
    $date = null;
    if ($value instanceof Zend_Date) {
      $date = $value;
    } elseif ($value instanceof DateTime) {
      $date = new Zend_Date($value->format('Y-m-d H:i:s'));
    } elseif (is_string($value) || is_numeric($value)) {
      $date = new Zend_Date($value);
    }
    $this->_setValue((string)$date);
    
    $res = version_compare($date->getTimestamp(), $this->refDate->getTimestamp(), $this->operator);
    
    if (!$res) {
      $this->_error(self::INVALID);
    }
    return $res;
  }
}