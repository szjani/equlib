<?php
namespace Equ\Controller\Plugin;

class CleanQuery extends \Zend_Controller_Plugin_Abstract {

  public function routeShutdown(\Zend_Controller_Request_Abstract $request) {
    $paramHandler = new \Equ\Controller\ArrayParamHandler();
    if (preg_match('/\?/', $request->getRequestUri())) {
      $filter    = new \Zend_Filter_Null(\Zend_Filter_Null::STRING);
      $newParams = array_filter(
        $paramHandler->convertArrayToString($request->getParams()),
        function($value) use ($filter) {
          return $filter->filter($value) !== null;
        }
      );
      \Zend_Controller_Action_HelperBroker::getStaticHelper('Redirector')->gotoRouteAndExit($newParams, null, true);
    } else {
      $newParams = $paramHandler->convertStringToArray($request->getParams());
      $request->setParams(array_merge($newParams, $request->getParams()));
    }
  }

}