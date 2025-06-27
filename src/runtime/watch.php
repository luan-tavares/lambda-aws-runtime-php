#!/usr/bin/env php
<?php

while (true) {
    echo "🟢 Iniciando processo lambda-dev (Swoole)...\n";

    $cmd = 'php /var/task/runtime/bootstrap_dev.php';
    passthru($cmd, $code);

    echo "⚠️ Processo finalizado com código $code\n";

    usleep(500_000);
}
