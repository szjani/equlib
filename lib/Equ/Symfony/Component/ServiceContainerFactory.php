<?php
namespace Equ\Symfony\Component;
use
    Symfony\Component\DependencyInjection,
    Equ\Exception\UnexpectedValueException,
    Symfony\Component\Config\FileLocator;

class ServiceContainerFactory
{

    protected static $_container;

    public static function getContainer(array $files)
    {
        self::$_container = new DependencyInjection\ContainerBuilder();
        foreach ($files as $file) {
            self::_loadConfigFile($file);
        }
        return self::$_container;
    }

    protected static function _loadConfigFile($file)
    {
        $suffix = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        $loader = null;
        switch ($suffix) {
            case 'xml':
                $loader = new DependencyInjection\Loader\XmlFileLoader(self::$_container, new FileLocator());
                break;

            case 'yml':
                $loader = new DependencyInjection\Loader\YamlFileLoader(self::$_container, new FileLocator());
                break;

            default:
                throw new UnexpectedValueException("Invalid configuration file provided; unknown config type '$suffix'");
        }
        $loader->load($file);
    }

}