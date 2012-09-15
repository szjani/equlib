<?php
namespace Equ\Doctrine\Session;

use Zend_Session_SaveHandler_Interface;
use Doctrine\ORM\EntityManager;
use Equ\Doctrine\Exception\InvalidArgumentException;
use Zend_Session;
use DateTime;
use Zend_Log;

/**
 * Save sessions to database with Doctrine2.
 *
 * @category    Equ
 * @package     Equ\Doctrine
 * @subpackage  Session
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @author      Szurovecz János <szjani@szjani.hu>
 */
class SaveHandler implements Zend_Session_SaveHandler_Interface
{
    /**
     * @var EntityManager
     */
    private $em;
    
    /**
     * @var EntityInterface
     */
    private $entityClass;
    
    /**
     * @var Zend_Log
     */
    private $log;
    
    private $sessionSavePath;
    
    private $sessionName;
    
    public function __construct(EntityManager $em, Zend_Log $log, $entityClass)
    {
        if (!in_array(__NAMESPACE__ . '\EntityInterface', class_implements($entityClass))) {
            throw new InvalidArgumentException('EntityClass ('.  $entityClass.') must implement ' . __NAMESPACE__ . '\EntityInterface');
        }
        $this->em = $em;
        $this->entityClass = $entityClass;
        $this->log = $log;
    }
    
    public function __destruct()
    {
        Zend_Session::writeClose();
    }
    
    public function close()
    {
        return true;
    }

    public function destroy($id)
    {
        try {
            $this->em->remove($this->em->find($this->entityClass, $id));
            $this->em->flush();
            return true;
        } catch (\Exception $e) {
            $this->log->err($e);
            return false;
        }
    }

    public function gc($maxlifetime)
    {
        try {
            $class = $this->entityClass;
            $this->em->createQueryBuilder()
                ->delete($class, 's')
                ->where('s.' . $class::expiresField() . ' < :expires')
                ->setParameter('expires', new DateTime())
                ->getQuery()
                ->execute();
            return true;
        } catch (\Exception $e) {
            $this->log->err($e);
            return false;
        }
    }

    public function open($savePath, $name)
    {
        $this->sessionSavePath = $savePath;
        $this->sessionName = $name;
        return true;
    }

    public function read($id)
    {
        try {
            $class = $this->entityClass;
            $session = $this->em->find($class, $id);
            /* @var $session EntityInterface */
            if ($session !== null) {
                if ($session->getExpires()->getTimestamp() < time()) {
                    $this->destroy($id);
                } else {
                    return $session->getData();
                }
            }
        } catch (\Exception $e) {
            $this->log->err($e);
        }
        return '';
    }

    public function write($id, $data)
    {
        try {
            $class = $this->entityClass;
            $session = $this->em->find($this->entityClass, $id);
            /* @var $session EntityInterface */
            if ($session === null) {
                $session = new $class();
                $session->setLifeTime((int)ini_get('session.gc_maxlifetime'));
                $session->setId($id);
                $session->setSessionName($this->sessionName);
                $session->setSavePath($this->sessionSavePath);
                $this->em->persist($session);
            }
            $session->setData($data);
            $this->em->flush();
            return true;
        } catch (\Exception $e) {
            $this->log->err($e);
            return false;
        }
    }
}