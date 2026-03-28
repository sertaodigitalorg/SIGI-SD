# SIGI-SD — Documentação de Fixtures

## Visão Geral

As fixtures do SIGI-SD são utilizadas para:

- popular dados estruturais iniciais
- padronizar ambientes de desenvolvimento
- facilitar onboarding
- garantir consistência em testes e demonstrações

O sistema utiliza fixtures apenas para **dados estruturais e institucionais iniciais**, e não para dados operacionais vivos.

---

## Estrutura Atual de Fixtures

As fixtures principais estão organizadas em cinco blocos:

1. `GeographyFixtures`
2. `CatalogFixtures`
3. `UserFixtures`
4. `OrganizationFixtures`
5. `InitialContactFixtures`
6. `AppFixtures` (execução final, se necessário)

---

## 1. `GeographyFixtures`

Responsável pela base territorial inicial.

### Entidades carregadas
- Country
- Region
- State
- Mesoregion
- Microregion
- City

### Base inicial atual
- País: Brasil
- Região: Nordeste
- Estados: todos os estados do Brasil
- Mesorregião: Sertão Paraibano
- Microrregião: Sousa
- Cidades:
  - Sousa
  - Marizópolis

### Objetivo
Criar a estrutura territorial mínima para o funcionamento do sistema.

---

## 2. `CatalogFixtures`

Responsável por catálogos e dados estruturais.

### Entidades carregadas

#### OrganizationType
Exemplos:
- Governo Estadual
- Prefeitura
- Câmara Municipal
- Secretaria
- Empresa
- ONG
- Associação

#### Role
Exemplos:
- Presidente
- Prefeito
- Vereador
- Diretor
- Coordenador
- Consultor

#### AddressType
Exemplos:
- Residencial
- Comercial
- Fiscal
- Operacional
- Trabalho
- Correspondência

#### ContactType
Exemplos:
- E-mail
- Telefone
- Celular
- WhatsApp
- Telegram
- Site
- Instagram
- LinkedIn

#### ContactStatus
Exemplos:
- Ativo
- Inativo
- Inválido
- Retornado
- Sem Resposta
- Bloqueado

#### ContactIssueType
Exemplos:
- E-mail Retornado
- Telefone Incorreto
- Pessoa Errada
- Número Não Encontrado
- Contato Duplicado

#### InteractionStatus
Exemplos:
- Enviado
- Entregue
- Lido
- Respondido
- Sem Resposta
- Encerrado

#### CoverageType
Exemplos:
- Institucional
- Comercial
- Educacional
- Técnica
- Política
- Social
- Operacional

#### ThematicArea
Exemplos:
- Tecnologia
- Setor Público
- Educação
- Infraestrutura
- Inovação
- Governo Digital
- Dados e IA
- Licitações
- Inclusão Digital

### Referências importantes criadas
Exemplos:
- `role_presidente`
- `contact_type_email`
- `contact_type_website`
- `contact_type_whatsapp`
- `contact_type_instagram`
- `contact_status_active`
- `organization_type_associacao`
- `organization_type_prefeitura`
- `organization_type_secretaria`

---

## 3. `UserFixtures`

Responsável pelos usuários iniciais do sistema.

### Objetivo
Criar contas básicas para desenvolvimento e operação inicial.

### Exemplos
- Wellington Carvalho (admin)
- Administrador SIGI-SD
- Usuário Operacional

### Regras
- senhas sempre com hash
- nunca armazenar senha em texto plano
- referências seguem padrão:

```text
user_{username}
```

Exemplo:

```text
user_wellington
```

## 4. OrganizationFixtures

Responsável pela base institucional inicial.

### Entidades carregadas
 - Organization
 - Person
 - PersonOrganization
 - PersonOrganizationRole

### Base inicial sugerida
- Organização:
  - Centro de Inovação e Tecnologia Sertão Digital
- Tipo:
  - Associação
- Pessoa:
  - Wellington Carvalho Silva
- Papel:
  - Presidente

### Referências importantes
 - organization_sertao_digital
 - person_wellington_carvalho_silva
 - person_organization_wellington_sertao_digital

## 5. InitialContactFixtures

Responsável pelos contatos institucionais iniciais.


### Entidades carregadas
 - OrganizationContact

### Base inicial sugerida

Contatos do Sertão Digital:

**E-mails**
 - contato@sertaodigital.org
 - atendimento@sertaodigital.org
 - financeiro@sertaodigital.org
 - parcerias@sertaodigital.org
 - projetos@sertaodigital.org
 - diretoria@sertaodigital.org
 - nao-responda@sertaodigital.org
 - imprensa@sertaodigital.org

**Outros canais**
 - site institucional
 - WhatsApp institucional
 - Instagram oficial

### Objetivo

Permitir uso inicial do CRM institucional já com contatos reais da organização.

## 6. AppFixtures

Responsável por execução final e composição complementar.

### Objetivo
 - orquestrar cenários finais
 - criar dados compostos
 - apoiar demonstrações e testes avançados

### Observação

Deve rodar depois das demais fixtures, usando `DependentFixtureInterface`.


## Ordem de Execução

A ordem recomendada é:

1. GeographyFixtures
2. CatalogFixtures
3. UserFixtures
4. OrganizationFixtures
5. InitialContactFixtures
6. AppFixtures

## Uso de Referências

### Criar referência

```php
$this->addReference('organization_sertao_digital', $organization);
```

### Utilizar referência

```php
$organization = $this->getReference('organization_sertao_digital', Organization::class);
```

### Padrão recomendado
| Tipo                 | Padrão               |
|----------------------|----------------------|
| Usuário              | user_nome            |
| Organização          | organization_nome    |
| Pessoa               | person_nome          |
| Estado               | state_uf             |
| Cidade               | city_nome            |
| Papel                | role_nome            |
| Tipo de organização  | organization_type_nome |

## O que deve ir em Fixture

### Sim
 - geografia
 - catálogos
 - usuários iniciais
 - organização base
 - contatos institucionais base

### Não
 - histórico de interações
 - follow-ups reais
 - contatos operacionais de terceiros
 - dados vivos de CRM
 - dados importados massivos sem estratégia - 

## Boas Práticas
 - manter fixtures pequenas e organizadas
 - usar referências para dependências
 - não duplicar dados
 - não usar fixtures para substituir importadores massivos
 - manter valores exibidos em português
 - manter código em inglês

## Comando de Carga
```bash
php bin/console doctrine:fixtures:load --no-interaction
```

## Considerações Finais

As fixtures do SIGI-SD são parte da infraestrutura do projeto e devem ser tratadas como:

 - base de inicialização
 - base de padronização
 - apoio a desenvolvimento e demonstração

Elas não substituem os fluxos operacionais do sistema.