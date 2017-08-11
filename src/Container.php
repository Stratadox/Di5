<?php

namespace Stratadox\Di;

use Closure;
use Psr\Container\ContainerInterface as PsrContainerInterface;
use Stratadox\Di\Exception\DependenciesCannotBeCircular;
use Stratadox\Di\Exception\InvalidFactory;
use Stratadox\Di\Exception\InvalidServiceDefinition;
use Stratadox\Di\Exception\InvalidServiceType;
use Stratadox\Di\Exception\ServiceNotFound;
use Exception;

class Container implements ContainerInterface, PsrContainerInterface
{
    protected $remember = [];
    protected $factoryFor = [];
    protected $mustReload = [];
    protected $isCurrentlyResolving = [];

    /**
     * @throws InvalidServiceDefinition
     * @throws ServiceNotFound
     */
    public function get($theService, $mustHaveThisType = '')
    {
        $this->mustKnowAbout($theService);

        if ($this->hasNotYetLoaded($theService) or $this->mustReload[$theService]) {
            $this->remember[$theService] = $this->load($theService);
        }

        $ourService = $this->remember[$theService];
        $this->typeMustCheckOut($theService, $ourService, $mustHaveThisType);
        return $ourService;
    }

    public function has($theService)
    {
        return isset($this->factoryFor[$theService]);
    }

    public function set(
        $theService,
        Closure $producingTheService,
        $cache = true
    ) {
        $this->remember[$theService] = null;
        $this->factoryFor[$theService] = $producingTheService;
        $this->mustReload[$theService] = !$cache;
    }

    public function forget($theService)
    {
        unset($this->remember[$theService]);
        unset($this->factoryFor[$theService]);
        unset($this->mustReload[$theService]);
    }

    /** @throws InvalidServiceDefinition */
    protected function load($theService)
    {
        if (isset($this->isCurrentlyResolving[$theService])) {
            throw DependenciesCannotBeCircular::loopDetectedIn($theService);
        }
        $this->isCurrentlyResolving[$theService] = true;
        $makeTheService = $this->factoryFor[$theService];
        try {
            return $makeTheService();
        } catch (Exception $encounteredException) {
            throw InvalidFactory::threwException($theService, $encounteredException);
        } finally {
            unset($this->isCurrentlyResolving[$theService]);
        }
    }

    private function hasNotYetLoaded($theService)
    {
        return !isset($this->remember[$theService]);
    }

    /** @throws ServiceNotFound */
    private function mustKnowAbout($theService)
    {
        if ($this->has($theService)) return;
        throw ServiceNotFound::noServiceNamed($theService);
    }

    /** @throws InvalidServiceDefinition */
    private function typeMustCheckOut($serviceName, $service, $requiredType)
    {
        if (empty($requiredType)) return;
        if (gettype($service) === $requiredType) return;
        if ($service instanceof $requiredType) return;
        throw InvalidServiceType::serviceIsNotOfType($serviceName, $requiredType);
    }
}
