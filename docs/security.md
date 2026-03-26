# SIGI-SD - Segurança e Proteção de Dados

## Visão geral

Este documento reúne diretrizes de segurança do SIGI-SD e princípios relacionados à proteção de dados.

## Princípios

- confidencialidade
- integridade
- disponibilidade
- rastreabilidade
- menor privilégio possível

## Proteção de dados

O sistema deve operar com estas diretrizes:
- coleta mínima de dados
- finalidade clara
- controle de acesso por perfil
- transparência sobre uso de dados
- revisão contínua de exposição de informações sensíveis

## Controle de acesso

Modelo base:
- RBAC

Exemplos de perfis:
- administrador
- operacional
- visualização

## Autenticação

Requisitos atuais e esperados:
- senhas com hash seguro
- sessões seguras
- proteção contra CSRF nas operações de formulário
- evolução futura para mecanismos adicionais quando necessário

## Boas práticas técnicas

- validar entrada de dados
- evitar exposição de dados sensíveis em respostas e logs
- usar HTTPS em produção
- registrar operações críticas
- revisar permissões periodicamente

## Auditoria e incidentes

Direções futuras:
- registro de ações críticas
- auditoria de alterações relevantes
- trilha de quem fez, quando fez e o que alterou
- procedimentos de detecção, isolamento e correção de incidentes

## Objetivo

Garantir que o SIGI-SD evolua com segurança compatível com o contexto institucional e com responsabilidade no tratamento de dados.