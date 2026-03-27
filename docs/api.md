# SIGI-SD — API e Padrões de Integração

## Visão Geral

O SIGI-SD possui dois tipos principais de endpoints:

1. Endpoints administrativos (uso interno via Twig)
2. Endpoints AJAX (suporte à interface dinâmica)
3. Estrutura futura de API REST pública

---

## 🧱 Tipos de Endpoints

### 1. Admin (Server Rendered)

Endpoints utilizados pelo sistema interno:

```text
/admin/*
```

Exemplos:
 - /admin/organization
 - /admin/person
 - /admin/organization-contact
 - /admin/person-contact
 - /admin/dashboard

### 2. AJAX (Interface Dinâmica)

Endpoints utilizados para:
 - autocomplete
 - carregamento dependente
 - busca dinâmica

Formato:

```text
/admin/*/search
/admin/*/{id}/relations
```

### 3. API REST (Futuro)

Formato planejado:

```text
/api/v1/*
```

## 🔎 Endpoints AJAX Implementados

### Buscar Organizações

```http
GET /admin/organization/search?q=texto
```

### Query Params:
 - q: termo de busca

### Busca por:
 - legalName
 - tradeName
 - acronym
 - cnpj

### Resposta (JSON):

```json
[
  {
    "id": 1,
    "text": "Prefeitura Municipal de Sousa"
  }
]
```

### Buscar Contatos da Organização

```http
GET /admin/organization/{id}/contacts
```

### Parâmetros:
 - id: ID da organização

### Comportamento:
 - retorna apenas contatos ativos
 - ordena por principal primeiro

### Resposta:

```json
[
  {
    "id": 10,
    "text": "E-mail — contato@prefeitura.pb.gov.br"
  },
  {
    "id": 11,
    "text": "WhatsApp — (83) 99999-9999"
  }
]
```

## 📊 Endpoints de Dashboard (Interno)

Utilizados pelo DashboardService:

 - métricas PF (PersonContactInteraction)
 - métricas PJ (OrganizationContactInteraction)

Esses endpoints são internos ao sistema (não expostos publicamente).

## 📦 Estrutura Padrão de Resposta

### Sucesso

```json
{
  "success": true,
  "data": {}
}
```

### Erro

```json
{
  "success": false,
  "message": "Erro ao processar requisição"
}
```

## 🔐 Segurança

### Admin
- acesso restrito a usuários autenticados
- controle por roles (ROLE_ADMIN, ROLE_USER)

### AJAX
 - protegido por sessão
 - não expor dados sensíveis
 - limitar resultados (ex: max 10–20 registros)

### API futura
 - autenticação via JWT ou OAuth2
 - versionamento obrigatório (/api/v1)
 - rate limiting

## 📌 Boas Práticas
 - nunca retornar listas completas sem filtro
 - sempre usar paginação em endpoints públicos
 - evitar expor IDs internos sem necessidade
 - padronizar respostas JSON
 - validar todos os inputs

## 🚀 Estrutura da API REST (Futuro)

### Organizações

```http
GET    /api/v1/organizations
POST   /api/v1/organizations
GET    /api/v1/organizations/{id}
PUT    /api/v1/organizations/{id}
DELETE /api/v1/organizations/{id}
```

### Pessoas

```http
GET    /api/v1/persons
POST   /api/v1/persons
GET    /api/v1/persons/{id}
PUT    /api/v1/persons/{id}
DELETE /api/v1/persons/{id}
```

### Contatos

```http
GET /api/v1/organization-contacts
GET /api/v1/person-contacts
```

### Interações

```http
GET /api/v1/interactions
POST /api/v1/interactions
```

## 🔄 Integrações Futuras

O sistema está preparado para integração com:

- sistemas de prefeituras
- ERPs públicos (ex: e-Cidade)
- plataformas de dados governamentais
- serviços de mensageria (WhatsApp, e-mail)
- ferramentas de BI

## 📌 Considerações Finais

A API do SIGI-SD foi projetada para:
 - suportar grandes volumes de dados
 - garantir segurança institucional
 - permitir expansão nacional
 - integrar com ecossistemas públicos e privados

## 🔮 Evoluções Futuras
 - API pública documentada (Swagger/OpenAPI)
 - Webhooks para eventos
 - Integração com chatbots
 - Automação de follow-ups
 - Motor de recomendação de contatos