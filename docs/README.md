# Documentacao do SIGI-SD

O SIGI-SD, Sistema de Inteligencia Geral de Interacoes, e a plataforma tecnologica da Central Publica Digital do Sertao Digital.

Use esta area para registrar arquitetura, infraestrutura, integracoes, operacao, LGPD, IA local, chatbot, agents e skills.

## Documentos principais

- `operacao/manual-do-usuario.md`
- `arquitetura/visao-geral.md`
- `arquitetura/camadas.md`
- `arquitetura/modulos.md`
- `infraestrutura/visao-geral.md`
- `docker/docker-compose.md`
- `wsl/ambiente-wsl.md`
- `ia/ia-embarcada.md`
- `chatbot/botpress.md`
- `integracoes/padrao-adapters.md`
- `lgpd/lgpd-by-design.md`
- `operacao/operacao-atendimento.md`

## Indice completo

Inicio e operacao:

- [Manual do usuario](operacao/manual-do-usuario.md)
- [Operacao de atendimento](operacao/operacao-atendimento.md)

Arquitetura:

- [Arquitetura](arquitetura/README.md)
- [Visao geral](arquitetura/visao-geral.md)
- [Camadas](arquitetura/camadas.md)
- [Modulos](arquitetura/modulos.md)
- [Decisoes arquiteturais](arquitetura/decisoes-arquiteturais.md)

Infraestrutura:

- [Visao geral](infraestrutura/visao-geral.md)
- [Docker Compose](docker/docker-compose.md)
- [Ambiente WSL2](wsl/ambiente-wsl.md)

IA e chatbot:

- [IA embarcada](ia/ia-embarcada.md)
- [RAG local](ia/rag-local.md)
- [Qdrant](ia/qdrant.md)
- [Ollama](ia/ollama.md)
- [Botpress](chatbot/botpress.md)
- [Fluxos conversacionais](chatbot/fluxos-conversacionais.md)

Integracoes:

- [Padrao de adapters](integracoes/padrao-adapters.md)
- [e-Cidade](integracoes/e-cidade.md)
- [i-Educar](integracoes/i-educar.md)
- [Amadeus LMS](integracoes/amadeus-lms.md)

Governanca tecnica:

- [LGPD by design](lgpd/lgpd-by-design.md)
- [Agents](agents/README.md)
- [Skills](skills/README.md)

Documentos legados do backend atual:

- [API](api.md)
- [Arquitetura antiga](architecture.md)
- [Modelo de dados](data_model.md)
- [Guia de desenvolvimento](dev-guide.md)
- [Fixtures](fixtures.md)
- [Roadmap](roadmap.md)
- [Seguranca](security.md)

## Limites

- Analytics, BI, dashboards e indicadores pertencem a Plataforma 360.
- Sistemas transacionais de gestao publica pertencem aos GRPs, como e-Cidade e i-Educar.
- O SIGI-SD nao possui modulo de workflow.
