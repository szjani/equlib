<?php
namespace Equ\Doctrine\Mapping;
use Gedmo\Mapping\MappedEventSubscriber as GedmoMappedEventSubscriber;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\CachedReader;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Annotations\Reader;
use Gedmo\Mapping\ExtensionMetadataFactory;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\Common\EventArgs;

abstract class MappedEventSubscriber extends GedmoMappedEventSubscriber
{

    public function __construct()
    {
        $reader = null;
        if (version_compare(\Doctrine\Common\Version::VERSION, '2.2.0-DEV', '>=')) {
            $reader = new \Doctrine\Common\Annotations\AnnotationReader();
            \Doctrine\Common\Annotations\AnnotationRegistry::registerAutoloadNamespace(
                'Equ\\Doctrine\\Mapping\\Annotation', __DIR__ . '/../../../'
            );
            $reader = new \Doctrine\Common\Annotations\CachedReader($reader, new ArrayCache());
        } else if (version_compare(\Doctrine\Common\Version::VERSION, '2.1.0RC4-DEV', '>=')) {
            $reader = new \Doctrine\Common\Annotations\AnnotationReader();
            \Doctrine\Common\Annotations\AnnotationRegistry::registerAutoloadNamespace(
                'Equ\\Doctrine\\Mapping\\Annotation', __DIR__ . '/../../../'
            );
            $reader->setDefaultAnnotationNamespace('Doctrine\ORM\Mapping\\');
            $reader = new \Doctrine\Common\Annotations\CachedReader($reader, new ArrayCache());
        } else if (version_compare(\Doctrine\Common\Version::VERSION, '2.1.0-BETA3-DEV', '>=')) {
            $reader = new \Doctrine\Common\Annotations\AnnotationReader();
            $reader->setDefaultAnnotationNamespace('Doctrine\ORM\Mapping\\');
            $reader->setIgnoreNotImportedAnnotations(true);
            $reader->setAnnotationNamespaceAlias('Equ\\Doctrine\\Mapping\\Annotation\\', 'equ');
            $reader->setEnableParsePhpImports(false);
            $reader->setAutoloadAnnotations(true);
            $reader = new \Doctrine\Common\Annotations\CachedReader(
                    new \Doctrine\Common\Annotations\IndexedReader($reader), new ArrayCache()
            );
        } else {
            $reader = new \Doctrine\Common\Annotations\AnnotationReader();
            $reader->setAutoloadAnnotations(true);
            $reader->setAnnotationNamespaceAlias('Equ\\Doctrine\\Mapping\\Annotation\\', 'equ');
            $reader->setDefaultAnnotationNamespace('Doctrine\ORM\Mapping\\');
        }

        $this->setAnnotationReader($reader);
//    return $this->defaultAnnotationReader;
    }

}
