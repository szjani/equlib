<?php
namespace Equ\Application\Bootstrap;

use Symfony\Component\DependencyInjection;
use Equ\Symfony\Component\ServiceContainerFactory;
use Equ\Controller\Action\Helper\ServiceContainer;
use Zend_Application_Bootstrap_Bootstrap;
use Zend_Controller_Action_HelperBroker;
use Zend_Cache;

/**
 * Bootstrap class which supports Symfony DI Container.
 *
 * @category    Equ
 * @package     Equ\Application
 * @subpackage  Bootstrap
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @author      Szurovecz János <szjani@szjani.hu>
 */
abstract class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    const DI_CACHE_KEY = 'DIContainerLoaded';
    
    protected abstract function getMasterConfigFiles();

    protected abstract function getConfigCacheDir();
    
    protected function getLeafConfigFiles()
    {
        $options = $this->getOption('bootstrap');
        return $options['container']['configFiles'];
    }

    private function getCache()
    {
        $cache = Zend_Cache::factory(
            'File',
            'File',
            array(// Frontend Default Options
                'master_files' => $this->getMasterConfigFiles(),
                'automatic_serialization' => true
            ),
            array(// Backend Default Options
                'cache_dir' => $this->getConfigCacheDir()
            )
        );
        return $cache;
    }

    public function getContainer()
    {
        if (null === $this->_container) {
            $container = null;
            $name = 'Container' . ucfirst($this->getEnvironment()) . 'ServiceContainer';
            $file = rtrim($this->getConfigCacheDir(), '/') . '/' . $name . '.php';

            $cache = $this->getCache();
            $diContainerLoaded = $cache->load(self::DI_CACHE_KEY);
            if (!$diContainerLoaded) {
                $cache->save('loaded', self::DI_CACHE_KEY);
            }

            if ($diContainerLoaded && file_exists($file)) {
                require_once $file;
                $container = new $name();
            } else {
                $container = ServiceContainerFactory::getContainer($this->getLeafConfigFiles());
                $dumper = new DependencyInjection\Dumper\PhpDumper($container);
                file_put_contents($file, $dumper->dump(array('class' => $name)));
            }
            $this->_container = $container;
            Zend_Controller_Action_HelperBroker::addHelper(new ServiceContainer($container));
        }
        return parent::getContainer();
    }

}