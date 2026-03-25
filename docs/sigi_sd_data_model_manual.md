# SIGI-SD — Manual de Modelagem de Dados

## Visão geral

Este documento descreve a modelagem base do SIGI-SD para:

- identidade de pessoas físicas e jurídicas
- vínculos institucionais e múltiplos papéis
- estrutura geográfica do Brasil
- endereços físicos
- contatos e presença digital
- histórico de comunicação/CRM
- cobertura territorial
- áreas temáticas de atuação

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

**Exemplo**

```sql
INSERT INTO persons (full_name, cpf, created_at, updated_at)
VALUES ('Wellington Carvalho', '314.269.938-46', NOW(), NULL);
```

### 1.2 `organizations`

**Descrição**  
Armazena pessoas jurídicas, instituições, empresas, prefeituras, câmaras, universidades e organizações em geral.

| Campo | Tipo | Descrição |
|---|---|---|
| id | int | Identificador interno |
| legal_name | varchar(191) | Razão social / nome jurídico |
| trade_name | varchar(191) nullable | Nome fantasia |
| cnpj | varchar(18) | CNPJ único |
| created_at | datetime immutable | Data de criação |
| updated_at | datetime immutable nullable | Data da última atualização |

**Exemplo**

```sql
INSERT INTO organizations (legal_name, trade_name, cnpj, created_at, updated_at)
VALUES ('CENTRO DE INOVACAO E TECNOLOGIA SERTAO DIGITAL', 'Sertão Digital', '61.367.666/0001-77', NOW(), NULL);
```

### 1.3 `roles`

**Descrição**  
Catálogo de papéis/funções exercidas por pessoas dentro de organizações.

| Campo | Tipo | Descrição |
|---|---|---|
| id | int | Identificador interno |
| name | varchar(100) | Nome do papel (único) |
| description | varchar nullable | Descrição do papel |

**Exemplos**

- President
- Director
- Teacher
- Student
- Volunteer
- PublicServant
- CouncilMember
- Mayor
- Consultant

```sql
INSERT INTO roles (name, description) VALUES
('President', 'Main executive leader'),
('CouncilMember', 'Legislative member'),
('Teacher', 'Educational role');
```

### 1.4 `person_organizations`

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

**Exemplo**

```sql
INSERT INTO person_organizations (person_id, organization_id, start_date, status, notes)
VALUES (1, 1, NOW(), 'active', 'Institutional leadership bond');
```

### 1.5 `person_organization_roles`

**Descrição**  
Permite múltiplos papéis para a mesma pessoa na mesma organização.

| Campo | Tipo | Descrição |
|---|---|---|
| id | int | Identificador interno |
| person_organization_id | fk person_organizations | Vínculo principal |
| role_id | fk roles | Papel desempenhado |
| start_date | datetime immutable nullable | Início do papel |
| end_date | datetime immutable nullable | Fim do papel |

**Exemplo**

```sql
INSERT INTO person_organization_roles (person_organization_id, role_id, start_date)
VALUES (1, 1, NOW());
```

---

## 2. Geographic Structure

Hierarquia territorial adotada:

```text
City -> Microregion -> Mesoregion -> State -> Region -> Country
```

### 2.1 `countries`

**Descrição**  
Catálogo de países.

| Campo | Tipo | Descrição |
|---|---|---|
| id | int | Identificador interno |
| name | varchar(191) | Nome do país |
| iso2 | varchar(2) | Código ISO2 |
| iso3 | varchar(3) | Código ISO3 |
| numeric_code | varchar(3) nullable | Código numérico ISO |
| phone_code | varchar(10) nullable | DDI |
| currency | varchar(10) nullable | Moeda |
| created_at | datetime immutable | Data de criação |
| updated_at | datetime immutable nullable | Data da última atualização |

**Exemplo**

```sql
INSERT INTO countries (name, iso2, iso3, numeric_code, phone_code, currency, created_at, updated_at)
VALUES ('Brazil', 'BR', 'BRA', '076', '55', 'BRL', NOW(), NULL);
```

