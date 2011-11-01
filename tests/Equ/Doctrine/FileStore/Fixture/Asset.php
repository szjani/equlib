<?php
namespace FileStore\Fixture;
use Equ\Doctrine\Mapping\Annotation as Equ;

/**
 * @Equ\FileStore(path="/tmp", method="copy")
 * @Entity
 */
class Asset {

  /**
   * @Id
   * @GeneratedValue
   * @Column(type="integer")
   */
  private $id;

  /**
   * @Column(name="file", type="string", length=255)
   * @Equ\Filename
   * @var string
   */
  private $file;

  /**
   * @Column(name="original_filename", type="string", length=255)
   * @Equ\OriginalFilename
   * @var string
   */
  private $originalFilename;

  /**
   * @Column(name="file_hash", type="string", length=255)
   * @Equ\Md5Hash
   * @var string
   */
  private $fileHash;

  /**
   * @Column(name="file_size", type="integer")
   * @Equ\Size
   * @var int
   */
  private $fileSize;

  /**
   * @Column(name="mime_type", type="string", length=255)
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