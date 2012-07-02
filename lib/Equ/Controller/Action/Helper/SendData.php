<?php
namespace Equ\Controller\Action\Helper;

/**
  * Send data to the browser for downloading
  *
  * @category    Equ
  * @package     Controller
  * @subpackage  Action\Helper
  * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
  * @author      Szurovecz János <szjani@szjani.hu>
  */
class SendData extends SendContent
{

    /**
      * @param type $data
      * @param array $options
      */
    public function direct($data, array $options = array())
    {
        $this->sendData($data, $options);
    }

    /**
      * @param type $data
      * @param array $options
      */
    public function sendData($data, array $options = array())
    {
        $this
            ->setFileName(md5(time() . mt_rand()))
            ->setModified(time())
            ->setContentLength(strlen($data))
            ->setContentType('application/octet-stream')
            ->setOptions($options)
            ->sendHeaders();
        $this->getResponse()->setBody((string)$data);
    }

}