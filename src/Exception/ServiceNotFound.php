<?php

namespace Stratadox\Di\Exception;

use Psr\Container\NotFoundExceptionInterface;
use RuntimeException;
use Throwable;

class ServiceNotFound extends RuntimeException implements NotFoundExceptionInterface
{
    public static function noServiceNamed($serviceName)
    {
        return new static(sprintf(
            'No service registered for %s',
            $serviceName
        ));
    }
}
