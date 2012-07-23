<?php
namespace Equ\Validate;

use PHPUnit_Framework_TestCase;

abstract class BaseTestClass extends PHPUnit_Framework_TestCase
{
    public $backupGlobals = false;

    const TEST_ENTITY_CLASS = "Validate\Fixture\Asset";
    
    /**
     *
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;
    
    public function setUp()
    {
        $classLoader = new \Doctrine\Common\ClassLoader('Validate\Fixture', __DIR__ . '/../');
        $classLoader->register();

        $config = new \Doctrine\ORM\Configuration();
        $config->setMetadataCacheImpl(new \Doctrine\Common\Cache\ArrayCache);
        $config->setQueryCacheImpl(new \Doctrine\Common\Cache\ArrayCache);
        $config->setProxyDir(__DIR__ . '/Proxy');
        $config->setProxyNamespace('Equ\Validate\Proxy');
        $driver = new \Doctrine\ORM\Mapping\Driver\DriverChain();
        $driver->addDriver(
            new \Doctrine\ORM\Mapping\Driver\AnnotationDriver(
                new \Doctrine\Common\Annotations\CachedReader(
                    new \Doctrine\Common\Annotations\AnnotationReader(),
                    new \Doctrine\Common\Cache\ArrayCache()
                ),
                __DIR__ . '/Fixture'
            ), 'Validate\Fixture'
        );
        $config->setMetadataDriverImpl($driver);

        $conn = array(
            'driver' => 'pdo_sqlite',
            'memory' => true,
        );

        $evm = new \Doctrine\Common\EventManager();
        $this->em = \Doctrine\ORM\EntityManager::create($conn, $config, $evm);

        $schemaTool = new \Doctrine\ORM\Tools\SchemaTool($this->em);
        $schemaTool->dropSchema(array());
        $schemaTool->createSchema(array(
            $this->em->getClassMetadata(self::TEST_ENTITY_CLASS)
        ));
    }
}