### 2.2 `regions`

**Descrição**  
Grandes regiões geográficas do país.

| Campo | Tipo | Descrição |
|---|---|---|
| id | int | Identificador interno |
| name | varchar(191) | Nome da região |
| country_id | fk countries | País ao qual a região pertence |

**Exemplo**

```sql
INSERT INTO regions (name, country_id)
VALUES ('Northeast', 1);
```

### 2.3 `states`

**Descrição**  
Estados da federação.

| Campo | Tipo | Descrição |
|---|---|---|
| id | int | Identificador interno |
| uf | varchar(2) | Sigla única do estado |
| name | varchar(191) | Nome do estado |
| area_km2 | decimal nullable | Área territorial |
| gdp | decimal nullable | PIB |
| population | int nullable | População |
| size | varchar(50) nullable | Porte |
| annual_revenue | decimal nullable | Receita anual |
| capital_city_id | fk cities nullable | Cidade capital |
| country_id | fk countries | País |
| region_id | fk regions | Região |

**Exemplo**

```sql
INSERT INTO states (uf, name, area_km2, gdp, population, size, annual_revenue, country_id, region_id)
VALUES ('PB', 'Paraíba', 56467.2, 0, 0, 'medium', 0, 1, 1);
```

### 2.4 `mesoregions`

**Descrição**  
Mesorregiões vinculadas ao estado.

| Campo | Tipo | Descrição |
|---|---|---|
| id | int | Identificador interno |
| name | varchar(191) | Nome da mesorregião |
| ibge_code | varchar(20) | Código IBGE único |
| municipalities_count | int nullable | Número de municípios |
| state_id | fk states | Estado |

**Exemplo**

```sql
INSERT INTO mesoregions (name, ibge_code, municipalities_count, state_id)
VALUES ('Sertão Paraibano', '2504', 83, 1);
```

### 2.5 `microregions`

**Descrição**  
Microrregiões vinculadas à mesorregião.

| Campo | Tipo | Descrição |
|---|---|---|
| id | int | Identificador interno |
| name | varchar(191) | Nome da microrregião |
| ibge_code | varchar(20) | Código IBGE único |
| mesoregion_id | fk mesoregions | Mesorregião |

**Exemplo**

```sql
INSERT INTO microregions (name, ibge_code, mesoregion_id)
VALUES ('Sousa', '25005', 1);
```

### 2.6 `cities`

**Descrição**  
Municípios/cidades.

| Campo | Tipo | Descrição |
|---|---|---|
| id | int | Identificador interno |
| microregion_id | fk microregions nullable | Microrregião |
| state_id | fk states | Estado |
| area_km2 | decimal nullable | Área |
| gdp | decimal nullable | PIB |
| population | int nullable | População |
| annual_revenue | decimal nullable | Receita anual |
| tom_code | varchar(20) nullable | Código TOM |
| ibge_code | varchar(20) nullable | Código IBGE |
| ibge_code7 | varchar(20) nullable | Código IBGE7 |
| zip_code | varchar(10) nullable | CEP |
| tom_name | varchar(191) nullable | Nome no TOM |
| ibge_name | varchar(191) | Nome no IBGE |
| size | varchar(50) nullable | Porte |
| is_capital | boolean | Indica capital |

**Exemplo**

```sql
INSERT INTO cities (microregion_id, state_id, ibge_name, ibge_code, ibge_code7, zip_code, is_capital)
VALUES (1, 1, 'Marizópolis', '2509152', '2509152', '58819000', 0);
```

---

## 3. Address Management

### 3.1 `address_types`

**Descrição**  
Catálogo de tipos de endereço.

Exemplos:
- Residential
- Commercial
- Work
- Fiscal
- Operational
- Correspondence

| Campo | Tipo | Descrição |
|---|---|---|
| id | int | Identificador interno |
| name | varchar(100) | Tipo de endereço |
| description | varchar nullable | Descrição |

