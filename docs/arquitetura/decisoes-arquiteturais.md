# Decisoes arquiteturais

## ADR-001: Symfony como backend principal

O Symfony concentra regras de negocio, APIs, entidades, auditoria e integracoes. Ferramentas externas como Chatwoot, Botpress, Ollama e Qdrant sao integradas como servicos.

## ADR-002: SIGI-SD nao e analytics

O SIGI-SD registra e organiza interacoes. Analytics, BI, dashboards e indicadores estrategicos pertencem a Plataforma 360.

## ADR-003: SIGI-SD nao e GRP

Sistemas como e-Cidade, i-Educar e Amadeus LMS continuam sendo sistemas transacionais. O SIGI-SD acessa esses sistemas por conectores, contratos e adaptadores.

## ADR-004: Sem modulo de workflow

O SIGI-SD pode registrar status, encaminhamentos e tramitacao simples, mas nao deve se transformar em motor de workflow.

## ADR-005: Chatwoot web separado do worker

O Chatwoot deve rodar com processo web e processo worker separados no Docker Compose.

- `chatwoot`: Rails/Puma para interface e API web.
- `chatwoot-worker`: Sidekiq para jobs, cron, IMAP, SMTP assincrono, auto-respostas e filas.

Essa separacao e obrigatoria para recebimento automatico de e-mails via IMAP. Sem o worker, a interface continua acessivel, mas mensagens recebidas no provedor de e-mail nao sao processadas como conversas.
