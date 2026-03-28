# SIGI-SD — Roadmap do Produto

## Visão Geral

O SIGI-SD é uma plataforma de gestão e inteligência institucional com foco em:

- transformação digital de governos
- gestão de relacionamento institucional (CRM)
- organização de dados territoriais
- apoio à tomada de decisão

Este roadmap define a evolução do sistema em fases.

---

## 🎯 Objetivo Estratégico

Construir uma plataforma nacional de:

- CRM público estruturado
- inteligência territorial
- governança digital
- integração entre instituições públicas e privadas

---

## 🧱 Fase 1 — Fundação do Sistema (CONCLUÍDO / EM ANDAMENTO)

### 📦 Base institucional

- [x] Cadastro de organizações
- [x] Hierarquia de organizações (parent)
- [x] Tipos de organização

---

### 👤 Cadastro de pessoas

- [x] CRUD de Person
- [x] Base para vínculo com organizações

---

### 📞 Contatos

#### Institucional (PJ)

- [x] OrganizationContact
- [x] Organização → múltiplos contatos

#### Pessoal (PF)

- [x] PersonContact
- [x] Pessoa → múltiplos contatos

---

### 📊 CRM — Interações

#### Institucional

- [x] OrganizationContactInteraction

#### Pessoa

- [x] PersonContactInteraction

---

### 🔄 Fluxo implementado

```text
Contato institucional
        ↓
Resposta da organização
        ↓
Identificação da pessoa
        ↓
Contato direto
        ↓
Relacionamento contínuo
```

### 📈 Dashboard operacional
 - [x] Interações recentes (PF e PJ)
 - [x] Follow-ups vencidos
 - [x] Taxa de resposta

## 🚀 Fase 2 — Operação e Produtividade (PRÓXIMO PASSO)

### 🎯 Gestão de follow-up
 - [] Lista de próximos contatos do dia
 - [] Lista de contatos vencidos
 - [] Alertas visuais (prioridade)
 - [] Indicadores de atraso

### 🔔 Notificações
 - [] Alertas de follow-up
 - [] Lembretes automáticos
 - [] Notificação por e-mail (futuro)
 - [] Notificação interna

### 🔍 Filtros e buscas
 - [] Filtro por organização
 - [] Filtro por pessoa
 - [] Filtro por status
 - [] Filtro por período

### 📊 Dashboard avançado
 - [] Interações por período
 - [] Taxa de resposta por usuário
 - [] Ranking de contatos
 - [] Evolução de relacionamento

## 🧠 Fase 3 — Inteligência e Automação

### 🤖 Automação de comunicação
 - [] Sugestão de follow-up automático
 - [] Templates de mensagens
 - [] Disparo automatizado (e-mail / WhatsApp)

### 🧠 Inteligência de relacionamento
 - [] Identificação de contatos estratégicos
 - [] Classificação de organizações
 - [] Score de relacionamento

### 📌 Pipeline institucional
 - [] Status do relacionamento:
   - Lead
   - Contato iniciado
   - Em negociação
   - Convertido
   - Perdido

## 🌐 Fase 4 — Integração e Ecossistema

### 🔗 Integrações
 - [] API pública (/api/v1)
 - [] Integração com e-Cidade
 - [] Integração com i-Educar
 - [] Integração com sistemas municipais

### 📡 Comunicação externa
 - [] Integração com WhatsApp API
 - [] Integração com e-mail
 - [] Chatbots (Botpress)

### 📊 BI e dados
 - [] Painéis analíticos (Power BI)
 - [] Exportação de dados
 - [] Data warehouse

## 🏛️ Fase 5 — Plataforma Nacional

### 🧩 Multi-tenant
 - [] Suporte a múltiplos municípios
 - [] Isolamento de dados por cliente

### 🔐 Governança
 - [] Controle de acesso avançado
 - [] Auditoria de ações
 - [] Logs completos

### 📜 Compliance
 - [] LGPD
 - [] Políticas de privacidade
 - [] Gestão de consentimento

## 🎯 Visão de Longo Prazo

Transformar o SIGI-SD em:

 - plataforma nacional de CRM público
 - base de inteligência institucional
 - motor de transformação digital municipal
 - hub de integração entre governo e sociedade

## 📌 Prioridade Atual

Foco imediato:

1. Melhorar fluxo de interação (UX)
2. Follow-up operacional
3. Alertas e notificações
4. Dashboard mais inteligente

## 🚀 Direção Estratégica

O SIGI-SD não é apenas um sistema de cadastro.

É uma plataforma de:

 - relacionamento institucional
 - inteligência de dados
 - apoio à decisão pública