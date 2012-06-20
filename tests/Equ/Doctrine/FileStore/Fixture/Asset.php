<?php
namespace FileStore\Fixture;
use Equ\Doctrine\Mapping\Annotation as Equ;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @Equ\FileStore(path="/tmp", method="copy")
 */
class Asset {

  /**
   * @ORM\Id
   * @ORM\GeneratedValue
   * @ORM\Column(type="integer")
   */
  private $id;

  /**
   * @ORM\Column(name="file", type="string", length=255)
   * @Equ\Filename
   * @var string
   */
  private $file;

  /**
   * @ORM\Column(name="original_filename", type="string", length=255)
   * @Equ\OriginalFilename
   * @var string
   */
  private $originalFilename;

  /**
   * @ORM\Column(name="file_hash", type="string", length=255)
   * @Equ\Md5Hash
   * @var string
   */
  private $fileHash;

  /**
   * @ORM\Column(name="file_size", type="integer")
   * @Equ\Size
   * @var int
   */
  private $fileSize;

  /**
   * @ORM\Column(name="mime_type", type="string", length=255)
   * @Equ\MimeType
   * @var string
   */
  private $mimeType;

  public function getFile() {
    return '/tmp/' . $this->file;
  }

  public function setFile($file) {
    $this->file = $file;
  }

  public function getOriginalFilename() {
    return $this->originalFilename;
  }

  public function getFileHash() {
    return $this->fileHash;
  }

  public function getFileSize() {
    return $this->fileSize;
  }

  public function getMimeType() {
    return $this->mimeType;
  }

}