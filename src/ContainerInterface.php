<?php

namespace Stratadox\Di;

use Closure;

interface ContainerInterface
{
    /**
     * @param string $serviceName
     * @param Closure $factory
     * @param bool $useCache
     */
    public function set($serviceName, Closure $factory, $useCache = true);

    /**
     * @param string $serviceName
     * @param string $type
     * @return mixed
     */
    public function get($serviceName, $type = '');

    /**
     * @param string $serviceName
     * @return boolean
     */
    public function has($serviceName);

    /**
     * @param string $serviceName
     */
    public function forget($serviceName);
}
