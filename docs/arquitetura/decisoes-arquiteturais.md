# Decisoes arquiteturais

## ADR-001: Symfony como backend principal

O Symfony concentra regras de negocio, APIs, entidades, auditoria e integracoes. Ferramentas externas como Chatwoot, Botpress, Ollama e Qdrant sao integradas como servicos.

## ADR-002: SIGI-SD nao e analytics

O SIGI-SD registra e organiza interacoes. Analytics, BI, dashboards e indicadores estrategicos pertencem a Plataforma 360.

## ADR-003: SIGI-SD nao e GRP

Sistemas como e-Cidade, i-Educar e Amadeus LMS continuam sendo sistemas transacionais. O SIGI-SD acessa esses sistemas por conectores, contratos e adaptadores.

## ADR-004: Workflow transacional de atendimento

O SIGI-SD deve controlar o ciclo de vida transacional das solicitacoes de atendimento com Symfony Workflow, mantendo o Chatwoot como interface operacional das conversas.

Esta decisao substitui a orientacao anterior de nao possuir modulo de workflow. O limite arquitetural permanece: o SIGI-SD nao deve se tornar um orquestrador generico de todos os processos administrativos. O workflow fica restrito a protocolo, estado oficial da solicitacao, controle de automacao, handoff humano, auditoria e integracoes de atendimento.

Regras:

- Chatwoot continua sendo a interface do atendente e dos canais.
- SIGI-SD e a fonte oficial de protocolo, estado, auditoria e decisoes de fluxo.
- Processamento assicrono deve usar Symfony Messenger com Redis.
- Concorrencia deve ser controlada com Symfony Lock e Redis.
- IA local via Ollama e base vetorial local via Qdrant devem ser acessadas por nomes de servico Docker, sem APIs externas de IA.
- URLs internas devem usar nomes como `symfony-admin`, `chatwoot`, `postgres`, `redis`, `ollama` e `qdrant`; URLs locais de navegador devem usar hosts Traefik como `admin.sigi.localhost` e `chat.sigi.localhost`.
- Validacoes com banco e servicos locais devem ser executadas pelo WSL/Docker, nao pelo PHP local do Windows.

## ADR-005: Chatwoot web separado do worker

O Chatwoot deve rodar com processo web e processo worker separados no Docker Compose.

- `chatwoot`: Rails/Puma para interface e API web.
- `chatwoot-worker`: Sidekiq para jobs, cron, IMAP, SMTP assincrono, auto-respostas e filas.

Essa separacao e obrigatoria para recebimento automatico de e-mails via IMAP. Sem o worker, a interface continua acessivel, mas mensagens recebidas no provedor de e-mail nao sao processadas como conversas.
