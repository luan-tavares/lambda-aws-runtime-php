<?php

namespace Runtime;

abstract class Context
{
    public static function create(array $headers): array
    {
        $deadlineMs = isset($headers['lambda-runtime-deadline-ms']) ?
            (int)$headers['lambda-runtime-deadline-ms'] :
            round(microtime(true) * 1000) + 15_000;

        return [
            'callbackWaitsForEmptyEventLoop' => true,
            'functionName' => getenv('AWS_LAMBDA_FUNCTION_NAME') ?: '',
            'functionVersion' => getenv('AWS_LAMBDA_FUNCTION_VERSION') ?: '',
            'invokedFunctionArn' => $headers['lambda-runtime-invoked-function-arn'] ?? '',
            'memoryLimitInMB' => getenv('AWS_LAMBDA_FUNCTION_MEMORY_SIZE') ?: '128',
            'awsRequestId' => $headers['lambda-runtime-aws-request-id'] ?? '',
            'logGroupName' => getenv('AWS_LAMBDA_LOG_GROUP_NAME') ?: '',
            'logStreamName' => getenv('AWS_LAMBDA_LOG_STREAM_NAME') ?: '',
            'getRemainingTimeInMillis' => function () use ($deadlineMs) {
                return max($deadlineMs - round(microtime(true) * 1000), 0);
            }
        ];
    }
}
