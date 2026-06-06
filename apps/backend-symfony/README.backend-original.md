# SIGI-SD

Sistema Integrado de Gestão e Inteligência do Sertão Digital.

O SIGI-SD é uma plataforma orientada a dados para apoiar gestão institucional, relacionamento, cobertura territorial e inteligência aplicada a organizações públicas e privadas.

## Base tecnológica

- PHP 8.2+
- Symfony
- Doctrine ORM
- Twig
- MySQL

## Documentação

A documentação principal do projeto está em `/docs`.

Documentos disponíveis:
- `/docs/architecture.md` - arquitetura do sistema
- `/docs/data_model.md` - modelo de dados e relacionamentos
- `/docs/fixtures.md` - estrutura de dados iniciais
- `/docs/dev-guide.md` - convenções de desenvolvimento
- `/docs/api.md` - diretrizes de API
- `/docs/security.md` - segurança e proteção de dados
- `/docs/roadmap.md` - evolução planejada do projeto

## Instalação

### 1. Clonar o repositório

```bash
git clone https://github.com/sertaodigitalorg/SIGI-SD.git
cd SIGI-SD
```

### 2. Instalar dependências

```bash
composer install
```

### 3. Configurar ambiente

Crie o arquivo `.env.local` com a conexão do banco:

```env
DATABASE_URL="mysql://usuario:senha@127.0.0.1:3306/sigi_sd"
```

### 4. Criar banco de dados

```bash
php bin/console doctrine:database:create
```

Caso haja falha verifique as configurações em `.env.local` citado acima no item 3. A base de dados pode ser criada manualmente.

### 5. Executar migrations

```bash
php bin/console doctrine:migrations:migrate
```

### 6. Carregar dados iniciais

```bash
php bin/console doctrine:fixtures:load --no-interaction
```

### 7. Stimulus (Hotwire) suporte a ImportMap

```bash
php bin/console importmap:install
```

Depois:

```bash
php bin/console asset-map:compile
```

### 8. Copilar CSS SASS

Caso solicite a copilação do sass após copilação dos assets.

```bash
php bin/console sass:build
```

### 9. Executar o projeto

```bash
symfony server:start
```

ou

```bash
php -S localhost:8000 -t public
```

## Testes

```bash
./bin/phpunit
```

## Objetivo

O projeto busca consolidar uma base técnica para:
- gestão pública digital
- inteligência territorial
- CRM institucional
- integração de dados
- governança digital