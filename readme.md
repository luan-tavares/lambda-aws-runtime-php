# Lambda AWS Runtime PHP

Runtime customizado para simular a execução local de funções AWS Lambda em PHP com Swoole.

---

## 📑 Sumário

1. [Instalação](#1---instalação)  
2. [Teste](#2---teste)  
   - [Simulando SQS](#simulando-sqs)  
   - [Simulando API Gateway](#simulando-api-gateway)  
3. [Deploy em Produção](#3---deploy-em-produção)

---

## 1 - Instalação

### 1.1 Clone o repositório

```bash
git clone https://github.com/luan-tavares/lambda-aws-runtime-php
```

---

### 1.2 Entre no diretório do projeto

```bash
cd lambda-aws-runtime-php/src
```

---

### 1.3 Instale as dependências PHP

```bash
composer install
```

---

### 1.4 Volte para a raiz da runtime

```bash
cd ..
```

---

### 1.5 Construa a imagem Docker

```bash
docker compose build
```

**ou**

```bash
docker-compose build
```

---

## 2 - Teste

Com o container rodando, envie requisições POST para o endpoint local:

```bash
curl -X POST http://localhost:9001 \
  -H "Content-Type: application/json" \
  -d '<payload-aqui>'
```

---

### 🔁 Simulando SQS

```bash
curl -X POST http://localhost:9001 \
  -H "Content-Type: application/json" \
  -d '{
    "Records": [
      {
        "messageId": "abc123",
        "receiptHandle": "algum-handle",
        "body": "{\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"data\":{\"commandName\":\"App\\\\Jobs\\\\Teste\",\"command\":\"O:17:\\\"App\\\\Jobs\\\\Teste\\\":1:{s:4:\\\"data\\\";s:9:\\\"exemplo\\\";}\"}}",
        "attributes": {
          "ApproximateReceiveCount": "1"
        },
        "messageAttributes": {},
        "eventSource": "aws:sqs",
        "eventSourceARN": "arn:aws:sqs:us-east-1:123456789012:queue-name",
        "awsRegion": "us-east-1"
      }
    ]
  }'
```

---

### 🌐 Simulando API Gateway

```bash
curl -X POST http://localhost:9001 \
  -H "Content-Type: application/json" \
  -d '{
    "version": "2.0",
    "routeKey": "POST /teste",
    "rawPath": "/teste",
    "rawQueryString": "",
    "headers": {
      "content-type": "application/json",
      "host": "localhost",
      "x-forwarded-for": "127.0.0.1",
      "x-forwarded-proto": "http",
      "x-forwarded-port": "9001",
      "user-agent": "curl/8.0.1"
    },
    "requestContext": {
      "http": {
        "method": "POST",
        "path": "/teste"
      }
    },
    "body": "{\"mensagem\": \"Olá do API Gateway\"}",
    "isBase64Encoded": false
  }'
```

---

## 3 - Deploy em Produção

Aguarde...