### 3.2 `addresses`

**Descrição**  
Endereços físicos vinculáveis a pessoas e organizações.

| Campo | Tipo | Descrição |
|---|---|---|
| id | int | Identificador interno |
| street | varchar(191) | Logradouro |
| number | varchar nullable | Número |
| complement | varchar nullable | Complemento |
| neighborhood | varchar nullable | Bairro |
| zip_code | varchar(10) nullable | CEP |
| reference | varchar nullable | Referência |
| latitude | decimal nullable | Latitude |
| longitude | decimal nullable | Longitude |
| city_id | fk cities | Cidade |
| created_at | datetime immutable | Data de criação |
| updated_at | datetime immutable nullable | Data da última atualização |

### 3.3 `person_addresses`

**Descrição**  
Vínculo entre pessoa e endereço.

| Campo | Tipo | Descrição |
|---|---|---|
| id | int | Identificador interno |
| person_id | fk persons | Pessoa |
| address_id | fk addresses | Endereço |
| address_type_id | fk address_types | Tipo de endereço |
| is_primary | boolean | Endereço principal |

### 3.4 `organization_addresses`

**Descrição**  
Vínculo entre organização e endereço.

| Campo | Tipo | Descrição |
|---|---|---|
| id | int | Identificador interno |
| organization_id | fk organizations | Organização |
| address_id | fk addresses | Endereço |
| address_type_id | fk address_types | Tipo de endereço |
| is_primary | boolean | Endereço principal |

---

## 4. Contacts and Digital Presence

### 4.1 `contact_types`

**Descrição**  
Catálogo extensível de canais de contato e presença digital.

Exemplos:
- Email
- Phone
- Mobile
- WhatsApp
- Telegram
- Website
- Instagram
- Facebook
- LinkedIn
- YouTube
- X
- TikTok
- Other

| Campo | Tipo | Descrição |
|---|---|---|
| id | int | Identificador interno |
| name | varchar(100) | Nome do tipo |
| description | varchar nullable | Descrição |
| category | varchar(50) nullable | communication / social / web |

### 4.2 `contact_statuses`

**Descrição**  
Estado atual do contato.

Exemplos:
- Active
- Inactive
- Invalid
- Bounced
- WrongPerson
- NoResponse
- Blocked

| Campo | Tipo | Descrição |
|---|---|---|
| id | int | Identificador interno |
| name | varchar(100) | Status |
| description | varchar nullable | Descrição |

### 4.3 `contact_issue_types`

**Descrição**  
Motivo do problema/invalidação do contato.

Exemplos:
- EmailBounced
- WrongPhoneNumber
- WrongPerson
- NumberNotFound
- BlockedByRecipient
- DuplicateContact
- InvalidFormat

| Campo | Tipo | Descrição |
|---|---|---|
| id | int | Identificador interno |
| name | varchar(100) | Tipo de problema |
| description | varchar nullable | Descrição |

### 4.4 `person_contacts`

**Descrição**  
Contatos de pessoa física.

| Campo | Tipo | Descrição |
|---|---|---|
| id | int | Identificador interno |
| person_id | fk persons | Pessoa |
| contact_type_id | fk contact_types | Tipo de contato |
| status_id | fk contact_statuses nullable | Status do contato |
| issue_type_id | fk contact_issue_types nullable | Motivo do problema |
| value | varchar(191) | Valor do contato |
| label | varchar(100) nullable | Rótulo (pessoal, comercial, etc.) |
| is_primary | boolean | Contato principal |
| is_public | boolean | Indica se é público |
| deactivated_at | datetime immutable nullable | Data de desativação |
| deactivation_reason | text nullable | Motivo da desativação |
| notes | text nullable | Observações |

### 4.5 `organization_contacts`

**Descrição**  
Contatos de pessoa jurídica.

