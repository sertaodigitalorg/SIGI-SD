# Ambiente WSL2

Recomendacao para desenvolvimento no Windows:

- Usar WSL2 com Ubuntu.
- Usar Docker Desktop integrado ao WSL2.
- Clonar o projeto dentro do filesystem Linux do WSL, quando possivel.
- Usar `docker compose up -d` a partir da raiz do repositorio.

## Passos

```bash
cp .env.example .env
docker compose build
docker compose up -d
docker compose ps
```

## Cuidados

- Nao versionar segredos reais.
- Ajustar senhas no `.env` local.
- Manter volumes persistentes para evitar perda de dados de desenvolvimento.
