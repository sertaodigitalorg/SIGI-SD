# SIGI-SD - Documentação de Fixtures

## Ordem de Carga

1. `GeographyFixtures`
2. `CatalogFixtures`
3. `UserFixtures`
4. `OrganizationFixtures`
5. `InitialContactFixtures`
6. `AppFixtures`

## Catálogos Institucionais

`CatalogFixtures` agora também carrega `OrganizationType`.

Tipos iniciais de organização:
- Instituição de Ciência e Tecnologia
- Associação
- Órgão Público
- Empresa
- Instituição de Ensino

Referências principais:
- `organization_type_ict`
- `organization_type_association`

## Fixtures de Organização

`OrganizationFixtures` cria um cenário institucional hierárquico.

### Estrutura carregada
- `Rede do Sertão`
  - tipo de organização: Associação
  - posição na hierarquia: organização pai
- `Sertão Digital`
  - tipo de organização: Instituição de Ciência e Tecnologia
  - organização pai: `Rede do Sertão`

### Objetivo da hierarquia
- representar redes e mantenedoras
- demonstrar o uso do campo `parent`
- fornecer dados reais para o CRUD e para testes manuais

### Referências criadas
- `organization_rede_inovacao_sertao`
- `organization_sertao_digital`
- `person_wellington_carvalho_silva`
- `person_organization_wellington_sertao_digital`

## Relação com Contatos

`InitialContactFixtures` continua reutilizando `organization_sertao_digital` como organização principal para os contatos institucionais iniciais.

## Recarregar Fixtures

```bash
php bin/console doctrine:fixtures:load
```

## Boas Práticas
- catálogos reutilizáveis devem ficar em `CatalogFixtures`
- cenários do domínio devem ficar em fixtures específicas do módulo
- use referências para relacionar entidades entre classes de fixture
- mantenha textos de seed em português quando forem dados exibidos ao usuário