<?php

namespace Runtime;

class Log
{
    public static function write(string $mensagem): void
    {
        $isDev = getenv('IS_DEV') === 'true';

        if ($isDev) {
            $timestamp = date('[Y-m-d H:i:s]');
            $linha = "{$timestamp} {$mensagem}\n";
        } else {
            $linha = $mensagem . "\n";
        }

        fwrite(STDERR, $linha);
    }
}
