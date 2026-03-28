# SIGI-SD — Manual de Modelagem de Dados

## Visão Geral

Este documento descreve a modelagem de dados do SIGI-SD, incluindo:

- identidade de pessoas físicas e jurídicas
- hierarquia organizacional
- vínculos institucionais
- estrutura territorial
- endereços
- contatos
- histórico de comunicação
- cobertura territorial
- áreas temáticas

A nomenclatura técnica do sistema foi padronizada em inglês para código, entidades, tabelas e campos.  
Os valores exibidos ao usuário devem ser mantidos em português do Brasil.

---

## 1. Núcleo de Identidade

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

---

### 1.2 `organization_types`

**Descrição**  
Catálogo de tipos de organização.

| Campo | Tipo | Descrição |
|---|---|---|
| id | int | Identificador interno |
| name | varchar(100) | Nome do tipo |
| description | varchar nullable | Descrição |

**Exemplos**
- Prefeitura
- Governo Estadual
- Câmara Municipal
- Secretaria
- Empresa
- ONG
- Associação
- Universidade

---

### 1.3 `organizations`

**Descrição**  
Armazena pessoas jurídicas e estruturas institucionais.

| Campo | Tipo | Descrição |
|---|---|---|
| id | int | Identificador interno |
| legal_name | varchar(191) | Nome jurídico / razão social |
| trade_name | varchar(191) nullable | Nome fantasia |
| acronym | varchar(50) nullable | Sigla |
| cnpj | varchar(18) nullable | CNPJ |
| status | varchar(50) nullable | Status organizacional |
| notes | text nullable | Observações |
| parent_id | fk organizations nullable | Organização pai |
| organization_type_id | fk organization_types nullable | Tipo de organização |
| created_at | datetime immutable | Data de criação |
| updated_at | datetime immutable nullable | Data da última atualização |

**Observação**  
A hierarquia institucional é representada por `parent_id`.

**Exemplo de hierarquia**
- Prefeitura Municipal de Sousa
  - Secretaria Municipal de Saúde de Sousa
  - Secretaria Municipal de Educação de Sousa
- Governo do Estado da Paraíba
  - Secretaria de Estado da Educação
  - Secretaria de Estado da Saúde

---

### 1.4 `roles`

**Descrição**  
Catálogo de papéis exercidos por pessoas em organizações.

| Campo | Tipo | Descrição |
|---|---|---|
| id | int | Identificador interno |
| name | varchar(100) | Nome do papel |
| description | varchar nullable | Descrição |

**Exemplos**
- Presidente
- Prefeito
- Vereador
- Secretário
- Diretor
- Coordenador
- Consultor

---

### 1.5 `person_organizations`

**Descrição**  
Vínculo principal entre pessoa e organização.

| Campo | Tipo | Descrição |
|---|---|---|
| id | int | Identificador interno |
| person_id | fk persons | Pessoa |
| organization_id | fk organizations | Organização |
| start_date | datetime immutable nullable | Início do vínculo |
| end_date | datetime immutable nullable | Fim do vínculo |
| status | varchar(50) nullable | Status do vínculo |
| notes | text nullable | Observações |

---

### 1.6 `person_organization_roles`

**Descrição**  
Papéis que a pessoa exerce dentro do vínculo com a organização.

| Campo | Tipo | Descrição |
|---|---|---|
| id | int | Identificador interno |
| person_organization_id | fk person_organizations | Vínculo principal |
| role_id | fk roles | Papel |
| start_date | datetime immutable nullable | Início do papel |
| end_date | datetime immutable nullable | Fim do papel |

**Observação**  
Uma pessoa pode ter mais de um papel na mesma organização.

---

## 2. Estrutura Territorial

Hierarquia adotada:

```text
City -> Microregion -> Mesoregion -> State -> Region -> Country
```

### 2.1 countries
| Campo         | Tipo                          | Descrição                     |
|---            |---                            |---                            |
| id            | int                           | Identificador interno         |
| name          | varchar(191)                  | Nome do país                  |
| iso2          | varchar(2)                    | Código ISO2                   |
| iso3          | varchar(3)                    | Código ISO3                   |
| numeric_code  | varchar(3) nullable           | Código numérico ISO           |
| phone_code    | varchar(10) nullable          | DDI                           |
| currency      | varchar(10) nullable  	    | Moeda                         |
| created_at    | datetime immutable    	    | Data de criação               |
| updated_at    | datetime immutable nullable   | Data da última atualização    |


### 2.2 regions
| Campo      | Tipo         | Descrição              |
|------------|--------------|------------------------|
| id         | int          | Identificador interno  |
| name       | varchar(191) | Nome da região         |
| country_id | fk countries | País                   |

