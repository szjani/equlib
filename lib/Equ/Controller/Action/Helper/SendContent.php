<?php
namespace Equ\Controller\Action\Helper;
use
  Zend_Controller_Action_Helper_Abstract,
  Zend_Controller_Action_HelperBroker,
  Zend_Controller_Request_Http;

/**
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @author      Szurovecz János <szjani@szjani.hu>
 */
abstract class SendContent extends Zend_Controller_Action_Helper_Abstract {
  
  /**
   * @var array
   */
  protected $caches = array();
  
  /**
   * @var string
   */
  protected $disposition = 'attachment';
  
  /**
   * @var int
   */
  protected $modified = null;
  
  /**
   * @var string
   */
  protected $fileName;
  
  /**
   * @var string
   */
  protected $contentType;
  
  /**
   * @var string
   */
  protected $contentLength;
  
  /**
   * @param string $contentType
   * @return SendContent $this
   */
  public function setContentType($contentType) {
    $this->contentType = $contentType;
    return $this;
  }
  
  /**
   * @param string $contentLength
   * @return SendContent $this
   */
  public function setContentLength($contentLength) {
    $this->contentLength = $contentLength;
    return $this;
  }
  
  /**
   * @param int $modified
   * @return SendContent $this
   */
  public function setModified($modified) {
    $this->modified = (int)$modified;
    return $this;
  }
  
  /**
   * @param string $fileName
   * @return SendContent $this
   */
  public function setFileName($fileName) {
    if (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE')) {
      $fileName = urlencode($fileName);
    }
    $this->fileName = $fileName;
    return $this;
  }
  
  /**
   * @param $disposition attachment|inline
   * @return SendContent $this
   */
  public function setDisposition($disposition) {
    static $validDisposition = array(
      'attachment',
      'inline',
    );
    if (!in_array($disposition, $validDisposition)) {
      throw new InvalidArgumentException('Not valid disposition!');
    }
    $this->disposition = $disposition;
    return $this;
  }
  
  /**
   * @param string $cache no-cache|no-store|public|must-revalidate|proxy-validation
   * @return SendContent $this
   */
  public function addCache($cache) {
    static $validCache = array(
      'no-cache',
      'no-store',
      'public',
      'must-revalidate',
      'proxy-validation',
      'post-check=0',
      'pre-check=0'
    );
    if (!in_array($cache, $validCache)) {
      throw new InvalidArgumentException('Not valid cache!');
    }
    if (!in_array($cache, $this->caches)) {
      $this->caches[] = $cache;
    }
    return $this;
  }
  
  /**
   * @param array $caches
   * @return SendContent $this
   */
  public function setCache(array $caches = array()) {
    foreach ($caches as $cache) {
      $this->addCache($cache);
    }
    return $this;
  }
  
  /**
   * @param array $options Keys: cache, disposition, modified, fileName, contentType
   * @return SendContent $this
   */
  public function setOptions(array $options) {
    if (isset($options['cache'])) {
      $this->setCache($options['cache']);
    }
    if (isset($options['disposition'])) {
      $this->setDisposition($options['disposition']);
    }
    if (isset($options['modified'])) {
      $this->setModified($options['modified']);
    }
    if (isset($options['fileName'])) {
      $this->setFileName($options['fileName']);
    }
    if (isset($options['contentType'])) {
      $this->setContentType($options['contentType']);
    }
    return $this;
  }
  
  /**
   * @return array
   */
  public function getCaches() {
    return $this->caches;
  }
  
  /**
   * @return string
   */
  public function getDisposition() {
    return $this->disposition;
  }
  
  /**
   * @return int
   */
  public function getModified() {
    return $this->modified;
  }
  
  /**
   * @return string
   */
  public function getFileName() {
    return $this->fileName;
  }
  
  /**
   * @return string
   */
  public function getContentLength() {
    return $this->contentLength;
  }

  /**
   * @return string
   */
  public function getContentType() {
    return $this->contentType;
  }
  
  /**
   * @return SendContent $this
   */
  public function sendHeaders() {
    // Disable layout
    if (Zend_Controller_Action_HelperBroker::hasHelper('Layout')) {
      $this->getActionController()->getHelper('Layout')->disableLayout();
    }

    // Disable ViewRenderer
    if (Zend_Controller_Action_HelperBroker::hasHelper('ViewRenderer')) {
      $this->getActionController()->getHelper('ViewRenderer')->setNoRender();
    }
    
    $response = $this->getResponse();
    if (empty($this->caches)) {
      $this
        ->addCache('must-revalidate')
        ->addCache('post-check=0')
        ->addCache('pre-check=0');
        
    }
    $response->setHeader('Cache-Control', implode(',', $this->caches), true);
    $response->setHeader('Pragma', 'no-cache', true);
    
    $request = $this->getRequest();
    if ($request instanceof Zend_Controller_Request_Http
        && !in_array('no-store', $this->caches)
        && $this->modified <= strtotime($request->getServer('HTTP_IF_MODIFIED_SINCE'))) {
      
      $response->setHttpResponseCode(304);
      return true;
    }
    
    // Required for IE, otherwise Content-disposition is ignored
    if (ini_get('zlib.output_compression')) {
      ini_set('zlib.output_compression', false);
    }
    
    $response->setHttpResponseCode(200);
    $response->setHeader('Content-Type', $this->contentType, true);
    $response->setHeader('Content-Description', $this->fileName);
    $response->setHeader('Content-Disposition', $this->disposition.'; filename="'.$this->fileName.'"', true);
    $response->setHeader('Last-Modified', gmdate('r', $this->modified), true);
    $response->setHeader('Content-Length', $this->contentLength, true);
    $response->sendHeaders();
    return $this;
  }
  
}