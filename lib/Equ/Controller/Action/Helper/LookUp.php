<?php
namespace Equ\Controller\Action\Helper;
use
  Zend_Controller_Action_Helper_Abstract,
  Equ\Crud\LookUpable;

/**
 * If you want to create an autocomplete field
 * then you should implement Equ\Crud\LookUpable interface
 * typically in your repository class.
 * This helper use request parameters and the given repository
 * to get the required data. Here is an example.
 *
 * @category    Equ
 * @package     Controller
 * @subpackage  Action\Helper
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @author      Szurovecz János <szjani@szjani.hu>
 */
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