### 2.3 states
| Campo           | Tipo                | Descrição               |
|----------------|---------------------|------------------------|
| id             | int                 | Identificador interno  |
| uf             | varchar(2)          | Sigla do estado        |
| name           | varchar(191)        | Nome do estado         |
| area_km2       | decimal nullable    | Área                   |
| gdp            | decimal nullable    | PIB                    |
| population     | int nullable        | População              |
| size           | varchar(50) nullable| Porte                  |
| annual_revenue | decimal nullable    | Receita anual          |
| capital_city_id| fk cities nullable  | Cidade capital         |
| country_id     | fk countries        | País                   |
| region_id      | fk regions          | Região                 |

### 2.4 mesoregions
| Campo                | Tipo             | Descrição                  |
|---------------------|------------------|----------------------------|
| id                  | int              | Identificador interno      |
| name                | varchar(191)     | Nome da mesorregião        |
| ibge_code           | varchar(20)      | Código IBGE                |
| municipalities_count| int nullable     | Número de municípios       |
| state_id            | fk states        | Estado                     |

### 2.5 microregions
| Campo         | Tipo             | Descrição               |
|---------------|------------------|-------------------------|
| id            | int              | Identificador interno   |
| name          | varchar(191)     | Nome da microrregião    |
| ibge_code     | varchar(20)      | Código IBGE             |
| mesoregion_id | fk mesoregions   | Mesorregião             |

### 2.6 cities
| Campo            | Tipo                  | Descrição                  |
|------------------|-----------------------|-----------------------------|
| id               | int                   | Identificador interno       |
| microregion_id   | fk microregions nullable | Microrregião             |
| state_id         | fk states             | Estado                      |
| area_km2         | decimal nullable      | Área                        |
| gdp              | decimal nullable      | PIB                         |
| population       | int nullable          | População                   |
| annual_revenue   | decimal nullable      | Receita anual               |
| tom_code         | varchar(20) nullable  | Código TOM                  |
| ibge_code        | varchar(20) nullable  | Código IBGE                 |
| ibge_code7       | varchar(20) nullable  | Código IBGE7                |
| zip_code         | varchar(10) nullable  | CEP                         |
| tom_name         | varchar(191) nullable | Nome no TOM                 |
| ibge_name        | varchar(191)          | Nome no IBGE                |
| size             | varchar(50) nullable  | Porte                       |
| is_capital       | boolean               | Indica se é capital         |

## 3. Endereços

### 3.1 address_types
| Campo       | Tipo             | Descrição               |
|-------------|------------------|-------------------------|
| id          | int              | Identificador interno   |
| name        | varchar(100)     | Tipo de endereço        |
| description | varchar nullable | Descrição               |

**Exemplos**
 - Residencial
 - Comercial
 - Fiscal
 - Operacional
 - Trabalho
 - Correspondência

### 3.2 addresses
| Campo        | Tipo                     | Descrição                     |
|--------------|--------------------------|-------------------------------|
| id           | int                      | Identificador interno         |
| street       | varchar(191)             | Logradouro                   |
| number       | varchar nullable         | Número                        |
| complement   | varchar nullable         | Complemento                   |
| neighborhood | varchar nullable         | Bairro                        |
| zip_code     | varchar(10) nullable     | CEP                           |
| reference    | varchar nullable         | Referência                    |
| latitude     | decimal nullable         | Latitude                      |
| longitude    | decimal nullable         | Longitude                     |
| city_id      | fk cities                | Cidade                        |
| created_at   | datetime immutable       | Data de criação               |
| updated_at   | datetime immutable nullable | Data da última atualização |

### 3.3 person_addresses
| Campo           | Tipo              | Descrição               |
|-----------------|-------------------|-------------------------|
| id              | int               | Identificador interno   |
| person_id       | fk persons        | Pessoa                  |
| address_id      | fk addresses      | Endereço                |
| address_type_id | fk address_types  | Tipo de endereço        |
| is_primary      | boolean           | Endereço principal      |

### 3.4 organization_addresses
| Campo           | Tipo               | Descrição               |
|-----------------|--------------------|-------------------------|
| id              | int                | Identificador interno   |
| organization_id | fk organizations   | Organização             |
| address_id      | fk addresses       | Endereço                |
| address_type_id | fk address_types   | Tipo de endereço        |
| is_primary      | boolean            | Endereço principal      |

## 4. Contatos

O sistema diferencia contatos institucionais (PJ) e contatos pessoais (PF).

### 4.1 contact_types
| Campo       | Tipo             | Descrição                              |
|-------------|------------------|----------------------------------------|
| id          | int              | Identificador interno                  |
| name        | varchar(100)     | Tipo de contato                        |
| description | varchar nullable | Descrição                              |
| category    | varchar(50) nullable | communication / social / web / other |

**Exemplos**
 - E-mail
 - Telefone
 - Celular
 - WhatsApp
 - Telegram
 - Site
 - Instagram
 - LinkedIn

