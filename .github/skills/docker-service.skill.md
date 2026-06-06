# docker-service

## Quando usar

Use para adicionar servicos ao `docker-compose.yml`.

## Entrada esperada

- Nome do servico.
- Imagem ou build.
- Portas internas.
- Volumes.
- Variaveis de ambiente.

## Saida esperada

- Servico no Compose.
- Labels Traefik quando aplicavel.
- Documentacao atualizada.

## Passos

- Validar se o servico pertence ao ambiente SIGI-SD.
- Adicionar rede `sigi-network`.
- Criar volume persistente se necessario.
- Usar variaveis no `.env.example`.

## Checklist

- Sem segredos hardcoded.
- Healthcheck ou dependencia quando necessario.
- Dominio local documentado.

## Exemplo de prompt

Adicione um servico de armazenamento compativel com S3 para desenvolvimento local.
