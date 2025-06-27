<?php

namespace Runtime;

use Throwable;

class HandlerExecute
{
    public static function handle(array $payload, array $headers): array
    {
        try {
            $handler = HandlerCallback::get($payload);

            $context = Context::create($headers);

            $start = hrtime(true);
            $result = $handler($payload, $context);
            $end = hrtime(true);

            $durationMs = ($end - $start) / 1e6;

            file_put_contents('php://stderr', "⏱️ Handler executado em " . number_format($durationMs, 2) . "ms\n");

            return is_array($result) ? $result : ['result' => $result];
        } catch (Throwable $e) {
            file_put_contents('php://stderr', "⚠️ Falha ao executar handler: " . $e->getMessage() . "\n");
            return ['batchItemFailures' => []];
        }
    }
}
