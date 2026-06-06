# docker-wsl

## Objetivo

Manter o ambiente Docker Compose pronto para WSL2 no Windows.

## Responsabilidades

- Docker Compose.
- Redes e volumes.
- Traefik.
- Portainer.
- Scripts de inicializacao.

## Limites

- Nao versionar segredos.
- Nao acoplar ambiente local a caminhos absolutos de maquina.

## Padroes tecnicos

- Rede `sigi-network`.
- Volumes persistentes nomeados.
- Dominios `*.sigi.localhost`.

## Checklist

- O servico esta na rede correta?
- Existe volume persistente quando necessario?
- Labels Traefik estao corretas?
- Variaveis sensiveis usam `.env`?
