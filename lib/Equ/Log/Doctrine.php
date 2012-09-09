<?php
namespace Equ\Log;

use Doctrine\DBAL\Logging\SQLLogger;
use Zend_Log;

class Doctrine implements SQLLogger
{

    private $time;

    /**
      *
      * @var Zend_Log
      */
    private $log;
    
    private $level;
    
    private $queryArgs;

    public function __construct(Zend_Log $log, $level = Zend_Log::DEBUG)
    {
        $this->log = $log;
        $this->level = $level;
    }

    public function startQuery($sql, array $params = null, array $types = null)
    {
        $this->time = microtime(true);
        $this->queryArgs = func_get_args();
    }

    public function stopQuery()
    {
        $this->log->log(
            sprintf("Doctrine SQL query (%f sec)\n %s\nParameters: %s",
                (microtime(true) - $this->time),
                print_r($this->queryArgs[0], true),
                print_r($this->queryArgs[1], true)
            ), $this->level
        );
    }


}