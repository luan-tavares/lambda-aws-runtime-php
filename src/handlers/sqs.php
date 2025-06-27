<?php

return function (array $payload, array $context) {
    try {
        $event = $payload;
        $records = isset($event['Records']) && is_array($event['Records']) ? $event['Records'] : [];

        if (count($records) === 0) {
            file_put_contents('php://stderr', "⚠️ Nenhum recorde encontrado.\n");
            return ['batchItemFailures' => []];
        }
        /**cdddasf */

        file_put_contents('php://stderr', "🧮 Batch: " . count($records) . " records\n");

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
                file_put_contents('php://stderr', "📨 Processando {$record['messageId']}\n");
                file_put_contents('php://stderr', "⏳ Parando: tempo restante ({$restante}ms) < média estimada ({$tempoEstimado}ms)\n");
                $failures[] = ['itemIdentifier' => $record['messageId']];
                continue;
            }

            try {
                file_put_contents('php://stderr', "📨 -Processando {$record['messageId']}\n");
                file_put_contents('php://stderr', "📝 Conteúdo: {$record['body']}\n");
                $t0 = round(microtime(true) * 1000);
                processar($record);
                $tempoTotal += round(microtime(true) * 1000) - $t0;
                $processados++;
            } catch (Throwable $e) {
                file_put_contents('php://stderr', "❌ Falha ao processar {$record['messageId']}: " . $e->getMessage() . "\n");
                $failures[] = ['itemIdentifier' => $record['messageId']];
            }
        }

        return ['batchItemFailures' => $failures];
    } catch (Throwable $err) {
        file_put_contents('php://stderr', "❌ Erro fatal no worker: " . $err->getMessage() . "\n");
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