| Campo | Tipo | Descrição |
|---|---|---|
| id | int | Identificador interno |
| organization_id | fk organizations | Organização |
| contact_type_id | fk contact_types | Tipo de contato |
| status_id | fk contact_statuses nullable | Status do contato |
| issue_type_id | fk contact_issue_types nullable | Motivo do problema |
| value | varchar(191) | Valor do contato |
| label | varchar(100) nullable | Rótulo |
| is_primary | boolean | Contato principal |
| is_public | boolean | Indica se é público |
| deactivated_at | datetime immutable nullable | Data de desativação |
| deactivation_reason | text nullable | Motivo da desativação |
| notes | text nullable | Observações |

---

## 5. Communication History / CRM

### 5.1 `interaction_statuses`

**Descrição**  
Resultado de uma interação específica.

Exemplos:
- Sent
- Delivered
- Read
- Answered
- NoResponse
- Failed
- Returned
- ScheduledFollowUp
- Closed

| Campo | Tipo | Descrição |
|---|---|---|
| id | int | Identificador interno |
| name | varchar(100) | Status da interação |
| description | varchar nullable | Descrição |

### 5.2 `person_contact_interactions`

**Descrição**  
Histórico de interações realizadas sobre contatos de pessoas físicas.

| Campo | Tipo | Descrição |
|---|---|---|
| id | int | Identificador interno |
| person_contact_id | fk person_contacts | Contato usado |
| interaction_status_id | fk interaction_statuses nullable | Resultado |
| performed_by | fk users nullable | Usuário que realizou o contato |
| contacted_at | datetime immutable | Data/hora do contato |
| subject | varchar(191) nullable | Assunto |
| message | text nullable | Conteúdo/resumo |
| response_received | boolean | Houve resposta? |
| response_text | text nullable | Resposta recebida |
| next_contact_at | datetime immutable nullable | Próximo contato |
| notes | text nullable | Observações |

### 5.3 `organization_contact_interactions`

**Descrição**  
Histórico de interações realizadas sobre contatos de organizações.

| Campo | Tipo | Descrição |
|---|---|---|
| id | int | Identificador interno |
| organization_contact_id | fk organization_contacts | Contato usado |
| interaction_status_id | fk interaction_statuses nullable | Resultado |
| performed_by | fk users nullable | Usuário que realizou o contato |
| contacted_at | datetime immutable | Data/hora do contato |
| subject | varchar(191) nullable | Assunto |
| message | text nullable | Conteúdo/resumo |
| response_received | boolean | Houve resposta? |
| response_text | text nullable | Resposta recebida |
| next_contact_at | datetime immutable nullable | Próximo contato |
| notes | text nullable | Observações |

---

## 6. Territorial Coverage

### 6.1 `coverage_types`

**Descrição**  
Tipo de cobertura/atuação territorial.

Exemplos:
- Institutional
- Commercial
- Educational
- Technical
- Political
- Social
- Operational

| Campo | Tipo | Descrição |
|---|---|---|
| id | int | Identificador interno |
| name | varchar(100) | Tipo |
| description | varchar nullable | Descrição |

### 6.2 `person_coverages`

**Descrição**  
Território de atuação da pessoa física.

| Campo | Tipo | Descrição |
|---|---|---|
| id | int | Identificador interno |
| person_id | fk persons | Pessoa |
| coverage_type_id | fk coverage_types | Tipo de cobertura |
| region_id | fk regions nullable | Região |
| state_id | fk states nullable | Estado |
| mesoregion_id | fk mesoregions nullable | Mesorregião |
| microregion_id | fk microregions nullable | Microrregião |
| city_id | fk cities nullable | Cidade |
| notes | text nullable | Observações |
| is_primary | boolean | Cobertura principal |

### 6.3 `organization_coverages`

**Descrição**  
Território de atuação da organização.

