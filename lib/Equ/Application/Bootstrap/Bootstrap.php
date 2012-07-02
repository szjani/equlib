<?php
namespace Equ\Application\Bootstrap;
use
    Symfony\Component\DependencyInjection,
    Equ\Symfony\Component\ServiceContainerFactory,
    Equ\Controller\Action\Helper\ServiceContainer;

class Bootstrap extends \Zend_Application_Bootstrap_Bootstrap
{

    protected function getMasterConfigFiles()
    {
        return array(
            APPLICATION_PATH . '/configs/production.xml',
            APPLICATION_PATH . '/configs/development.xml'
        );
    }
    
    private function getCache()
    {
        $cache = \Zend_Cache::factory(
            'File',
            'File',
            array(// Frontend Default Options
                'master_files' => $this->getMasterConfigFiles(),
                'automatic_serialization' => true
            ),
            array(// Backend Default Options
                'cache_dir' => APPLICATION_PATH . '/../data/cache'
            )
        );
        return $cache;
    }
    
    public function getContainer()
    {
        $options = $this->getOption('bootstrap');

        if (null === $this->_container && $options['container']['type'] == 'symfony') {
            $container = null;
            $name = 'Container'.$this->getEnvironment().'ServiceContainer';
            $file = APPLICATION_PATH.'/../data/cache/'.$name.'.php';
            
            $cache = $this->getCache();
            $diContainerLoaded = $cache->load('DIContainerLoaded');
            if (!$diContainerLoaded) {
                $cache->save('loaded', 'DIContainerLoaded');
            }
            
            if ($diContainerLoaded && file_exists($file)) {
                require_once $file;
                $container = new $name();
            } else {
                $container = ServiceContainerFactory::getContainer($options['container']);
                $dumper = new DependencyInjection\Dumper\PhpDumper($container);
                file_put_contents($file, $dumper->dump(array('class' => $name)));
            }
            $this->_container = $container;
            \Zend_Controller_Action_HelperBroker::addHelper(new ServiceContainer());
        }
        return parent::getContainer();
    }

}