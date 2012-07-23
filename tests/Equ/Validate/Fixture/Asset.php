<?php

namespace Validate\Fixture;

use Doctrine\ORM\Mapping as ORM;
use Equ\Object\Validatable;
use Equ\Validate\Object\ObjectInterface as ObjectValidator;

/**
 * @ORM\Entity
 */
class Asset implements Validatable
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(name="file", type="string", length=255, nullable=true)
     * @var string
     */
    private $file;

    /**
     * @ORM\Column(name="original_filename", type="string", length=255, nullable=true)
     * @var string
     */
    private $originalFilename;

    /**
     * @ORM\Column(name="file_hash", type="string", length=255, nullable=true)
     * @var string
     */
    private $fileHash;

    /**
     * @ORM\Column(name="file_size", type="integer", nullable=true)
     * @var int
     */
    private $fileSize;

    /**
     * @ORM\Column(name="mime_type", type="string", length=255, nullable=true)
     * @var string
     */
    private $mimeType;
    
    public function getId()
    {
        return $this->id;
    }

    public function getFile()
    {
        return '/tmp/' . $this->file;
    }

    public function setFile($file)
    {
        $this->file = $file;
    }

    public function getOriginalFilename()
    {
        return $this->originalFilename;
    }

    public function getFileHash()
    {
        return $this->fileHash;
    }

    public function getFileSize()
    {
        return $this->fileSize;
    }

    public function getMimeType()
    {
        return $this->mimeType;
    }

    public static function loadValidators(ObjectValidator $validator)
    {
        $validator->add('fileHash', new \Zend_Validate_StringLength(32, 32));
        $validator->add('file', new \Zend_Validate_File_Exists('.'));
    }

}