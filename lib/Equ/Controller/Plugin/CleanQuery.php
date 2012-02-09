<?php
namespace Equ\Controller\Plugin;

class CleanQuery extends \Zend_Controller_Plugin_Abstract {

  public function routeShutdown(\Zend_Controller_Request_Abstract $request) {
    $paramHandler = new \Equ\Controller\ArrayParamHandler();
    if (preg_match('/\?/', $request->getRequestUri())) {
      $newParams = $paramHandler->convertArrayToString($request->getParams());
      \Zend_Controller_Action_HelperBroker::getStaticHelper('Redirector')->gotoRouteAndExit($newParams);
    } else {
      $newParams = $paramHandler->convertStringToArray($request->getParams());
      $request->setParams(array_merge($newParams, $request->getParams()));
    }
  }

}