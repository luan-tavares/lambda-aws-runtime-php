#!/usr/bin/env php
<?php

use Runtime\HandlerExecute;
use Runtime\TimestampLastFileUpdated;
use Swoole\Http\Server;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\Timer;

$dir = getenv("LAMBDA_TASK_ROOT") ?? "..";

require $dir . '/vendor/autoload.php';

$PORT = 9001;


$lastModified = TimestampLastFileUpdated::get($dir);

// Cria servidor HTTP
$server = new Server("0.0.0.0", $PORT);

$server->on("request", function (Request $request, Response $response) {
    if ($request->server['request_method'] !== 'POST' || $request->server['request_uri'] !== '/') {
        $response->status(405);
        $response->header('Content-Type', 'application/json');
        $response->end(json_encode(['error' => 'Method not allowed']));
        return;
    }

    $payload = json_decode($request->rawContent(), true) ?? [];
    $json = runWorkerDirect($payload);

    $response->header('Content-Type', 'application/json');
    $response->end(json_encode($json));
});

// Verifica alterações a cada 1s — se mudou, mata o processo
$timerId = Timer::tick(1000, function () use (&$lastModified, $dir, &$timerId) {
    $current =  TimestampLastFileUpdated::get($dir);

    if ($current > $lastModified) {
        fwrite(STDERR, "----------------------------------\n");
        fwrite(STDERR, "🔁 Detecção de mudança em arquivos. Matando processo para hot reload...\n");
        Timer::clear($timerId); // 🛑 Impede múltiplas execuções
        posix_kill(posix_getpid(), SIGTERM);
    }
});

echo "🚀 Swoole ouvindo em http://localhost:$PORT\n";
$server->start();

function runWorkerDirect(array $payload): array
{
    $MEMORY_SIZE_MB = getenv("DEV_MEMORY_MB") ?? 256;

    $start = hrtime(true);
    $requestId = bin2hex(random_bytes(8));
    $deadline = (int)(microtime(true) * 1000) + 10_000;

    $headers = [
        'lambda-runtime-invoked-function-arn' => 'mock-function',
        'lambda-runtime-deadline-ms' => $deadline,
        'lambda-runtime-aws-request-id' => $requestId
    ];

    fwrite(STDERR, "----------------------------------\n");
    fwrite(STDERR, "START RequestId: $requestId Version: \$LATEST\n");

    try {
        $result = HandlerExecute::handle($payload, $headers);

        $end = hrtime(true);
        $duration = ($end - $start) / 1e6;
        $billed = ceil($duration);
        $memoryUsedBytes = memory_get_usage(true);
        $memoryUsedMb = round($memoryUsedBytes / (1024 * 1024), 2);

        fwrite(STDERR, "END RequestId: $requestId\n");
        fwrite(STDERR, "REPORT RequestId: $requestId Duration: " . number_format($duration, 2) . " ms Billed Duration: $billed ms Memory Size: $MEMORY_SIZE_MB MB Max Memory Used: $memoryUsedMb MB\n");

        return is_array($result) ? $result : ['result' => $result];
    } catch (Throwable $e) {
        fwrite(STDERR, "❌ Erro ao executar handler: {$e->getMessage()}\n");
        fwrite(STDERR, "END RequestId: $requestId\n");
        return ['error' => 'Erro interno no handler'];
    }
}
