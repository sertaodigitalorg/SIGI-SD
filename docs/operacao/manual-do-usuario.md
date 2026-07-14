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

- Admin Hub Symfony: http://admin.sigi.localhost
- Chatwoot: http://chat.sigi.localhost
- WhatsApp oficial: configurado no Chatwoot via Meta Cloud API
- Botpress: http://bot.sigi.localhost
- Ollama: http://ia.sigi.localhost
- Qdrant: http://qdrant.sigi.localhost
- Portainer: http://portainer.sigi.localhost
- pgAdmin: http://pgadmin.sigi.localhost
- Dashboard Traefik: http://localhost:18080

## 6. Subir apenas uma parte da aplicacao

Nem sempre e necessario subir tudo. Use os comandos individuais:

Subir apenas Symfony:

```bash
make up-admin
```

Tambem funciona:

```bash
make up-symfony
```

Esse comando sobe `symfony-admin`, `postgres`, `redis` e `traefik`.

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

Esse comando sobe `chatwoot`, `chatwoot-worker`, `postgres`, `redis` e `traefik`.

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

Subir apenas pgAdmin:

```bash
make up-pgadmin
```

Esse comando sobe `pgadmin`, `postgres` e `traefik`.

Parar servicos especificos:

```bash
make stop-admin
make stop-ia
make stop-chat
make stop-bot
```

Ver logs especificos:

```bash
make logs-admin
make logs-ia
make logs-chat
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

### Recebimento de e-mails no Chatwoot

O Chatwoot precisa de dois processos:

- `chatwoot`: interface web.
- `chatwoot-worker`: Sidekiq, jobs em background, IMAP, auto-respostas e filas.

Se o e-mail estiver configurado por IMAP e as mensagens nao chegarem na caixa de entrada, confira primeiro se o worker esta rodando:

```bash
docker compose ps chatwoot chatwoot-worker
docker compose logs -f chatwoot-worker
```

O log do worker deve listar o agendador:

```text
trigger_imap_email_inboxes_job
```

Para abrir o console Rails:

```bash
docker exec -it sigi-chatwoot bundle exec rails c
```

Para testar manualmente a busca IMAP da inbox `1` pelo WSL:

```bash
docker exec -it sigi-chatwoot bundle exec rails runner "Inboxes::FetchImapEmailsJob.new.perform(Inbox.find(1).channel, 24)"
```

Configuracao Titan usada para caixas de e-mail:

```text
Incoming server: imap.titan.email
Port: 993
Encryption method: SSL/TLS
Outgoing server: smtp.titan.email
Port: 465
Encryption method: SSL/TLS
Username: endereco completo do e-mail
```

## 8. Primeiro acesso ao pgAdmin

Abra:

```text
http://pgadmin.sigi.localhost
```

Credenciais padrao de desenvolvimento:

- E-mail: `admin@sigi.dev.br`
- Senha: `sigi_pgadmin_dev`

Para cadastrar o PostgreSQL no pgAdmin, crie um servidor com:

- Host: `postgres`
- Porta: `5432`
- Banco principal: `sigi_sd`
- Usuario: valor de `POSTGRES_USER`
- Senha: valor de `POSTGRES_PASSWORD`

## 9. Verificar saude dos endpoints

Execute:

```bash
make health
```

Resultados esperados:

- `admin.sigi.localhost`: HTTP 200.
- `chat.sigi.localhost`: HTTP 302 para onboarding ou dashboard.
- `bot.sigi.localhost`: HTTP 302 para `/admin`.
- `pgadmin.sigi.localhost`: HTTP 200 ou HTTP 302 para tela de login.
- `ia.sigi.localhost/api/version`: JSON com versao do Ollama.
- `qdrant.sigi.localhost/collections`: JSON com `status: ok`.

## 10. Ver logs

Todos os logs:

```bash
make logs
```

Logs de um servico especifico:

```bash
docker compose logs -f symfony-admin
docker compose logs -f chatwoot chatwoot-worker
docker compose logs -f traefik
```

## 11. Rodar comandos Symfony

Abrir shell no container:

```bash
make shell-admin
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

## 12. Parar ou reiniciar

Parar containers:

```bash
make down
```

Reiniciar tudo:

```bash
make restart
```

## 13. Problemas comuns

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

E-mails do Chatwoot nao chegam na conversa:

```bash
docker compose ps chatwoot chatwoot-worker
docker compose logs -f chatwoot-worker
```

Se `chatwoot-worker` nao estiver `Up`, rode:

```bash
make up-chat
```

Symfony mostra erro de CSS/Sass:

```bash
docker compose exec symfony-admin php bin/console sass:build
docker compose restart symfony-admin
```

## 14. Atualizar depois de mudancas no projeto

Quando arquivos Docker ou dependencias mudarem:

```bash
make rebuild
make ps
make health
```

## 15. Encerramento seguro

Para encerrar o ambiente local:

```bash
make down
```

Os dados persistem nos volumes Docker. Para apagar volumes, use comandos Docker manualmente apenas quando tiver certeza de que quer perder os dados locais.