| Campo | Tipo | Descrição |
|---|---|---|
| id | int | Identificador interno |
| organization_id | fk organizations | Organização |
| coverage_type_id | fk coverage_types | Tipo de cobertura |
| region_id | fk regions nullable | Região |
| state_id | fk states nullable | Estado |
| mesoregion_id | fk mesoregions nullable | Mesorregião |
| microregion_id | fk microregions nullable | Microrregião |
| city_id | fk cities nullable | Cidade |
| notes | text nullable | Observações |
| is_primary | boolean | Cobertura principal |

---

## 7. Thematic Areas of Activity

### 7.1 `thematic_areas`

**Descrição**  
Catálogo hierárquico de áreas temáticas de atuação.

Exemplos:
- Government Digital
- Education
- Health
- Infrastructure
- Technology
- Data and AI
- Public Management
- Transparency
- Bidding
- Software Development
- Digital Inclusion

| Campo | Tipo | Descrição |
|---|---|---|
| id | int | Identificador interno |
| name | varchar(150) | Nome da área temática |
| description | varchar nullable | Descrição |
| parent_id | fk thematic_areas nullable | Área temática pai |

### 7.2 `person_thematic_areas`

**Descrição**  
Áreas temáticas em que a pessoa atua.

| Campo | Tipo | Descrição |
|---|---|---|
| id | int | Identificador interno |
| person_id | fk persons | Pessoa |
| thematic_area_id | fk thematic_areas | Área temática |
| notes | text nullable | Observações |
| is_primary | boolean | Área principal |

### 7.3 `organization_thematic_areas`

**Descrição**  
Áreas temáticas em que a organização atua.

| Campo | Tipo | Descrição |
|---|---|---|
| id | int | Identificador interno |
| organization_id | fk organizations | Organização |
| thematic_area_id | fk thematic_areas | Área temática |
| notes | text nullable | Observações |
| is_primary | boolean | Área principal |

---

## 8. Exemplo integrado

### Exemplo conceitual

- Person: Wellington Carvalho
- Organization: Centro de Inovação e Tecnologia Sertão Digital
- Role: President
- City: Marizópolis
- Microregion: Sousa
- Mesoregion: Sertão Paraibano
- State: Paraíba
- Region: Northeast
- Country: Brazil

### Exemplo de uso

- Wellington está vinculado ao Sertão Digital
- Wellington exerce o papel de President
- O Sertão Digital possui endereço comercial em Marizópolis
- O Sertão Digital possui contatos institucionais, financeiros e de parcerias
- O histórico registra e-mails enviados, respostas recebidas e próximo follow-up
- O Sertão Digital atua territorialmente na Paraíba, Sertão Paraibano e Nordeste
- O Sertão Digital atua tematicamente em Government Digital, Education e Digital Inclusion

---

## 9. Ordem recomendada de carga de dados

Para evitar problemas com chaves estrangeiras, a carga deve seguir esta ordem:

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
14. thematic_areas
15. persons
16. organizations
17. person_organizations
18. person_organization_roles
19. addresses
20. person_addresses
21. organization_addresses
22. person_contacts
23. organization_contacts
24. person_contact_interactions
25. organization_contact_interactions
26. person_coverages
27. organization_coverages
28. person_thematic_areas
29. organization_thematic_areas

---

## 10. Observações finais

- Endereço físico e área de atuação são conceitos diferentes e devem permanecer separados.
- Contatos e presença digital não devem ficar presos a um único campo em `persons` ou `organizations`.
- O histórico de comunicação é parte do CRM e precisa registrar o usuário responsável pelo contato.
- Papéis devem ser vinculados ao relacionamento entre pessoa e organização, permitindo múltiplos papéis simultâneos.
- Áreas temáticas e cobertura territorial devem ser separadas para permitir filtros inteligentes.

---

## 11. Próximos passos sugeridos

- criar fixtures iniciais para catálogos
- criar importadores CSV para base territorial
- criar dashboards de cobertura territorial e temática
- criar CRUD administrativo apenas para módulos operacionais
- documentar regras de negócio adicionais (validações e consistência territorial)

