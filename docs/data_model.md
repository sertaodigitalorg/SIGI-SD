# SIGI-SD - Manual de Modelagem de Dados

## Visão geral

Este documento descreve a modelagem base do SIGI-SD para:

- identidade de pessoas físicas e jurídicas
- vínculos institucionais e múltiplos papéis
- estrutura geográfica do Brasil
- endereços físicos
- contatos e presença digital
- histórico de comunicação e CRM
- cobertura territorial
- áreas temáticas de atuação
- hierarquia e classificação institucional de organizações

A nomenclatura das entidades e campos foi padronizada em inglês para manter consistência técnica no projeto.

---

## 1. Identity Core

### 1.1 `persons`

**Descrição**
Armazena pessoas físicas.

| Campo | Tipo | Descrição |
|---|---|---|
| id | int | Identificador interno |
| full_name | varchar(191) | Nome completo |
| cpf | varchar(14) | CPF único |
| created_at | datetime immutable | Data de criação |
| updated_at | datetime immutable nullable | Data da última atualização |

### 1.2 `organizations`

**Descrição**
Armazena pessoas jurídicas, instituições, empresas, prefeituras, câmaras, universidades e organizações em geral.

| Campo | Tipo | Descrição |
|---|---|---|
| id | int | Identificador interno |
| legal_name | varchar(191) | Razão social / nome jurídico |
| trade_name | varchar(191) nullable | Nome fantasia |
| cnpj | varchar(18) | CNPJ único |
| acronym | varchar(50) nullable | Sigla |
| status | varchar(50) nullable | Status institucional |
| notes | text nullable | Observações gerais |
| parent_id | fk organizations nullable | Organização pai na hierarquia |
| organization_type_id | fk organization_types nullable | Tipo de organização |
| created_at | datetime immutable | Data de criação |
| updated_at | datetime immutable nullable | Data da última atualização |

**Regras de negócio**
- `parent_id` é opcional e define a hierarquia institucional.
- uma organização não pode apontar para si mesma como pai.
- a cadeia hierárquica não pode formar ciclos.
- a remoção da organização pai deve apenas limpar o vínculo, não excluir as organizações filhas.

**Exemplo conceitual**

```text
Rede do Sertão
└── Sertão Digital
```

No exemplo acima:
- `Rede do Sertão` é uma organização raiz.
- `Sertão Digital` é filha de `Rede do Sertão`.
- a hierarquia é modelada por `parent_id`.

### 1.3 `organization_types`

**Descrição**
Catálogo de classificação institucional das organizações.

| Campo | Tipo | Descrição |
|---|---|---|
| id | int | Identificador interno |
| name | varchar(100) | Nome único do tipo |
| description | varchar(255) nullable | Descrição do tipo |

**Finalidade**
- classificar a natureza institucional da organização
- evitar texto livre repetido em `organizations`
- permitir expansão de tipos sem alterar a semântica da tabela principal

**Exemplos**
- Instituição de Ciência e Tecnologia
- Associação
- Órgão Público
- Empresa
- Instituição de Ensino

### 1.4 `roles`

**Descrição**
Catálogo de papéis e funções exercidas por pessoas dentro de organizações.

| Campo | Tipo | Descrição |
|---|---|---|
| id | int | Identificador interno |
| name | varchar(100) | Nome do papel (único) |
| description | varchar nullable | Descrição do papel |

### 1.5 `person_organizations`

**Descrição**
Representa o vínculo principal entre uma pessoa e uma organização.

| Campo | Tipo | Descrição |
|---|---|---|
| id | int | Identificador interno |
| person_id | fk persons | Pessoa vinculada |
| organization_id | fk organizations | Organização vinculada |
| start_date | datetime immutable nullable | Início do vínculo |
| end_date | datetime immutable nullable | Fim do vínculo |
| status | varchar(50) nullable | Status do vínculo |
| notes | text nullable | Observações |

### 1.6 `person_organization_roles`

**Descrição**
Permite múltiplos papéis para a mesma pessoa na mesma organização.

| Campo | Tipo | Descrição |
|---|---|---|
| id | int | Identificador interno |
| person_organization_id | fk person_organizations | Vínculo principal |
| role_id | fk roles | Papel desempenhado |
| start_date | datetime immutable nullable | Início do papel |
| end_date | datetime immutable nullable | Fim do papel |

