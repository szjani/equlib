<?php
namespace Equ\View\Helper;
use
    Zend_View_Helper_Abstract;

class HideMail extends Zend_View_Helper_Abstract
{
    
    public function hideMail($email)
    {
        $email = str_replace('@', '&#x' . base_convert(ord('@'), 10, 16) . ';', $email);
        $email = str_replace('.', '&#x' . base_convert(ord('.'), 10, 16) . ';', $email);
        return $email;
    }
    
}