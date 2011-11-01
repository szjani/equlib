<?php
define('APPLICATION_ENV', 'testing');

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
  '/development/Frameworks/ZF_1.11_svn/library',
  '/development/Frameworks/zf1-classmap/library',
  get_include_path()
)));

require_once 'ZendX/Loader/AutoloaderFactory.php';
ZendX_Loader_AutoloaderFactory::factory(array(
  'ZendX_Loader_StandardAutoloader' => array(
    'namespaces' => array(
      'Equ'      => __DIR__ . '/../lib/Equ',
      'Doctrine' => '/home/szjani/development/php/libs/Doctrine-2.1/lib/Doctrine',
      'Gedmo'    => '/home/szjani/development/php/libs/l3pp4rd/DoctrineExtensions2.1/lib/Gedmo',
      'DoctrineExtensions' => '/home/szjani/development/php/libs/beberlei/DoctrineExtensions/lib/DoctrineExtensions',
    ),
    'fallback_autoloader' => true,
  ),
));