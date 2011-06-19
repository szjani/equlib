<?php
namespace Equ\Controller;
use \Zend_Controller_Request_Abstract as Request;

class ArrayParamHandler {
  
  const DELIMITER = '-';
  
  /**
   * @param  string $key
   * @param  array $data
   * @param  string $globalPrefix Egyeb fix prefix, amit az osszes kulcs ele tesz
   * @return array
   */
  protected function convertData($key, $data) {
    if (!is_array($data)) {
      return array($key => $data);
    }
    $ret = array();
    foreach ($data as $localKey => $value) {
      $prefix = $key == null ? '' : $key . self::DELIMITER;
      $ret += $this->convertData($prefix . $localKey, $value);
    }
    return $ret;
  }
  
  /**
   * Create ZF parameters from array notation:
   * ?user[name]=john&user[age]=25
   *  ->
   * /user:name/john/user:age/25
   * 
   * @param array $params
   * @return array $newParams
   */
  public function convertArrayToString(array $params) {
    $filters = array();
    $newParams = array();
    foreach ($params as $key => $value) {
      if (is_array($value)) {
        $newParams = array_merge($newParams, $this->convertData($key, $value));
      } else {
        $newParams[$key] = $value;
      }
    }
    return $newParams;
  }
  
  /**
   * @param array $params
   * @return type 
   */
  public function convertStringToArray(array $params) {
    $newParams = array();
    $current = &$newParams;
    foreach ($params as $key => $value) {
      if (false !== strpos($key, self::DELIMITER)) {
        foreach (explode(self::DELIMITER, $key) as $paramParts) {
          if (!array_key_exists($paramParts, $current)) {
            $current[$paramParts] = array();
          }
          $current = &$current[$paramParts];
        }
        $current = $value;
        $current = &$newParams;
      }
    }
    return $newParams;
  }
  
}