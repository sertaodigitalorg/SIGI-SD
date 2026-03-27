# SIGI-SD — Guia de Desenvolvimento

## Visão Geral

Este documento define os padrões técnicos e boas práticas de desenvolvimento do SIGI-SD.

Objetivos:

- garantir consistência no código
- facilitar manutenção e evolução
- permitir escalabilidade
- acelerar onboarding de novos desenvolvedores

---

## 🧱 Princípios de Desenvolvimento

- Clean Code
- Single Responsibility Principle (SRP)
- Separação de camadas
- Reutilização de código
- Padronização
- Clareza semântica

---

## 📐 Padrão de Nomenclatura

| Tipo | Padrão |
|------|--------|
| Código (classes, métodos, variáveis) | Inglês |
| Labels e conteúdo exibido | Português (Brasil) |

---

## 🧩 Entidades (Entities)

### Regras

- nomes em inglês
- usar singular (Person, Organization, Contact)
- atributos em camelCase
- evitar abreviações
- manter clareza semântica

### Exemplo

```php
class Person
{
    private string $fullName;
    private string $cpf;
}
```

## 🗄️ Migrations

Sempre que alterar entidades:

```bash
php bin/console make:migration
php bin/console doctrine:migrations:migrate
```

## 📦 Fixtures

### Criação

```bash
php bin/console make:fixture NomeFixtures
```

### Estrutura padrão

```php
public function load(ObjectManager $manager): void
{
    $entity = new Entity();
    $manager->persist($entity);

    $this->addReference('reference_name', $entity);

    $manager->flush();
}
```

## 🔗 Uso de Referências

```php
$this->addReference('user_admin', $user);
$user = $this->getReference('user_admin', User::class);
```

## 🧠 Arquitetura de Código

### Controllers

Responsabilidade:

 - receber requisições
 - chamar serviços
 - retornar resposta

❌ Não deve conter:

 - regra de negócio
 - lógica complexa

### Services

Responsabilidade:

 - regras de negócio
 - processamento de dados
 - cálculos e métricas

### Repositories

Responsabilidade:

 - consultas ao banco
 - queries customizadas
 - otimizações de leitura

## 🏗️ Estrutura do Projeto

```text
src/
  Entity/
  Repository/
  Controller/
    Admin/
  Service/
  DataFixtures/

templates/
  admin/
```

## 🔐 Área Administrativa

Todos os CRUDs devem estar em:

```text
/admin/*
```

### Exemplo:

```text
/admin/organization
/admin/person
/admin/organization-contact
/admin/person-contact
```

## 📄 Padrão de CRUD

Cada módulo deve conter:

 - Controller
 - Form (Type)
 - Templates Twig
 - Rotas padronizadas

### Rotas

```text
admin_entity_index
admin_entity_new
admin_entity_show
admin_entity_edit
admin_entity_delete
```

## 🧩 Templates

Localização:

```text
templates/admin/{entity}/
```

Arquivos obrigatórios:

 - index.html.twig
 - new.html.twig
 - edit.html.twig
 - show.html.twig
 - _form.html.twig
 - _delete_form.html.twig

## 🔎 AJAX e Busca Dinâmica

### Quando usar
 - autocomplete
 - listas grandes
 - selects dependentes

### Padrão de endpoints

```text
/admin/{entity}/search
/admin/{entity}/{id}/relations
```

### Exemplo

Buscar organizações:

```text
/admin/organization/search?q=texto
```

Buscar contatos:

```text
/admin/organization/{id}/contacts
```

### Boas práticas
 - limitar resultados (10–20)
 - usar debounce no frontend
 - retornar JSON simples (id + text)
 - nunca carregar listas completas

## 🎯 UX Padrão (IMPORTANTE)

### Seleção dependente

Sempre que houver:

 - grande volume de dados
 - relação pai → filho

Usar fluxo:

```text
Seleciona entidade principal
        ↓
Carrega opções dependentes
```

### Exemplo

```text
Organização → Contatos
Pessoa → Contatos
```

## 📊 CRM (Modelo do Sistema)

### Contato Institucional (PJ)
 - Organization
 - OrganizationContact
 - OrganizationContactInteraction

### Contato Pessoal (PF)
 - Person
 - PersonContact
 - PersonContactInteraction

### Fluxo

```text
Contato institucional
        ↓
Resposta
        ↓
Identificação da pessoa
        ↓
Contato direto
```

## 🧼 Boas Práticas
 - evitar duplicação
 - usar tipagem forte
 - manter métodos pequenos
 - nomes descritivos
 - separar responsabilidades
 - evitar lógica em controllers

## 🔐 Segurança
 - nunca armazenar senha em texto plano
 - usar PasswordHasher
 - validar entradas
 - proteger rotas admin
 - não expor dados sensíveis

## 📊 Dados e Modelagem
 - normalizar dados
 - evitar redundância
 - usar chaves estrangeiras corretamente
 - respeitar hierarquia organizacional

## 🧪 Testes (Planejamento)
 - testes unitários
 - testes de integração
 - testes de API

## ⚠️ Regras Importantes
 - nunca acessar banco diretamente (usar Doctrine)
 - nunca misturar lógica de negócio com controller
 - sempre usar migrations
 - fixtures apenas para dados estruturais

## 🎯 Objetivo

Garantir que o desenvolvimento do SIGI-SD seja:

 - consistente
 - escalável
 - organizado
 - sustentável