---

## 2. Estrutura Institucional de Organizações

### Hierarquia com `parent`
- use `parent` para representar subordinação, mantença, rede ou vínculo estrutural entre organizações
- organizações raiz ficam com `parent = null`
- organizações filhas podem ser recuperadas pelo lado inverso da relação, como `children`

### Classificação com `organizationType`
- use `organizationType` para representar a categoria institucional da organização
- `organizationType` não substitui a hierarquia
- duas organizações do mesmo tipo podem ocupar posições diferentes na hierarquia

### Exemplo prático
- `Rede do Sertão` pode ter tipo `Associação`
- `Sertão Digital` pode ter tipo `Instituição de Ciência e Tecnologia`
- `Sertão Digital.parent = Rede do Sertão`

---

## 3. Geographic Structure

Hierarquia territorial adotada:

```text
City -> Microregion -> Mesoregion -> State -> Region -> Country
```

### 3.1 `countries`

Catálogo de países.

### 3.2 `regions`

Grandes regiões geográficas do país.

### 3.3 `states`

Estados da federação.

### 3.4 `mesoregions`

Mesorregiões vinculadas ao estado.

### 3.5 `microregions`

Microrregiões vinculadas à mesorregião.

### 3.6 `cities`

Municípios e cidades.

---

## 4. Address Management

### 4.1 `address_types`
Catálogo de tipos de endereço.

### 4.2 `addresses`
Endereços físicos vinculáveis a pessoas e organizações.

### 4.3 `person_addresses`
Vínculo entre pessoa e endereço.

### 4.4 `organization_addresses`
Vínculo entre organização e endereço.

---

## 5. Contacts and Digital Presence

### 5.1 `contact_types`
Catálogo extensível de canais de contato e presença digital.

### 5.2 `contact_statuses`
Estado atual do contato.

### 5.3 `contact_issue_types`
Motivo do problema ou invalidação do contato.

### 5.4 `person_contacts`
Contatos de pessoa física.

### 5.5 `organization_contacts`
Contatos de pessoa jurídica.

---

## 6. Communication History / CRM

### 6.1 `interaction_statuses`
Resultado de uma interação específica.

### 6.2 `person_contact_interactions`
Histórico de interações realizadas sobre contatos de pessoas físicas.

### 6.3 `organization_contact_interactions`
Histórico de interações realizadas sobre contatos de organizações.

---

## 7. Territorial Coverage

### 7.1 `coverage_types`
Tipo de cobertura ou atuação territorial.

### 7.2 `person_coverages`
Território de atuação da pessoa física.

### 7.3 `organization_coverages`
Território de atuação da organização.

---

## 8. Thematic Areas of Activity

### 8.1 `thematic_areas`
Catálogo hierárquico de áreas temáticas de atuação.

### 8.2 `person_thematic_areas`
Áreas temáticas em que a pessoa atua.

### 8.3 `organization_thematic_areas`
Áreas temáticas em que a organização atua.

---

## 9. Ordem recomendada de carga de dados

1. countries
2. regions
3. states
4. mesoregions
5. microregions
6. cities
7. roles
8. address_types
9. contact_types
10. contact_statuses
11. contact_issue_types
12. interaction_statuses
13. coverage_types
14. organization_types
15. thematic_areas
16. persons
17. organizations
18. person_organizations
19. person_organization_roles
20. addresses
21. person_addresses
22. organization_addresses
23. person_contacts
24. organization_contacts
25. person_contact_interactions
26. organization_contact_interactions
27. person_coverages
28. organization_coverages
29. person_thematic_areas
30. organization_thematic_areas

---

## 10. Observações finais

- `parent` e `organizationType` têm responsabilidades diferentes e complementares.
- hierarquia institucional e classificação institucional não devem ser misturadas.
- endereço físico e área de atuação são conceitos diferentes e devem permanecer separados.
- contatos e presença digital não devem ficar presos a um único campo em `persons` ou `organizations`.
- o histórico de comunicação é parte do CRM e precisa registrar o usuário responsável pelo contato.
- papéis devem ser vinculados ao relacionamento entre pessoa e organização, permitindo múltiplos papéis simultâneos.
- áreas temáticas e cobertura territorial devem ser separadas para permitir filtros inteligentes.