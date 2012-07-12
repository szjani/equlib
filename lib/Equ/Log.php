<?php
namespace Equ;

use Zend_Log;
use Equ\Auth\AuthenticatedUserStorage;

/**
 * Extended logger.
 *
 * @category    Equ
 * @package     Equ\Log
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @author      Szurovecz János <szjani@szjani.hu>
  */
class Log extends Zend_Log
{
    /**
     * @var \Equ\Auth\AuthenticatedUserStorage
     */
    private $authenticatedUserStorage;
    
    /**
     * @param \Equ\Auth\AuthenticatedUserStorage $userStorage
     * @return \Zend_Log
     */
    public function setAuthenticatedUserStorage(AuthenticatedUserStorage $userStorage)
    {
        $this->authenticatedUserStorage = $userStorage;
        return $this;
    }
    
    /**
     * @return \Equ\Auth\AuthenticatedUserStorage
     */
    public function getAuthenticatedUserStorage()
    {
        return $this->authenticatedUserStorage;
    }
    
    /**
     * @return array
     */
    protected function getExtraInfos()
    {
        $res = array('uname' => php_uname());
        $request = \Zend_Controller_Front::getInstance()->getRequest();
        if ($request instanceof \Zend_Controller_Request_Http) {
            $res = array_merge($res, array(
                'host'       => $request->getHttpHost(),
                'clientIP'   => $request->getClientIp(),
                'requestURI' => $request->getRequestUri(),
                'module'     => $request->getModuleName(),
                'controller' => $request->getControllerName(),
                'action'     => $request->getActionName()
            ));
        }
        if (null !== $this->getAuthenticatedUserStorage()) {
            $res['user'] = (string)$this->getAuthenticatedUserStorage()->getAuthenticatedUser();
        }
        return $res;
    }
    
    public function log($message, $priority, $extras = null)
    {
        $extras = array_merge((is_array($extras) ? $extras : array()), $this->getExtraInfos());
        parent::log($message, $priority, $extras);
    }
    
}