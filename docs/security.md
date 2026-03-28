# SIGI-SD — Segurança, LGPD e Governança

## Visão Geral

O SIGI-SD trata dados institucionais e pessoais, incluindo:

- nomes de pessoas
- contatos (e-mail, telefone, WhatsApp)
- CPF
- histórico de comunicação

Portanto, o sistema deve seguir princípios de:

- segurança da informação
- privacidade
- rastreabilidade
- conformidade com a LGPD

---

## 🔐 Princípios de Segurança

- menor privilégio (least privilege)
- segregação de responsabilidades
- proteção de dados sensíveis
- rastreabilidade de ações
- controle de acesso rigoroso

---

## 👥 Controle de Acesso (RBAC)

O sistema deve utilizar **controle baseado em papéis (roles)**.

### Papéis básicos

- `ROLE_ADMIN`
- `ROLE_USER`
- `ROLE_MANAGER` (futuro)
- `ROLE_VIEWER` (futuro)

---

### Regras

- apenas usuários autenticados acessam `/admin`
- operações críticas exigem permissões específicas
- exclusões devem ser protegidas por CSRF

---

## 🔑 Autenticação

- login via e-mail/usuário + senha
- uso de `PasswordHasher`
- nunca armazenar senha em texto plano
- recomendação futura:
  - autenticação em dois fatores (2FA)

---

## 🧾 Autorização

- verificar permissões em controllers
- proteger rotas administrativas
- impedir acesso direto a dados sem autorização

---

## 📊 Proteção de Dados (LGPD)

### Dados tratados

- CPF
- contatos pessoais
- histórico de comunicação

---

### Princípios LGPD aplicáveis

- finalidade específica
- necessidade mínima
- transparência
- segurança
- prevenção

---

### Boas práticas

- evitar armazenar dados desnecessários
- permitir anonimização futura
- evitar exposição de dados sensíveis na interface
- mascarar dados quando possível

---

## 🔒 Dados Sensíveis

### CPF

- armazenar como string
- validar formato (opcional)
- evitar exibição completa em telas públicas
- recomendação futura:
  - mascarar parcialmente (ex: ***.***.***-00)

---

### Contatos

- tratar e-mails e telefones como dados sensíveis
- não expor publicamente sem necessidade
- controlar acesso por perfil

---

## 🧠 Histórico de Interações

Os dados de interação incluem:

- mensagens enviadas
- respostas recebidas
- datas de contato

Esses dados podem conter informações sensíveis.

### Regras

- acesso restrito
- logs de acesso recomendados
- evitar exportação sem controle

---

## 🧾 Auditoria

### Objetivo

Garantir rastreabilidade completa do sistema.

---

### Eventos a registrar (futuro)

- criação de registros
- edição de registros
- exclusão de registros
- login/logout
- envio de comunicação

---

### Estrutura sugerida

Entidade:
- `AuditLog`

Campos:
- usuário
- ação
- entidade
- ID da entidade
- data/hora
- dados alterados (JSON)

---

## 🌐 Segurança de API (Futuro)

- autenticação via JWT ou OAuth2
- versionamento (/api/v1)
- rate limiting
- validação de entrada

---

## 🔍 Validação de Entrada

- validar todos os inputs
- evitar SQL injection (Doctrine já protege)
- evitar XSS (Twig auto-escape)
- sanitizar entradas externas

---

## 🛡️ Proteções Técnicas

- CSRF protection em formulários
- validação backend obrigatória
- uso de prepared statements (Doctrine)
- escaping automático (Twig)

---

## 📁 Arquivos e Uploads (Futuro)

- validar tipo de arquivo
- limitar tamanho
- evitar execução de arquivos
- armazenar fora da raiz pública

---

## 🔐 Infraestrutura

Recomendações:

- uso de HTTPS obrigatório
- backups periódicos
- controle de acesso ao banco de dados
- ambientes separados:
  - dev
  - staging
  - produção

---

## 🚨 Incidentes de Segurança

Procedimentos futuros:

- identificar incidente
- bloquear acesso
- registrar evento
- notificar responsáveis
- avaliar impacto (LGPD)

---

## 📌 Boas Práticas Gerais

- nunca expor dados desnecessários
- revisar permissões frequentemente
- manter dependências atualizadas
- usar logs para monitoramento

---

## 🎯 Objetivo

Garantir que o SIGI-SD seja:

- seguro
- confiável
- auditável
- compatível com LGPD
- preparado para uso institucional e governamental

---

## 🔮 Evoluções Futuras

- criptografia de dados sensíveis
- mascaramento dinâmico de dados
- controle de acesso por entidade (row-level)
- logs completos de auditoria
- compliance automatizado