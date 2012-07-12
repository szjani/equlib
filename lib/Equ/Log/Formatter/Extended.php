<?php
namespace Equ\Log\Formatter;

use Zend_Log_Formatter_Simple;

/**
 * Extended log formatter.
 *
 * @category    Equ
 * @package     Equ\Log
 * @subpackage  Formatter
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @author      Szurovecz János <szjani@szjani.hu>
  */
class Extended extends Zend_Log_Formatter_Simple
{
    protected $_format = "%timestamp% %priorityName% (%priority%): %message%\nUname:\t\t%uname%\nHost:\t\t%host%
Client IP:\t%clientIP%\nURI:\t\t%requestURI%\nModule:\t\t%module%\nController:\t%controller%\nAction:\t\t%action%\nUser: \t\t%user%";
    
    public function __construct($format = null)
    {
        if (null === $format) {
            $format = $this->_format;
        }
        parent::__construct($format);
    }
}