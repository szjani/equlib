<?php
namespace Equ\Doctrine\Session;

use DateTime;

interface EntityInterface
{
    public static function expiresField();
    
    /**
     * @return string
     */
    public function getId();
    
    /**
     * @param string $sessionId
     */
    public function setId($sessionId);
    
    /**
     * @return int
     */
    public function getLifeTime();
    
    /**
     * @return DateTime
     */
    public function getLastModified();
    
    /**
     * It must modify expires and lastModified fields.
     * 
     * @param int $lifeTime
     */
    public function setLifeTime($lifeTime);
    
    /**
     * @return mixed
     */
    public function getData();
    
    /**
     * It must modify lastModified field.
     * 
     * @param string $data
     */
    public function setData($data);
    
    /**
     * @return DateTime
     */
    public function getExpires();
    
    /**
     * @return string
     */
    public function getSessionName();
    
    /**
     * @param string $name
     */
    public function setSessionName($name);
    
    /**
     * @return string
     */
    public function getSavePath();
    
    /**
     * @param string $savePath
     */
    public function setSavePath($savePath);
    
}