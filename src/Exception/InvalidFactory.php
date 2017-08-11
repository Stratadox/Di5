<?php

namespace Stratadox\Di\Exception;

use RuntimeException;
use Exception;

class InvalidFactory extends RuntimeException implements InvalidServiceDefinition
{
    public static function threwException(
        $serviceName,
        Exception $exception
    ) {
        return new static(sprintf(
            'Service `%s` was configured incorrectly and could not be created: %s',
            $serviceName,
            $exception->getMessage()
        ), 0, $exception);
    }
}
