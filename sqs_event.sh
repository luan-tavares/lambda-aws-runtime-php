#!/bin/bash

curl -X POST http://localhost:9001 \
  -H "Content-Type: application/json" \
  -H "lambda-runtime-aws-request-id: test-req-id-123" \
  -H "lambda-runtime-deadline-ms: $(( $(date +%s%3N) + 15000 ))" \
  -H "lambda-runtime-invoked-function-arn: arn:aws:lambda:us-east-1:123456789012:function:my-func" \
  -d '{
    "Records": [
      {
        "messageId": "abc123",
        "receiptHandle": "some-receipt-handle",
        "body": "{\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"data\":{\"commandName\":\"App\\\\Jobs\\\\ExampleJob\",\"command\":\"O:23:\\\"App\\\\Jobs\\\\ExampleJob\\\":1:{s:7:\\\"data\\\";s:11:\\\"hello world\\\";}\"}}",
        "attributes": {
          "ApproximateReceiveCount": "1"
        },
        "messageAttributes": {},
        "eventSource": "aws:sqs",
        "eventSourceARN": "arn:aws:sqs:us-east-1:123456789012:example-queue",
        "awsRegion": "us-east-1"
      }
    ]
  }'
