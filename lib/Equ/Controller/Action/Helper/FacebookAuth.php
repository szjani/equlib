<?php
namespace Equ\Controller\Action\Helper;

use
  Facebook,
  Zend_Controller_Action_Helper_Abstract,
  Equ\Controller\Exception\RuntimeException;

/**
 * Authentication methods for Facebook
 *
 * @category    Equ
 * @package     Controller
 * @subpackage  Action\Helper
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @author      Szurovecz János <szjani@szjani.hu>
 */
class FacebookAuth extends Zend_Controller_Action_Helper_Abstract {
  
  /**
   * @var Facebook
   */
  protected $facebook;

  /**
   * @param Facebook $facebook 
   */
  public function __construct(Facebook $facebook) {
    $this->facebook = $facebook;
  }
  
  /**
   * Server-side page tab authentication
   */
  public function checkPageAuth() {
    if (!$this->facebook->getUser()) {
      $appId  = $this->facebook->getAppId();
      $signedRequest = $this->facebook->getSignedRequest();
      if (null == $signedRequest) {
        throw new RuntimeException('Missing signed_request!');
      }
      if (!array_key_exists('page', $signedRequest)) {
        throw new RuntimeException('The app has to be loaded on a page tab!');
      }
      $pageId = $signedRequest['page']['id'];
      $loginUrl = $this->facebook->getLoginUrl(array(
        'scope' => 'publish_stream',
        'redirect_uri' => $this->getRequest()->getScheme() . "://www.facebook.com/pages/null/$pageId?sk=app_$appId"
      ));
      echo "<script type='text/javascript'>top.location.href = '$loginUrl';</script>";
      exit;
    }
  }
  
}