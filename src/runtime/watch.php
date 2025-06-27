#!/usr/bin/env php
<?php

use Runtime\Log;

$dir = getenv("LAMBDA_TASK_ROOT") ?? "..";

require $dir . '/vendor/autoload.php';

while (true) {
    Log::write("🟢 Iniciando processo lambda-dev (Swoole)...");

    $cmd = 'php /var/task/runtime/bootstrap_dev.php';
    passthru($cmd, $code);

    Log::write("⚠️ Processo finalizado com código $code");

    usleep(500_000);
}
