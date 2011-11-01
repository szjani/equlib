<?php

namespace Equ\Doctrine\FileStore\Mapping\Driver;

use Gedmo\Mapping\Driver,
 Doctrine\Common\Annotations\AnnotationReader,
 Doctrine\Common\Persistence\Mapping\ClassMetadata,
 Gedmo\Exception\InvalidMappingException,
 Gedmo\Mapping\Driver\AnnotationDriverInterface;

class Annotation implements AnnotationDriverInterface {
  const ANNOTATION_FILESTORE = 'Equ\\Doctrine\\Mapping\\Annotation\\FileStore';
  const ANNOTATION_FILENAME = 'Equ\\Doctrine\\Mapping\\Annotation\\Filename';
  const ANNOTATION_ORIGINAL_FILENAME = 'Equ\\Doctrine\\Mapping\\Annotation\\OriginalFilename';
  const ANNOTATION_SIZE = 'Equ\\Doctrine\\Mapping\\Annotation\\Size';
  const ANNOTATION_MD5HASH = 'Equ\\Doctrine\\Mapping\\Annotation\\Md5Hash';
  const ANNOTATION_MIMETYPE = 'Equ\\Doctrine\\Mapping\\Annotation\\MimeType';

  private $validStringTypes = array(
    'string',
  );
  private $validNumericTypes = array(
    'integer',
    'smallint',
    'bigint',
  );
  
  /**
   * Annotation reader instance
   *
   * @var object
   */
  private $reader;

  /**
   * {@inheritDoc}
   */
  public function setAnnotationReader($reader) {
    $this->reader = $reader;
  }

  /**
   * Checks if $field type is valid
   *
   * @param ClassMetadata $meta
   * @param string $field
   * @return boolean
   */
  protected function isValidField($meta, $field, $types) {
    $mapping = $meta->getFieldMapping($field);
    return $mapping && in_array($mapping['type'], $types);
  }

  /**
   * @param ClassMetadata $meta
   * @param array $config
   */
  public function readExtendedMetadata(ClassMetadata $meta, array &$config) {
    $reader = $this->reader;
    $class = $meta->getReflectionClass();
    
    if ($annot = $reader->getClassAnnotation($class, self::ANNOTATION_FILESTORE)) {
      if (!file_exists($annot->path) || !is_writable($annot->path) || !is_dir($annot->path)) {
        throw new InvalidMappingException("Directory '{$annot->path}' has be a writtable directory!");
      }
      $config['path'] = rtrim($annot->path, '/');
      
      if (!\in_array($annot->method, array('move', 'copy'))) {
        throw new InvalidMappingException("Method '{$annot->method}' has to be 'move' or 'copy'!");
      }
      $config['method'] = $annot->method;
    }

    // property annotations
    foreach ($class->getProperties() as $property) {
      if ($meta->isMappedSuperclass && !$property->isPrivate() ||
        $meta->isInheritedField($property->name) ||
        isset($meta->associationMappings[$property->name]['inherited'])
      ) {
        continue;
      }
      // filename
      if ($filename = $reader->getPropertyAnnotation($property, self::ANNOTATION_FILENAME)) {
        $field = $property->getName();
        if (!$meta->hasField($field)) {
          throw new InvalidMappingException("Unable to find 'filename' - [{$field}] as mapped property in entity - {$meta->name}");
        }
        if (!$this->isValidField($meta, $field, $this->validStringTypes)) {
          throw new InvalidMappingException("FileStore filename field - [{$field}] type is not valid and must be 'string' in class - {$meta->name}");
        }
        $config['filename'] = $field;
      }
      // originalFilename
      if ($originalFilename = $reader->getPropertyAnnotation($property, self::ANNOTATION_ORIGINAL_FILENAME)) {
        $field = $property->getName();
        if (!$meta->hasField($field)) {
          throw new InvalidMappingException("Unable to find 'originalFilename' - [{$field}] as mapped property in entity - {$meta->name}");
        }
        if (!$this->isValidField($meta, $field, $this->validStringTypes)) {
          throw new InvalidMappingException("FileStore originalFilename field - [{$field}] type is not valid and must be 'string' in class - {$meta->name}");
        }
        $config['originalFilename'] = $field;
      }
      // size
      if ($size = $reader->getPropertyAnnotation($property, self::ANNOTATION_SIZE)) {
        $field = $property->getName();
        if (!$this->isValidField($meta, $field, $this->validNumericTypes)) {
          throw new InvalidMappingException("FileStore size field - [{$field}] type is not valid and must be 'integer' in class - {$meta->name}");
        }
        $config['size'] = $field;
      }
      // md5Hash
      if ($md5Hash = $reader->getPropertyAnnotation($property, self::ANNOTATION_MD5HASH)) {
        $field = $property->getName();
        if (!$meta->hasField($field)) {
          throw new InvalidMappingException("Unable to find 'md5Hash' - [{$field}] as mapped property in entity - {$meta->name}");
        }
        if (!$this->isValidField($meta, $field, $this->validStringTypes)) {
          throw new InvalidMappingException("FileStore md5Hash field - [{$field}] type is not valid and must be 'string' in class - {$meta->name}");
        }
        $config['md5Hash'] = $field;
      }
      // mimetype
      if ($mimeType = $reader->getPropertyAnnotation($property, self::ANNOTATION_MIMETYPE)) {
        $field = $property->getName();
        if (!$meta->hasField($field)) {
          throw new InvalidMappingException("Unable to find 'mimeType' - [{$field}] as mapped property in entity - {$meta->name}");
        }
        if (!$this->isValidField($meta, $field, $this->validStringTypes)) {
          throw new InvalidMappingException("FileStore mimeType field - [{$field}] type is not valid and must be 'string' in class - {$meta->name}");
        }
        $config['mimeType'] = $field;
      }
    }
  }

  /**
   * @param ClassMetadata $meta
   * @param array $config
   */
  public function validateFullMetadata(ClassMetadata $meta, array $config) {
    $missingFields = array();
    if (!isset($config['filename'])) {
      $missingFields[] = 'filename';
    }
    //if ($missingFields) {
    //  throw new InvalidMappingException("Missing properties: " . implode(', ', $missingFields) . " in class - {$meta->name}");
    //}
  }
  
  /**
   * Passes in the original driver
   *
   * @param $driver
   * @return void
   */
  public function setOriginalDriver($driver) {
    
  }

}