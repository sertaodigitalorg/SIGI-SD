# SIGI-SD — Arquitetura do Sistema

## Visão Geral

O SIGI-SD é uma plataforma de gestão e inteligência voltada à transformação digital institucional, com foco em:

- prefeituras
- governos estaduais
- câmaras municipais
- instituições públicas e privadas
- organizações do terceiro setor

O sistema foi projetado com base em:

- arquitetura modular
- separação de responsabilidades
- escalabilidade
- interoperabilidade

---

## 🧱 Camadas da Arquitetura

O sistema segue uma arquitetura baseada em camadas:

### 1. Presentation Layer
- Controllers (Symfony)
- Templates Twig
- Interface administrativa (/admin)

### 2. Application Layer
- Services
- Orquestração de regras de negócio
- Processamento de dados

### 3. Domain Layer
- Entities (Doctrine ORM)
- Modelagem de negócio

### 4. Infrastructure Layer
- Banco de dados (MySQL)
- Repositories (Doctrine)
- Integrações externas (futuro)

---

## 🏛️ Módulo de Organizações

### Entidade: Organization

Representa qualquer estrutura institucional:

- Prefeitura
- Governo Estadual
- Câmara Municipal
- Secretaria
- Empresa
- ONG
- Universidade

### Hierarquia

O sistema suporta estrutura hierárquica:

```text
Organização (pai)
└── Sub-organizações (filhas)
```

Exemplo:

```text
Prefeitura de Sousa
├── Secretaria de Saúde
├── Secretaria de Educação
└── Secretaria de Infraestrutura
```

### Classificação

Entidade auxiliar:

 - `OrganizationType`

Define o tipo da organização:

 - Prefeitura
 - Secretaria
 - Câmara
 - Empresa
 - ONG
 - etc.

## 👤 Módulo de Pessoas

#### Entidade: Person

Representa indivíduos que interagem com o sistema:

 - gestores públicos
 - secretários
 - assessores
 - representantes institucionais

### 🔗 Vínculo Pessoa ↔ Organização

#### Entidade: PersonOrganization

Define a relação entre uma pessoa e uma organização.

Permite:

 - múltiplos vínculos
 - histórico institucional

#### Entidade: PersonOrganizationRole

Define o papel da pessoa:

 - Prefeito
 - Secretário
 - Diretor
 - Coordenador
 - Assessor

## 📞 Módulo de Contatos

O sistema diferencia dois tipos de contato:

### 1. Contato Institucional (PJ)

#### Entidade: OrganizationContact

Representa canais oficiais da organização:

 - e-mail institucional
 - telefone geral
 - WhatsApp institucional
 - site
 - redes sociais

### 2. Contato Pessoal (PF)

#### Entidade: PersonContact

Representa canais diretos da pessoa:

 - e-mail pessoal ou institucional
 - telefone direto
 - WhatsApp pessoal
 - LinkedIn

## 📊 Módulo de Interações (CRM)

O sistema implementa um modelo de CRM institucional com dois fluxos:

### 1. Interação Institucional (PJ)

#### Entidade: OrganizationContactInteraction

Registra:

 - contato utilizado (OrganizationContact)
 - data/hora do contato
 - mensagem enviada
 - resposta recebida
 - próximo contato (follow-up)

### 2. Interação com Pessoa (PF)

#### Entidade: PersonContactInteraction

Registra:

 - contato direto da pessoa (PersonContact)
 - histórico de comunicação
 - resposta
 - continuidade do relacionamento

## 🔄 Fluxo de Relacionamento

O sistema segue um fluxo natural de relacionamento:

```text
Contato institucional (genérico)
        ↓
Resposta da organização
        ↓
Identificação da pessoa
        ↓
Contato direto com pessoa
        ↓
Relacionamento contínuo
```

## 📈 Módulo de Dashboard Operacional

### Objetivo

Fornecer visão operacional do CRM.

### Métricas principais
 - Interações recentes (PF e PJ)
 - Follow-ups vencidos
 - Taxa de resposta
 - Total de interações

### Fontes de dados
 - OrganizationContactInteraction
 - PersonContactInteraction

## 🧠 Princípios Arquiteturais

 - separação entre dados institucionais e pessoais
 - desacoplamento entre entidades
 - escalabilidade para grandes volumes de dados
 - suporte a múltiplos níveis organizacionais
 - rastreabilidade completa de interações

## 🚀 Escalabilidade

O sistema foi projetado para suportar:
 - milhares de organizações
 - múltiplos contatos por organização
 - grande volume de interações
 - uso simultâneo por múltiplos usuários

## 🔐 Segurança (visão geral)

 - controle de acesso por usuário
 - dados sensíveis protegidos
 - separação entre área pública e administrativa
 - auditoria futura de interações

## 📌 Considerações Finais

A arquitetura do SIGI-SD foi projetada para evoluir de:

 - cadastro institucional

para:

 - plataforma de inteligência
 - CRM público estruturado
 - sistema de apoio à decisão

## 🔮 Evoluções Futuras

 - API pública
 - integração com sistemas governamentais
 - BI e dashboards avançados
 - automação de comunicação
 - notificações e alertas inteligentes