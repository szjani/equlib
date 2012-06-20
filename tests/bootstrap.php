<?php
define('APPLICATION_ENV', 'testing');

// ==== Modify these values! ==== //
define('PROJECT_CACHE_PREFIX', 'equlib_');

$zfDir         = '/development/Frameworks/ZF_1.11_svn';
$doctrineDir   = '/development/Frameworks/Doctrine-2.2';
$fixturesDir   = '/development/Frameworks/doctrine-fixtures';
$migrationsDir = '/development/Frameworks/migrations';
// ============================== //

$zfDebugDir    = __DIR__ . '/../library/ZFDebug/library';
$zfDir .= '/library';
$sources = array(
  'Zend'     => array($zfDir, '_'),
  'ZFDebug'  => array($zfDebugDir, '_'),
  'Doctrine\ORM' => $doctrineDir . '/lib',
  'Doctrine\Common\DataFixtures' => $fixturesDir . '/lib',
  'Doctrine\DBAL\Migrations'     => $migrationsDir . '/lib',
  'Doctrine\Common' => $doctrineDir . '/lib/vendor/doctrine-common/lib',
  'Doctrine\DBAL'   => $doctrineDir . '/lib/vendor/doctrine-dbal/lib',
  'Gedmo'    => __DIR__ . '/../library/Gedmo/lib',
  'Equ'      => __DIR__ . '/../lib',
  'Symfony'  => __DIR__ . '/../library',
);

set_include_path(implode(PATH_SEPARATOR, array(
  $zfDir, $zfDebugDir, get_include_path()
)));

require_once __DIR__ . '/../library/Symfony/Component/ClassLoader/UniversalClassLoader.php';

use Symfony\Component\ClassLoader\UniversalClassLoader;
$loader = new UniversalClassLoader();
$loader->register();

foreach ($sources as $namespace => $source) {
  $dir = $source;
  $separator = '\\';
  if (is_array($source)) {
    $dir = array_shift($source);
    if (!empty($source)) {
      $separator = array_shift($source);
      $loader->registerPrefix($namespace, $dir);
    }
  } else {
    $loader->registerNamespace($namespace, $dir);
  }
}
Doctrine\Common\Annotations\AnnotationRegistry::registerAutoloadNamespace(
  'Doctrine\ORM\Mapping',
  $sources['Doctrine\ORM']
);