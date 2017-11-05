<?php

namespace Backend\Service;

use Cake\Core\App;
use Cake\Core\ObjectRegistry;
use Cake\Event\EventDispatcherTrait;
use Cake\Event\EventManager;

class ServiceRegistry extends ObjectRegistry
{

    use EventDispatcherTrait;

    /**
     * Constructor
     *
     * @param \Cake\Event\EventManager $events Event Manager that services should bind to.
     *   Typically this is the global manager.
     */
    public function __construct(EventManager $events)
    {
        $this->eventManager($events);
    }

    /**
     * Resolve a service class name.
     *
     * Part of the template method for Cake\Utility\ObjectRegistry::load()
     *
     * @param string $class Partial class name to resolve.
     * @return string|false Either the correct class name or false.
     */
    protected function _resolveClassName($class)
    {
        return App::className($class, 'Service', 'Service');
    }

    /**
     * Throws an exception when a component is missing.
     *
     * Part of the template method for Cake\Utility\ObjectRegistry::load()
     *
     * @param string $class The classname that is missing.
     * @param string $plugin The plugin the component is missing in.
     * @return void
     * @throws \RuntimeException
     */
    protected function _throwMissingClassError($class, $plugin)
    {
        throw new \RuntimeException("Unable to find '$class' service.");
    }

    /**
     * Create the services instance.
     *
     * Part of the template method for Cake\Utility\ObjectRegistry::load()
     *
     * @param string $class The classname to create.
     * @param string $alias The alias of the service.
     * @param array $config An array of config to use for the service.
     * @return \Backend\BackendService The constructed service class.
     */
    protected function _create($class, $alias, $config)
    {
        $instance = new $class($this, $config);
        $this->eventManager()->on($instance);

        return $instance;
    }
}