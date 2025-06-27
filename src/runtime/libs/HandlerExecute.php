<?php

namespace Runtime;

use Throwable;
use Runtime\Log;

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

            Log::write("⏱️ Handler executado em " . number_format($durationMs, 2) . "ms");

            return is_array($result) ? $result : ['result' => $result];
        } catch (Throwable $e) {
            Log::write("⚠️ Falha ao executar handler: " . $e->getMessage());
            return ['batchItemFailures' => []];
        }
    }
}
