curl -X POST http://localhost:9001 \
  -H "Content-Type: application/json" \
  -H "lambda-runtime-aws-request-id: test-api-req-id-456" \
  -H "lambda-runtime-deadline-ms: $(( $(date +%s%3N) + 15000 ))" \
  -H "lambda-runtime-invoked-function-arn: arn:aws:lambda:us-east-1:123456789012:function:api-func" \
  -d '{
    "version": "2.0",
    "routeKey": "POST /teste",
    "rawPath": "/teste",
    "rawQueryString": "",
    "headers": {
      "content-type": "application/json",
      "host": "example.execute-api.us-east-1.amazonaws.com",
      "x-forwarded-for": "1.2.3.4",
      "x-forwarded-proto": "https",
      "x-forwarded-port": "443"
    },
    "requestContext": {
      "accountId": "123456789012",
      "apiId": "example",
      "domainName": "example.execute-api.us-east-1.amazonaws.com",
      "domainPrefix": "example",
      "http": {
        "method": "POST",
        "path": "/teste",
        "protocol": "HTTP/1.1",
        "sourceIp": "1.2.3.4",
        "userAgent": "curl/8.5.0"
      },
      "requestId": "req-id-test-456",
      "routeKey": "POST /teste",
      "stage": "$default",
      "time": "27/Jun/2025:12:34:56 +0000",
      "timeEpoch": 1751037296000
    },
    "body": "{\"mensagem\": \"olá do api gate
