# Visao geral da arquitetura

O SIGI-SD e a plataforma tecnologica que apoia a Central Publica Digital. Sua responsabilidade e registrar, organizar e automatizar interacoes entre cidadaos, atendentes, agentes digitais, orgaos publicos, cooperativas, empresas e sistemas integrados.

## Separacao conceitual

Central Publica Digital:

- Conceito e servico publico.
- Operacao institucional.
- Ponto de contato com cidadaos e organizacoes.

SIGI-SD:

- Plataforma tecnologica de interacoes.
- Atendimento multicanal e CRM operacional.
- Chatbot, IA conversacional e integracoes.
- Protocolos, ouvidoria, agendamentos e notificacoes.

Cooperativa de Atendimento:

- Operacao humana.
- Atendentes, supervisores, qualidade e SLA.

Plataforma 360:

- BI, analytics, dashboards e indicadores.
- IA analitica e observabilidade estrategica.

GRPs:

- e-Cidade, i-Educar, Amadeus LMS e sistemas legados.
- Fontes transacionais acessadas por conectores e adaptadores.

## Diagrama

```mermaid
flowchart TD
    CPD[Central Publica Digital] --> SIGI[SIGI-SD]
    SIGI --> CHAT[Chatwoot]
    SIGI --> BOT[Botpress]
    SIGI --> API[Symfony API]
    SIGI --> EVO[Evolution API]
    SIGI --> OLLAMA[Ollama]
    SIGI --> QDRANT[Qdrant]
    API --> PG[PostgreSQL]
    API --> REDIS[Redis]
    API --> GRP[Integracoes GRP]
    GRP --> ECIDADE[e-Cidade]
    GRP --> IEDUCAR[i-Educar]
    GRP --> AMADEUS[Amadeus LMS]
    SIGI --> P360[Plataforma 360 para analytics]
```
