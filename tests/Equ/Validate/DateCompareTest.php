<?php
namespace Equ\Validate;
use
  PHPUnit_Framework_TestCase,
  Zend_Date,
  DateTime;


class DateCompareTest extends PHPUnit_Framework_TestCase {

  public function testTimestamp() {
    $validator = new DateCompare(new Zend_Date());
    $past = time() - 10;
    self::assertTrue($validator->isValid($past));
  }
  
  public function testDateTime() {
    $validator = new DateCompare(new Zend_Date());
    $past = new DateTime('@' . (time() - 10));
    self::assertTrue($validator->isValid($past));
  }
  
  public function testZendDate() {
    $validator = new DateCompare(new Zend_Date());
    $past = new Zend_Date(time() - 10);
    self::assertTrue($validator->isValid($past));
  }
  
  public function testZendDateGreater() {
    $validator = new DateCompare(new Zend_Date(), 'gt');
    $past = new Zend_Date(time() + 10);
    self::assertTrue($validator->isValid($past));
  }
  
}