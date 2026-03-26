# SIGI-SD - Guia de Desenvolvimento

## Convenções Gerais
- Código, classes, métodos e atributos permanecem em inglês.
- Textos exibidos ao usuário, mensagens de interface e documentação do produto devem estar em português do Brasil.
- Alterações de banco devem sempre ser acompanhadas de migration.

## Entidades do Módulo Institucional

### `Organization`
Campos principais:
- `legalName`
- `tradeName`
- `cnpj`
- `acronym`
- `status`
- `notes`
- `parent`
- `organizationType`

Regras importantes:
- `parent` pode ser nulo.
- `parent` nunca pode apontar para a própria organização.
- A cadeia de `parent` não pode formar ciclos.
- `__toString()` deve retornar um nome amigável para formulários e templates.

### `OrganizationType`
Campos:
- `name` único
- `description`

Uso:
- centralizar tipos de organização reutilizáveis
- evitar texto livre repetido em `Organization`

## CRUD de Organizações
- Controller: `src/Controller/OrganizationController.php`
- Formulário: `src/Form/OrganizationTypeForm.php`
- Templates: `templates/organization/*`

Boas práticas adotadas:
- feedback ao usuário com flash messages em português
- placeholders para relacionamentos opcionais
- tratamento explícito de valores nulos nos templates
- exclusão protegida por CSRF

## Banco de Dados
Ao alterar a estrutura institucional:

```bash
php bin/console make:migration
php bin/console doctrine:migrations:migrate
```

A migration da evolução institucional deve criar:
- tabela `organization_types`
- novos campos em `organizations`
- índices e chaves estrangeiras para `parent` e `organizationType`

## Fixtures
A carga institucional depende de `CatalogFixtures` porque os tipos de organização são referências reutilizadas por `OrganizationFixtures`.

Padrão recomendado:
- cadastros de catálogo em fixtures de catálogo
- dados de cenário em fixtures específicas do módulo
- referências nomeadas com prefixos consistentes

## Validação
As validações do módulo devem existir no nível mais próximo do domínio possível.

Exemplos deste módulo:
- unicidade de CNPJ
- unicidade do nome do tipo de organização
- bloqueio de auto-relacionamento em `parent`
- bloqueio de hierarquia circular