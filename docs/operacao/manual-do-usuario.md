# Manual do usuario

Este manual mostra como preparar, subir, acessar e operar o ambiente local do SIGI-SD desde o inicio.

## 1. Pre-requisitos

Use Windows com WSL2 e Docker funcionando dentro do WSL.

No terminal do WSL, verifique:

```bash
docker version
docker compose version
```

Se `make` ou `curl` nao estiverem instalados:

```bash
sudo apt update
sudo apt install -y make curl
```

## 2. Entrar na pasta do projeto

Se o projeto estiver no Windows em `C:\Users\Public\sigi-sd`, acesse pelo WSL assim:

```bash
cd /mnt/c/Users/Public/sigi-sd
```

Opcionalmente, para melhor desempenho, copie para o filesystem Linux:

```bash
mkdir -p ~/projetos
cp -r /mnt/c/Users/Public/sigi-sd ~/projetos/
cd ~/projetos/sigi-sd
```

## 3. Preparar arquivo de ambiente

Crie o `.env` local:

```bash
make setup
```

Se quiser fazer manualmente:

```bash
cp .env.example .env
```

Edite o `.env` apenas se precisar trocar senhas, nomes de banco ou URLs locais.

## 4. Subir toda a aplicacao

O comando principal e:

```bash
make up
```

Na primeira execucao, se quiser garantir build atualizado:

```bash
make rebuild
```

Verifique os containers:

```bash
make ps
```

Todos os servicos devem aparecer como `Up`.

## 5. Acessar os servicos

- API Symfony: http://api.sigi.localhost
- Chatwoot: http://chat.sigi.localhost
- Evolution API: http://whatsapp.sigi.localhost
- Botpress: http://bot.sigi.localhost
- Ollama: http://ia.sigi.localhost
- Qdrant: http://qdrant.sigi.localhost
- Portainer: http://portainer.sigi.localhost
- Dashboard Traefik: http://localhost:18080

## 6. Subir apenas uma parte da aplicacao

Nem sempre e necessario subir tudo. Use os comandos individuais:

Subir apenas Symfony:

```bash
make up-api
```

Tambem funciona:

```bash
make up-symfony
```

Esse comando sobe `symfony-api`, `postgres`, `redis` e `traefik`.

Subir apenas IA local:

```bash
make up-ia
```

Tambem funciona:

```bash
make up-ai
```

Esse comando sobe `ollama`, `qdrant` e `traefik`.

Subir apenas Chatwoot:

```bash
make up-chat
```

Tambem funciona:

```bash
make up-chatwoot
```

Esse comando sobe `chatwoot`, `postgres`, `redis` e `traefik`.

Subir apenas WhatsApp/Evolution API:

```bash
make up-whatsapp
```

Tambem funciona:

```bash
make up-evolution
```

Subir apenas Botpress:

```bash
make up-bot
```

Tambem funciona:

```bash
make up-botpress
```

Subir apenas bancos:

```bash
make up-db
```

Subir apenas proxy:

```bash
make up-proxy
```

Subir apenas Portainer:

```bash
make up-portainer
```

Parar servicos especificos:

```bash
make stop-api
make stop-ia
make stop-chat
make stop-whatsapp
make stop-bot
```

Ver logs especificos:

```bash
make logs-api
make logs-ia
make logs-chat
make logs-whatsapp
make logs-bot
make logs-proxy
```

## 7. Primeiro acesso ao Chatwoot

Abra:

```text
http://chat.sigi.localhost
```

O Chatwoot deve redirecionar para:

```text
/installation/onboarding
```

Siga o onboarding na tela para criar a instalacao inicial, usuario administrador e configuracoes basicas.

## 8. Verificar saude dos endpoints

Execute:

```bash
make health
```

Resultados esperados:

- `api.sigi.localhost`: HTTP 200.
- `chat.sigi.localhost`: HTTP 302 para onboarding ou dashboard.
- `bot.sigi.localhost`: HTTP 302 para `/admin`.
- `whatsapp.sigi.localhost`: HTTP 200.
- `ia.sigi.localhost/api/version`: JSON com versao do Ollama.
- `qdrant.sigi.localhost/collections`: JSON com `status: ok`.

## 9. Ver logs

Todos os logs:

```bash
make logs
```

Logs de um servico especifico:

```bash
docker compose logs -f symfony-api
docker compose logs -f chatwoot
docker compose logs -f traefik
```

## 10. Rodar comandos Symfony

Abrir shell no container:

```bash
make shell-api
```

Comandos comuns:

```bash
php bin/console --version
php bin/console doctrine:migrations:migrate
php bin/console cache:clear
php bin/console sass:build
```

Direto pelo Makefile:

```bash
make migrate
make cache-clear
```

## 11. Parar ou reiniciar

Parar containers:

```bash
make down
```

Reiniciar tudo:

```bash
make restart
```

## 12. Problemas comuns

Porta `80` ocupada:

- Pare o servico que usa a porta 80 no Windows/WSL.
- Depois rode `make up` novamente.

Porta `18080` ocupada:

- Altere o mapeamento do Traefik no `docker-compose.yml`.
- Exemplo: trocar `18080:8080` por `18081:8080`.

Chatwoot sai com erro de banco:

```bash
docker compose logs chatwoot
docker compose logs postgres
```

O Postgres usado pelo projeto e `pgvector/pgvector:pg16`, necessario para a extensao `vector` do Chatwoot.

Symfony mostra erro de CSS/Sass:

```bash
docker compose exec symfony-api php bin/console sass:build
docker compose restart symfony-api
```

## 13. Atualizar depois de mudancas no projeto

Quando arquivos Docker ou dependencias mudarem:

```bash
make rebuild
make ps
make health
```

## 14. Encerramento seguro

Para encerrar o ambiente local:

```bash
make down
```

Os dados persistem nos volumes Docker. Para apagar volumes, use comandos Docker manualmente apenas quando tiver certeza de que quer perder os dados locais.
