<?php
namespace Equ\Doctrine\Mapping\Annotation;
use Doctrine\Common\Annotations\Annotation;

/**
  * @Annotation
  */
final class FileStore extends Annotation
{

    /**
      * Directory path
      *
      * @var string
      */
    public $path;

    /**
      * move|copy
      * 
      * @var string
      */
    public $method;

}