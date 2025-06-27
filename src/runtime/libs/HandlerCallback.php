<?php

namespace Runtime;

use Exception;

abstract class HandlerCallback
{
    private static array $handlers = [];

    public static function get(array $payload): callable
    {
        $handlerPath = self::selectWorker($payload);

        if (!file_exists($handlerPath)) {
            throw new Exception("Arquivo $handlerPath não encontrado.");
        }

        if (!isset(static::$handlers[$handlerPath])) {
            static::$handlers[$handlerPath] = require_once $handlerPath;
        }

        $handler = static::$handlers[$handlerPath];

        if (!is_callable($handler)) {
            throw new Exception("Arquivo $handlerPath não retornou uma função handler válida.");
        }

        return $handler;
    }

    private static function selectWorker(array $payload): string
    {
        $base = getenv('LAMBDA_TASK_ROOT') ?: __DIR__;

        if (isset($payload['Records'])) {
            return $base . '/handlers/sqs.php';
        }

        if (isset($payload['rawPath'])) {
            return $base . '/handlers/apigateway.php';
        }

        if (isset($payload['artisan'])) {
            return $base . '/handlers/worker_artisan.php';
        }

        return $base . '/handlers/generic.php';
    }
}
