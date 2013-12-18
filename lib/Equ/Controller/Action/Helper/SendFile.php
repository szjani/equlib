<?php
namespace Equ\Controller\Action\Helper;
use
    SplFileInfo,
    Equ\Controller\Exception\InvalidArgumentException,
    Equ\Controller\Exception\RuntimeException;

/**
  * Send file to the browser for downloading
  *
  * @category    Equ
  * @package     Controller
  * @subpackage  Action\Helper
  * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
  * @author      Szurovecz János <szjani@szjani.hu>
  */
class SendFile extends SendContent
{

    /**
      * @param SplFileInfo $file
      * @param array $options
      */
    public function direct(SplFileInfo $file, array $options = array())
    {
        $this->sendFile($file, $options);
    }

    /**
      * @param type $filename
      * @return string
      */
    private function detectMimeType($filename)
    {
        $result = null;

        if (class_exists('finfo', false)) {
            $const = defined('FILEINFO_MIME_TYPE') ? FILEINFO_MIME_TYPE : FILEINFO_MIME;
            $mime = @finfo_open($const);
            if (!empty($mime)) {
                $result = finfo_file($mime, $filename);
            }
            unset($mime);
        }

        if (empty($result) && (function_exists('mime_content_type') && ini_get('mime_magic.magicfile'))) {
            $result = mime_content_type($filename);
        }

        if (empty($result)) {
            $result = 'application/octet-stream';
        }
        return $result;
    }

    /**
      * @param SplFileInfo $file
      * @param array $options
      * @throws InvalidArgumentException
      * @throws RuntimeException
      */
    public function sendFile(SplFileInfo $file, array $options = array())
    {
        $this
            ->setFileName($file->getFilename())
            ->setModified($file->getMTime())
            ->setContentLength($file->getSize())
            ->setContentType($this->detectMimeType($file->getPathname()))
            ->setOptions($options)
            ->sendHeaders();

        ob_end_clean();
        ignore_user_abort(1);
        if (!ini_get('safe_mode')) {
            set_time_limit(0);
        }

        $fp = fopen($file->getPathname(), 'rb');
        if (!$fp) {
            throw new InvalidArgumentException('Invalid filename: '.$file->getPathname());
        }
        while (!feof($fp)) {
            print fread($fp, 1024);
            if (connection_aborted()) {
                fclose($fp);
                throw new RuntimeException('Unsuccess file download: '.$file->getPathname());
            }
        }
        fclose($fp);
        $this->getResponse()->clearAllHeaders();
        $this->getResponse()->sendResponse();
    }

}
