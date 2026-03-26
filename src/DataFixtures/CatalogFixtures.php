<?php

namespace App\DataFixtures;

use App\Entity\Role;
use App\Entity\AddressType;
use App\Entity\ContactType;
use App\Entity\ContactStatus;
use App\Entity\ContactIssueType;
use App\Entity\InteractionStatus;
use App\Entity\CoverageType;
use App\Entity\ThematicArea;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CatalogFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $this->loadRoles($manager);
        $this->loadAddressTypes($manager);
        $this->loadContactTypes($manager);
        $this->loadContactStatuses($manager);
        $this->loadContactIssueTypes($manager);
        $this->loadInteractionStatuses($manager);
        $this->loadCoverageTypes($manager);
        $this->loadThematicAreas($manager);

        $manager->flush();
    }

    private function loadRoles(ObjectManager $manager): void
    {
        $items = [
            ['Presidente', 'Principal liderança executiva'],
            ['Diretor', 'Diretor estratégico ou executivo'],
            ['Professor', 'Função educacional'],
            ['Aluno', 'Papel de estudante ou aprendiz'],
            ['Voluntário', 'Colaborador voluntário'],
            ['Pesquisador', 'Atuação acadêmica ou de pesquisa'],
            ['Servidor Público', 'Trabalhador do setor público'],
            ['Vereador', 'Membro do poder legislativo'],
            ['Prefeito', 'Chefe do poder executivo municipal'],
            ['Consultor', 'Consultor técnico ou de negócios'],
            ['Coordenador', 'Coordenação operacional ou estratégica'],
            ['Técnico', 'Função técnica'],
            ['Gerente', 'Função gerencial'],
            ['Mentor', 'Atuação de mentoria'],
            ['Parceiro', 'Aliado ou parceiro institucional'],
        ];

        foreach ($items as [$name, $description]) {
            $entity = new Role();
            $entity->setName($name);
            $entity->setDescription($description);

            $manager->persist($entity);

            if ($name === 'Presidente') {
                $this->addReference('role_presidente', $entity);
            }
        }
    }

    private function loadAddressTypes(ObjectManager $manager): void
    {
        $items = [
            ['Residencial', 'Endereço residencial'],
            ['Comercial', 'Endereço comercial'],
            ['Trabalho', 'Endereço do local de trabalho'],
            ['Fiscal', 'Endereço fiscal ou jurídico'],
            ['Operacional', 'Endereço de unidade operacional'],
            ['Correspondência', 'Endereço para correspondência'],
        ];

        foreach ($items as [$name, $description]) {
            $entity = new AddressType();
            $entity->setName($name);
            $entity->setDescription($description);

            $manager->persist($entity);
        }
    }

    private function loadContactTypes(ObjectManager $manager): void
    {
        $items = [
            ['E-mail', 'Endereço de e-mail', 'communication'],
            ['Telefone', 'Telefone fixo', 'communication'],
            ['Celular', 'Número de celular', 'communication'],
            ['WhatsApp', 'Contato via WhatsApp', 'communication'],
            ['Telegram', 'Contato via Telegram', 'communication'],
            ['Site', 'Endereço de site', 'web'],
            ['Instagram', 'Perfil no Instagram', 'social'],
            ['Facebook', 'Perfil ou página no Facebook', 'social'],
            ['LinkedIn', 'Perfil ou página no LinkedIn', 'social'],
            ['YouTube', 'Canal ou perfil no YouTube', 'social'],
            ['X', 'Perfil no X/Twitter', 'social'],
            ['TikTok', 'Perfil no TikTok', 'social'],
            ['Outro', 'Outro tipo de canal de contato', 'other'],
        ];

        foreach ($items as [$name, $description, $category]) {
            $entity = new ContactType();
            $entity->setName($name);
            $entity->setDescription($description);
            $entity->setCategory($category);

            $manager->persist($entity);

            if ($name === 'E-mail') {
                $this->addReference('contact_type_email', $entity);
            }

            if ($name === 'Site') {
                $this->addReference('contact_type_website', $entity);
            }

            if ($name === 'WhatsApp') {
                $this->addReference('contact_type_whatsapp', $entity);
            }

            if ($name === 'Instagram') {
                $this->addReference('contact_type_instagram', $entity);
            }
        }
    }

    private function loadContactStatuses(ObjectManager $manager): void
    {
        $items = [
            ['Ativo', 'Contato válido e ativo'],
            ['Inativo', 'Contato inativo'],
            ['Inválido', 'Contato inválido'],
            ['Retornado', 'Contato retornado ou rejeitado'],
            ['Pessoa Errada', 'Pertence a outra pessoa'],
            ['Sem Resposta', 'Sem resposta recorrente'],
            ['Bloqueado', 'Bloqueado pelo destinatário ou canal'],
        ];

        foreach ($items as [$name, $description]) {
            $entity = new ContactStatus();
            $entity->setName($name);
            $entity->setDescription($description);

            $manager->persist($entity);

            if ($name === 'Ativo') {
                $this->addReference('contact_status_active', $entity);
            }
        }
    }

    private function loadContactIssueTypes(ObjectManager $manager): void
    {
        $items = [
            ['E-mail Retornado', 'E-mail voltou ou caixa indisponível'],
            ['Telefone Incorreto', 'Número de telefone incorreto'],
            ['Pessoa Errada', 'O contato pertence a outra pessoa'],
            ['Número Não Encontrado', 'Número inexistente ou inacessível'],
            ['Bloqueado pelo Destinatário', 'O destinatário bloqueou o contato'],
            ['Desativado pela Equipe', 'Contato desativado internamente pela equipe'],
            ['Contato Duplicado', 'Registro de contato duplicado'],
            ['Formato Inválido', 'Formato de contato inválido'],
        ];

        foreach ($items as [$name, $description]) {
            $entity = new ContactIssueType();
            $entity->setName($name);
            $entity->setDescription($description);

            $manager->persist($entity);
        }
    }

    private function loadInteractionStatuses(ObjectManager $manager): void
    {
        $items = [
            ['Enviado', 'Tentativa de contato ou mensagem enviada'],
            ['Entregue', 'Entregue com sucesso'],
            ['Lido', 'Lido pelo destinatário'],
            ['Respondido', 'O destinatário respondeu'],
            ['Sem Resposta', 'Nenhuma resposta recebida'],
            ['Falhou', 'Falha no envio ou na execução'],
            ['Retornado', 'Interação retornada ou rejeitada'],
            ['Retorno Agendado', 'Próximo contato agendado'],
            ['Encerrado', 'Ciclo de comunicação encerrado'],
        ];

        foreach ($items as [$name, $description]) {
            $entity = new InteractionStatus();
            $entity->setName($name);
            $entity->setDescription($description);

            $manager->persist($entity);
        }
    }

    private function loadCoverageTypes(ObjectManager $manager): void
    {
        $items = [
            ['Institucional', 'Cobertura territorial institucional'],
            ['Comercial', 'Cobertura territorial comercial'],
            ['Educacional', 'Cobertura territorial educacional'],
            ['Técnica', 'Cobertura territorial técnica'],
            ['Política', 'Cobertura territorial política'],
            ['Social', 'Cobertura territorial social'],
            ['Operacional', 'Cobertura territorial operacional'],
        ];

        foreach ($items as [$name, $description]) {
            $entity = new CoverageType();
            $entity->setName($name);
            $entity->setDescription($description);

            $manager->persist($entity);
        }
    }

    private function loadThematicAreas(ObjectManager $manager): void
    {
        $roots = [];

        $rootItems = [
            'Tecnologia' => 'Tecnologia e soluções digitais',
            'Setor Público' => 'Gestão pública e temas relacionados ao governo',
            'Educação' => 'Educação e aprendizagem',
            'Infraestrutura' => 'Infraestrutura e operações',
            'Inovação' => 'Inovação e empreendedorismo',
        ];

        foreach ($rootItems as $name => $description) {
            $entity = new ThematicArea();
            $entity->setName($name);
            $entity->setDescription($description);
            $entity->setParent(null);

            $manager->persist($entity);
            $roots[$name] = $entity;
        }

        $children = [
            ['Governo Digital', 'Transformação digital no setor público', 'Setor Público'],
            ['Transparência', 'Transparência pública e prestação de contas', 'Setor Público'],
            ['Licitações', 'Compras públicas e licitações', 'Setor Público'],
            ['Gestão Pública', 'Administração pública e governança', 'Setor Público'],

            ['Desenvolvimento de Software', 'Engenharia e desenvolvimento de software', 'Tecnologia'],
            ['Desenvolvimento Web', 'Plataformas e aplicações web', 'Tecnologia'],
            ['Arquitetura', 'Arquitetura de software e sistemas', 'Tecnologia'],
            ['Infraestrutura de TI', 'Infraestrutura tecnológica e hospedagem', 'Tecnologia'],
            ['Dados e IA', 'Análise de dados, BI e inteligência artificial', 'Tecnologia'],
            ['Cibersegurança', 'Segurança e proteção de dados', 'Tecnologia'],
            ['Código Aberto', 'Software livre e comunidades open source', 'Tecnologia'],

            ['Gestão Escolar', 'Gestão educacional e escolar', 'Educação'],
            ['Formação Técnica', 'Educação técnica e desenvolvimento de habilidades', 'Educação'],
            ['Inclusão Digital', 'Alfabetização digital e inclusão social', 'Educação'],

            ['Infraestrutura Urbana', 'Infraestrutura urbana e serviços públicos', 'Infraestrutura'],
            ['Mobilidade', 'Transporte e mobilidade', 'Infraestrutura'],
            ['Energia', 'Energia e sustentabilidade', 'Infraestrutura'],

            ['Empreendedorismo', 'Negócios, startups e empreendedorismo', 'Inovação'],
            ['GovTech', 'Tecnologia para governo', 'Inovação'],
            ['Inovação Cívica', 'Inovação voltada ao interesse público', 'Inovação'],
        ];

        foreach ($children as [$name, $description, $parentName]) {
            $entity = new ThematicArea();
            $entity->setName($name);
            $entity->setDescription($description);
            $entity->setParent($roots[$parentName]);

            $manager->persist($entity);
        }
    }
}