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
    $future = new Zend_Date(time() + 10);
    self::assertTrue($validator->isValid($future));
  }
  
  public function testDateWithoutTime() {
    $now = new Zend_Date();
    $validator = new DateCompare($now, 'gt');
    $future = clone $now;
    $future = $future->addMonth(1)->toString('yyyy-MM-dd');
    self::assertTrue($validator->isValid($future));
  }
  
}