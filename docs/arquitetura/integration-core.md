# SIGI-SD Integration Core — Especificação técnica inicial

**Data:** 2026-10-10  
**Estado:** proposta para revisão técnica (não implementado)  
**Escopo:** camada de interoperabilidade do SIGI-SD com softwares públicos e serviços governamentais.

## 1. Decisões
1. O SIGI-SD mantém o núcleo próprio de CiRM (cidadão, atendimento, casos, protocolos, catálogo, comunicação e auditoria).
2. Sistemas setoriais são fontes de verdade para seus domínios; não replicar bases completas sem necessidade e fundamento legal.
3. Integrações são opcionais, desacopladas, versionadas e ativadas por órgão/instalação.
4. SDK institucional reutilizável não deve depender de um fornecedor ou serviço federal específico.
5. Toda integração passa por avaliação de licença, segurança, elegibilidade, LGPD, acessibilidade e manutenção.
6. OpenCiRM, CiviCRM e e-SIC Livre são referências funcionais; nenhum deles é adotado como fork-base.

## 2. Arquitetura lógica
Canal (Chatwoot/portal/presencial) -> Atendimento SIGI-SD -> Serviço de aplicação -> Integration Core -> Adapter -> Sistema externo.

**Módulos propostos:**
- `IntegrationRegistry`: catálogo de conectores, versão, capacidades e requisitos;
- `ConnectionConfig`: configuração por instalação/órgão, segredos em cofre ou variáveis seguras;
- `IntegrationGateway`: execução controlada de operações e autorização;
- `IdentityMapping`: vínculo entre IDs internos e externos, sem pressupor CPF como chave técnica universal;
- `EventInbox/Outbox`: entrega assíncrona, deduplicação e rastreabilidade;
- `AuditTrail`: quem acessou, finalidade, recurso, resultado e correlação;
- `HealthMonitor`: disponibilidade e métricas de falha;
- `DataPolicy`: minimização, retenção e classificação de dados.

## 3. Contrato mínimo de adaptador (proposta)
- `id`, `name`, `version`, `capabilities[]`, `supportedVersions[]`;
- `configure(connection)`, `healthCheck()`;
- `execute(operation, payload, context)` com validação por schema;
- `context`: `tenantId`, `actorId`, `purpose`, `correlationId`, `idempotencyKey`;
- resposta: `status`, `externalReference`, `data`, `errors[]`, `correlationId`.

Não pressupor que todos os fornecedores ofereçam API: registrar capacidade `api | webhook | file_exchange | manual` e limitar operações conforme suporte real.

## 4. Regras de segurança e resiliência
- Credenciais por organização; menor privilégio; segredos nunca em logs.
- Autenticação/autorização específicas por integração; GOV.BR apenas mediante habilitação oficial.
- TLS, validação de entrada, allowlist de destinos, proteção SSRF e assinatura de webhooks quando disponível.
- Timeout, retry exponencial somente para operações seguras, circuit breaker e fila de falhas.
- Idempotência em comandos com efeito; não repetir protocolo/criação sem chave verificável.
- Logs com dados pessoais mascarados; trilha de auditoria imutável conforme política.
- Isolamento municipal; evitar vazamento entre tenants.
- Modo degradado: indisponibilidade externa não bloqueia abertura do atendimento local.
- Política de retenção, acesso, base legal e compartilhamento documentada por conector.

## 5. Matriz de conectores (proposta, não homologação)

| Conector | Operação piloto | Pré-requisito | Situação |
|---|---|---|---|
| i-Educar | Consultar situação de solicitação escolar autorizada | Verificar API/versão, ambiente e credenciais | Estudo |
| e-Cidade | Consultar protocolo ou serviço municipal | Verificar instalação, API e autorização | Estudo |
| SAPL-SD | Consultar tramitação pública de proposição | Verificar API e versão do fork | Estudo |
| Fala.BR | Encaminhamento/consulta quando institucionalmente permitido | Confirmar interface e elegibilidade | Pesquisa |
| e-SIC Livre | Intercâmbio com instalação existente | Avaliar legado e ausência/presença de API | Referência |
| GOV.BR | Autenticação/assinatura, quando habilitadas | Credenciamento e requisitos oficiais | Pesquisa |

## 6. Prova de conceito 01 — i-Educar
**Objetivo:** demonstrar um atendimento iniciado no SIGI-SD e enriquecido por consulta autorizada ao i-Educar.

**Sequência:** abrir atendimento -> selecionar serviço escolar -> verificar identidade e autorização -> consultar adaptador em ambiente de testes -> registrar somente referência e resumo necessário -> informar resultado ou encaminhar ao servidor.

**Critérios de aceite:**
1. Isolamento entre duas organizações testado.
2. Credenciais fora do repositório e logs.
3. Timeout, indisponibilidade e respostas inválidas tratados.
4. Registro de auditoria e `correlationId` presente.
5. Contratos e mocks testados em CI.
6. Nenhum dado real de aluno usado sem autorização e fundamento legal.
7. Integração pode ser desativada sem impedir atendimento local.
8. Evidência de versão e documentação da API anexada à homologação.

## 7. Governança e entregas
- Etapa A: revisar esta especificação e conciliar o roadmap legado (CRM comercial/BI) com a definição atual de CiRM.
- Etapa B: implementar interfaces, registro, configuração segura e adaptador mock.
- Etapa C: testes de contrato, integração, LGPD e observabilidade.
- Etapa D: PoC i-Educar; só depois decidir conectores seguintes.
- Fluxo de branches: `dev -> hml -> main`, com revisão e checks; esta proposta não autoriza merge automático.
- Cada integração deve ter issue, ADR, matriz de capacidades, responsável, fonte, versão, licença e evidências de homologação.

## 8. Referências
- Matriz anterior: `SIGI-SD_Matriz_Software_Publico_2026-10-10.md` (arquivo produzido para revisão; ainda não incorporado aqui).
- https://www.gov.br/governodigital/pt-br/plataformas-e-servicos-digitais/software-publico
- https://github.com/portabilis/i-educar
- https://github.com/esiclivre/esiclivre
