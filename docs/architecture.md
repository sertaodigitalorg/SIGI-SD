# SIGI-SD - Arquitetura do Sistema

## Visão Geral

O módulo institucional do SIGI-SD trata organizações como uma estrutura hierárquica e classificável. Isso permite representar redes, mantenedoras, unidades executoras e demais entidades relacionadas sem duplicar dados.

## Camadas Relevantes

### Domínio
- `Organization` representa a organização operacional.
- `OrganizationType` classifica a natureza institucional da organização.
- `parent` em `Organization` forma a hierarquia entre organizações.

### Persistência
- A tabela `organizations` guarda os dados cadastrais e a referência opcional para a organização pai.
- A tabela `organization_types` centraliza os tipos de organização reutilizáveis.
- As chaves estrangeiras usam `ON DELETE SET NULL` para evitar perda em cascata da estrutura institucional.

### Aplicação
- O CRUD de organizações fica em `OrganizationController`.
- O formulário `OrganizationTypeForm` concentra as regras de entrada de dados.
- A validação de hierarquia impede `parent = self` e bloqueia ciclos indiretos.

## Modelagem Institucional

### Hierarquia com `parent`
- Use `parent` quando uma organização estiver subordinada, vinculada ou mantida por outra.
- Organizações raiz ficam com `parent = null`.
- Organizações filhas podem ser exibidas a partir da própria entidade, usando a coleção `children`.

### Classificação com `organizationType`
- Use `organizationType` para indicar o tipo de organização.
- O tipo não substitui a hierarquia: ele apenas classifica a entidade.
- Exemplos: associação, órgão público, empresa, instituição de ensino, instituição de ciência e tecnologia.

## Fluxo do Módulo

1. Catálogos carregam os tipos de organização.
2. Fixtures institucionais criam organizações pai e filhas.
3. O CRUD permite consultar, cadastrar, editar e excluir organizações.
4. Templates exibem valores nulos com mensagens amigáveis em português.

## Decisões de Arquitetura
- Hierarquia e classificação foram separadas para evitar sobrecarga semântica em um único campo.
- `OrganizationType` é catálogo próprio para permitir expansão sem alterar a tabela principal.
- A validação de ciclo foi implementada na entidade para proteger qualquer ponto de entrada, não apenas o formulário.