### 4.2 contact_statuses
| Campo       | Tipo             | Descrição              |
|-------------|------------------|------------------------|
| id          | int              | Identificador interno  |
| name        | varchar(100)     | Status                 |
| description | varchar nullable | Descrição              |

**Exemplos**
 - Ativo
 - Inativo
 - Inválido
 - Retornado
 - Sem Resposta
 - Bloqueado

### 4.3 contact_issue_types
| Campo       | Tipo             | Descrição              |
|-------------|------------------|------------------------|
| id          | int              | Identificador interno  |
| name        | varchar(100)     | Motivo do problema     |
| description | varchar nullable | Descrição              |

**Exemplos**
 - E-mail Retornado
 - Telefone Incorreto
 - Pessoa Errada
 - Número Não Encontrado
 - Contato Duplicado

### 4.4 organization_contacts

**Descrição**
Contato genérico/institucional da organização.

| Campo                | Tipo                         | Descrição                     |
|----------------------|------------------------------|-------------------------------|
| id                   | int                          | Identificador interno         |
| organization_id      | fk organizations             | Organização                   |
| contact_type_id      | fk contact_types             | Tipo de contato               |
| status_id            | fk contact_statuses nullable | Status                        |
| issue_type_id        | fk contact_issue_types nullable | Problema                   |
| value                | varchar(191)                 | Valor do contato              |
| label                | varchar(100) nullable        | Rótulo                        |
| is_primary           | boolean                      | Principal                     |
| is_public            | boolean                      | Público                       |
| deactivated_at       | datetime immutable nullable  | Data de desativação           |
| deactivation_reason  | text nullable                | Motivo da desativação         |
| notes                | text nullable                | Observações                   |

**Exemplos**
 - e-mail geral da prefeitura
 - e-mail do gabinete
 - WhatsApp institucional
 - site oficial

### 4.5 person_contacts

**Descrição**
Contato direto da pessoa.

| Campo                | Tipo                         | Descrição                     |
|----------------------|------------------------------|-------------------------------|
| id                   | int                          | Identificador interno         |
| person_id            | fk persons                   | Pessoa                        |
| contact_type_id      | fk contact_types             | Tipo de contato               |
| status_id            | fk contact_statuses nullable | Status                        |
| issue_type_id        | fk contact_issue_types nullable | Problema                   |
| value                | varchar(191)                 | Valor do contato              |
| label                | varchar(100) nullable        | Rótulo                        |
| is_primary           | boolean                      | Principal                     |
| is_public            | boolean                      | Público                       |
| deactivated_at       | datetime immutable nullable  | Data de desativação           |
| deactivation_reason  | text nullable                | Motivo da desativação         |
| notes                | text nullable                | Observações                   |

## 5. Histórico de Interações (CRM)

### 5.1 interaction_statuses
| Campo       | Tipo             | Descrição                    |
|-------------|------------------|------------------------------|
| id          | int              | Identificador interno        |
| name        | varchar(100)     | Status da interação          |
| description | varchar nullable | Descrição                    |

**Exemplos**
 - Enviado
 - Entregue
 - Lido
 - Respondido
 - Sem Resposta
 - Encerrado

### 5.2 organization_contact_interactions

**Descrição**
Histórico de comunicação com contato institucional genérico.

| Campo                   | Tipo                          | Descrição                     |
|--------------------------|-------------------------------|-------------------------------|
| id                       | int                           | Identificador interno         |
| organization_contact_id  | fk organization_contacts      | Contato institucional         |
| interaction_status_id    | fk interaction_statuses nullable | Status da interação        |
| performed_by             | fk users nullable             | Usuário que realizou          |
| contacted_at             | datetime immutable            | Data/hora do contato          |
| subject                  | varchar(191) nullable         | Assunto                       |
| message                  | text nullable                 | Mensagem/resumo               |
| response_received        | boolean                       | Houve resposta                |
| response_text            | text nullable                 | Texto da resposta             |
| next_contact_at          | datetime immutable nullable   | Próximo contato               |
| notes                    | text nullable                 | Observações                   |

**Fluxo típico**

 - contato inicial com prefeitura, secretaria ou gabinete
 - refinamento posterior para uma pessoa

### 5.3 person_contact_interactions

**Descrição**
Histórico de comunicação com contato direto da pessoa.

| Campo                  | Tipo                          | Descrição                     |
|-------------------------|-------------------------------|-------------------------------|
| id                      | int                           | Identificador interno         |
| person_contact_id       | fk person_contacts            | Contato da pessoa             |
| interaction_status_id   | fk interaction_statuses nullable | Status da interação        |
| performed_by            | fk users nullable             | Usuário que realizou          |
| contacted_at            | datetime immutable            | Data/hora do contato          |
| subject                 | varchar(191) nullable         | Assunto                       |
| message                 | text nullable                 | Mensagem/resumo               |
| response_received       | boolean                       | Houve resposta                |
| response_text           | text nullable                 | Texto da resposta             |
| next_contact_at         | datetime immutable nullable   | Próximo contato               |
| notes                   | text nullable                 | Observações                   |

