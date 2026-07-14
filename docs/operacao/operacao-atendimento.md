# Operacao de atendimento

A operacao de atendimento combina Chatwoot, Botpress e Symfony API. O WhatsApp produtivo entra pelo Chatwoot usando a integracao oficial da Meta Cloud API.

Fluxo basico:

- Cidadao entra por canal digital.
- Chatwoot recebe a mensagem, organiza fila e atendimento humano.
- Botpress faz triagem quando aplicavel.
- Symfony API recebe eventos do Chatwoot, registra entidades, gera/atualiza protocolos, envia o numero do protocolo ao cidadao e integra sistemas externos.

Supervisores acompanham qualidade e SLA operacional. Indicadores analiticos consolidados devem ser enviados para a Plataforma 360.