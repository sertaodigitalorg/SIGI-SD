# SIGI-SD: Canais WhatsApp e Instagram via Chatwoot

## Objetivo

Preparar o SIGI-SD para operar WhatsApp e Instagram pelo mesmo motor ja validado para e-mail:

Canal externo -> Chatwoot -> webhook SIGI -> pessoa/contato -> protocolo -> etiquetas -> dashboard -> resposta pelo Chatwoot.

O SIGI nao substitui o Chatwoot. O Chatwoot segue como camada operacional de atendimento, enquanto o SIGI consolida protocolo, governanca, auditoria, historico e indicadores.

## Arquitetura

- Entrada: inbox nativo do Chatwoot para e-mail, WhatsApp ou Instagram.
- Normalizacao: `ChatwootConversationNormalizer` identifica o canal e produz um DTO unico.
- Canal: `ChatwootChannelMapper` converte nomes e `channel_type` do Chatwoot para `email`, `whatsapp`, `instagram`, `webchat` ou `unknown`.
- Pessoa: `ChatwootContactSyncService` cria ou reaproveita pessoa por `contact_id`, e-mail, telefone ou username.
- Protocolo: `ChatwootConversationSyncService` busca protocolo por conversa Chatwoot antes de criar um novo.
- Etiquetas: etiquetas operacionais sao aplicadas de forma idempotente, sem interromper o protocolo se o Chatwoot falhar.

## Fluxo WhatsApp

1. Mensagem chega no inbox WhatsApp do Chatwoot.
2. Webhook envia o evento para o SIGI.
3. SIGI detecta `whatsapp` pelo inbox ou `channel_type`.
4. SIGI normaliza telefone.
5. SIGI localiza pessoa por `chatwootContactId` ou telefone.
6. SIGI cria contato do tipo `WhatsApp` quando necessario.
7. SIGI gera ou reaproveita protocolo pela conversa.
8. SIGI aplica etiquetas como `origem-whatsapp`, `canal-whatsapp` e `sigi-protocolo-gerado`.
9. SIGI envia mensagem publica de protocolo pelo proprio Chatwoot, uma unica vez.

## Fluxo Instagram

1. Mensagem chega no inbox Instagram do Chatwoot.
2. Webhook envia o evento para o SIGI.
3. SIGI detecta `instagram` pelo inbox ou `channel_type`.
4. SIGI extrai username/id disponivel no payload.
5. SIGI localiza pessoa por `chatwootContactId` ou contato ja cadastrado.
6. SIGI cria contato do tipo `Instagram` quando necessario.
7. SIGI gera ou reaproveita protocolo pela conversa.
8. SIGI aplica etiquetas como `origem-instagram`, `canal-instagram` e `sigi-protocolo-gerado`.
9. SIGI envia mensagem publica de protocolo pelo proprio Chatwoot, uma unica vez.

## Configuracao Chatwoot

1. Criar/configurar inbox WhatsApp no Chatwoot.
2. Criar/configurar inbox Instagram no Chatwoot.
3. Configurar webhook do Chatwoot apontando para `/admin/integrations/chatwoot/webhook/{accountId}`.
4. Enviar o segredo em `X-SIGI-CHATWOOT-SECRET` ou `X-Chatwoot-Webhook-Secret`.
5. Garantir que o usuario/token da conta SIGI tenha permissao para criar mensagens e aplicar labels.

## Variaveis

- `CHATWOOT_BASE_URL`
- `CHATWOOT_INTERNAL_BASE_URL`
- `CHATWOOT_ACCOUNT_ID`
- `CHATWOOT_API_TOKEN`
- `CHATWOOT_INBOX_ID`
- `SIGI_CHATWOOT_URL`
- `SIGI_PUBLIC_URL`

## Etiquetas

Gerais:

- `sigi-sincronizado`
- `sigi-novo`
- `sigi-protocolo-gerado`
- `prioridade-normal`
- `setor-atendimento-geral`

WhatsApp:

- `origem-whatsapp`
- `canal-whatsapp`
- `sigi-whatsapp-validado`
- `contato-telefone-identificado`
- `protocolo-enviado-whatsapp`

Instagram:

- `origem-instagram`
- `canal-instagram`
- `sigi-instagram-validado`
- `contato-social-identificado`
- `protocolo-enviado-instagram`

## Cenarios de teste

- Receber `conversation_created` de WhatsApp e criar protocolo.
- Receber `message_created` da mesma conversa e nao duplicar protocolo.
- Criar ou vincular pessoa por telefone normalizado.
- Aplicar etiquetas de WhatsApp.
- Enviar protocolo publico uma unica vez.
- Receber `conversation_created` de Instagram e criar protocolo.
- Criar ou vincular pessoa por username.
- Aplicar etiquetas de Instagram.
- Garantir que e-mail segue gerando protocolo no mesmo fluxo.

## Payloads

Payloads simulados estao em:

- `docs/payloads/chatwoot-whatsapp-conversation-created.json`
- `docs/payloads/chatwoot-whatsapp-message-created.json`
- `docs/payloads/chatwoot-instagram-conversation-created.json`
- `docs/payloads/chatwoot-instagram-message-created.json`

Eles sao exemplos compativeis com a estrutura esperada pelo normalizador. Quando houver payload real do ambiente, substituir ou complementar estes exemplos.

## Proximos passos

- Persistir identidade multicanal em tabela propria se a auditoria exigir historico por provider/source id.
- Exibir `inboxId`, link Chatwoot e payload bruto na pagina de detalhe do protocolo em modo debug.
- Adicionar testes funcionais com kernel Symfony para os quatro payloads.
