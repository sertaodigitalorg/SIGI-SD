# Operacao de atendimento

A operacao de atendimento combina Chatwoot, Botpress, Evolution API e Symfony API.

Fluxo basico:

- Cidadao entra por canal digital.
- Evolution API ou outro conector recebe a mensagem.
- Botpress faz triagem quando aplicavel.
- Chatwoot organiza fila e atendimento humano.
- Symfony API registra entidades, protocolos, agendamentos, auditoria e integracoes.

Supervisores acompanham qualidade e SLA operacional. Indicadores analiticos consolidados devem ser enviados para a Plataforma 360.
