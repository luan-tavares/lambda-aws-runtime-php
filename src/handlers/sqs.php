<?php

use Runtime\Log;

return function (array $payload, array $context) {
    try {
        $event = $payload;
        $records = isset($event['Records']) && is_array($event['Records']) ? $event['Records'] : [];

        if (count($records) === 0) {
            Log::write("⚠️ Nenhum recorde encontrado.");
            return ['batchItemFailures' => []];
        }
        /**cdddasf */

        Log::write("🧮 Batch: " . count($records) . " records");

        $TOTAL_TIMEOUT_MS = $context['getRemainingTimeInMillis']();
        $GORDURA_PCT = 0.15;
        $GORDURA_MINIMA_MS = 2000;
        $MARGEM_FINAL_MS = max($GORDURA_MINIMA_MS, $TOTAL_TIMEOUT_MS * $GORDURA_PCT);
        $DEADLINE = (int)(microtime(true) * 1000) + $TOTAL_TIMEOUT_MS - $MARGEM_FINAL_MS;

        $failures = [];
        $tempoTotal = 0;
        $processados = 0;

        foreach ($records as $record) {
            $restante = $DEADLINE - round(microtime(true) * 1000);
            $tempoEstimado = $processados === 0 ? 0 : ceil($tempoTotal / $processados);

            if ($processados > 0 && $restante < $tempoEstimado) {
                Log::write("📨 Processando {$record['messageId']}");
                Log::write("⏳ Parando: tempo restante ({$restante}ms) < média estimada ({$tempoEstimado}ms)");
                $failures[] = ['itemIdentifier' => $record['messageId']];
                continue;
            }

            try {
                Log::write("📨 Processando {$record['messageId']}");
                Log::write("📝 Conteúdo: {$record['body']}");
                $t0 = round(microtime(true) * 1000);
                processar($record);
                $tempoTotal += round(microtime(true) * 1000) - $t0;
                $processados++;
            } catch (Throwable $e) {
                Log::write("❌ Falha ao processar {$record['messageId']}: " . $e->getMessage() . "");
                $failures[] = ['itemIdentifier' => $record['messageId']];
            }
        }

        return ['batchItemFailures' => $failures];
    } catch (Throwable $err) {
        Log::write("❌ Erro fatal no worker: " . $err->getMessage() . "");
        return ['batchItemFailures' => []];
    }
};

// Função mock de processamento, simula delay e falha
function processar(array $record): void
{
    if (strpos($record['body'], 'fail') !== false) {
        throw new Exception('Erro simulado');
    }

    // Simula delay entre 300–400ms
    usleep(300000 + rand(0, 100000));
}