## 6. Cobertura Territorial

### 6.1 coverage_types
| Campo       | Tipo             | Descrição              |
|-------------|------------------|------------------------|
| id          | int              | Identificador interno  |
| name        | varchar(100)     | Tipo de cobertura      |
| description | varchar nullable | Descrição              |

**Exemplos**

Institucional
Comercial
Educacional
Técnica
Política
Social
Operacional

### 6.2 person_coverages
| Campo            | Tipo                       | Descrição                 |
|------------------|----------------------------|---------------------------|
| id               | int                        | Identificador interno     |
| person_id        | fk persons                 | Pessoa                    |
| coverage_type_id | fk coverage_types          | Tipo                      |
| region_id        | fk regions nullable        | Região                    |
| state_id         | fk states nullable         | Estado                    |
| mesoregion_id    | fk mesoregions nullable    | Mesorregião               |
| microregion_id   | fk microregions nullable   | Microrregião              |
| city_id          | fk cities nullable         | Cidade                    |
| notes            | text nullable              | Observações               |
| is_primary       | boolean                    | Cobertura principal       |

### 6.3 organization_coverages
| Campo            | Tipo                       | Descrição                 |
|------------------|----------------------------|---------------------------|
| id               | int                        | Identificador interno     |
| organization_id  | fk organizations           | Organização               |
| coverage_type_id | fk coverage_types          | Tipo                      |
| region_id        | fk regions nullable        | Região                    |
| state_id         | fk states nullable         | Estado                    |
| mesoregion_id    | fk mesoregions nullable    | Mesorregião               |
| microregion_id   | fk microregions nullable   | Microrregião              |
| city_id          | fk cities nullable         | Cidade                    |
| notes            | text nullable              | Observações               |
| is_primary       | boolean                    | Cobertura principal       |

## 7. Áreas Temáticas

### 7.1 thematic_areas
| Campo       | Tipo                         | Descrição            |
|-------------|------------------------------|----------------------|
| id          | int                          | Identificador interno|
| name        | varchar(150)                 | Área temática        |
| description | varchar nullable             | Descrição            |
| parent_id   | fk thematic_areas nullable   | Área pai             |

### 7.2 person_thematic_areas
| Campo            | Tipo                | Descrição            |
|------------------|---------------------|----------------------|
| id               | int                 | Identificador interno|
| person_id        | fk persons          | Pessoa               |
| thematic_area_id | fk thematic_areas   | Área temática        |
| notes            | text nullable       | Observações          |
| is_primary       | boolean             | Área principal       |

### 7.3 organization_thematic_areas
| Campo            | Tipo                | Descrição            |
|------------------|---------------------|----------------------|
| id               | int                 | Identificador interno|
| organization_id  | fk organizations    | Organização          |
| thematic_area_id | fk thematic_areas   | Área temática        |
| notes            | text nullable       | Observações          |
| is_primary       | boolean             | Área principal       |

## 8. Fluxo Conceitual do CRM

O SIGI-SD adota dois níveis de relacionamento:

### Etapa 1 — contato institucional (PJ)
 - fala-se com a organização de forma genérica
 - exemplo: e-mail do gabinete, prefeitura, secretaria

### Etapa 2 — contato pessoal (PF)
 - identifica-se a pessoa correta
 - continua-se o relacionamento com contato direto

Fluxo:

```text
Organization
  -> OrganizationContact
    -> OrganizationContactInteraction
        ↓
      Person
        -> PersonContact
          -> PersonContactInteraction
```

## 9. Dashboard Operacional

As métricas do dashboard são derivadas de:

 - `organization_contact_interactions`
 - `person_contact_interactions`

Indicadores iniciais:

 - interações recentes
 - follow-ups vencidos
 - taxa de resposta PF
 - taxa de resposta PJ
 - taxa de resposta geral

## 10. Ordem Recomendada de Carga Inicial
1. countries
2. regions
3. states
4. mesoregions
5. microregions
6. cities
7. organization_types
8. roles
9. address_types
10. contact_types
11. contact_statuses
12. contact_issue_types
13. interaction_statuses
14. coverage_types
15. thematic_areas
16. users
17. organizations
18. persons
19. person_organizations
20. person_organization_roles
21. organization_contacts

## 11. Considerações Finais

A modelagem do SIGI-SD foi projetada para:

 - suportar crescimento institucional
 - organizar relacionamento com organizações e pessoas
 - permitir CRM escalável
 - manter separação entre contato genérico e contato pessoal
 - sustentar dashboards e inteligência operacional