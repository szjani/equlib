<?php
namespace Equ\Controller\Action\Helper;
use
  Zend_Controller_Action_Helper_Abstract,
  Equ\Crud\LookUpable;

class LookUp extends Zend_Controller_Action_Helper_Abstract {
  
  /**
   * Usefull for autocomplete fields
   * Use $id or $q but not at the same time
   * 
   * $key   Key field in response
   * $value Value field in response
   * $id    Search one field by id (init form element)
   * $q     Search value
   * 
   * @param LookUpable $lookUpable
   * @return array
   */
  public function direct(LookUpable $lookUpable) {
    $request = $this->getRequest();
    $res = array();
    if (null !== $request->getParam('id')) {
      $res = $lookUpable->findOneForLookUp(
        $request->getParam('id'),
        $request->getParam('key', 'id'),
        $request->getParam('value', 'value')
      );
    } else {
      $res = $lookUpable->findForLookUp(
        $request->getParam('q'),
        $request->getParam('key', 'id'),
        $request->getParam('value', 'value')
      );
    }
    return $res;
  }
  
}