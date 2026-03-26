<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260326204536 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE address_types (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, description VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_6E95FE105E237E06 (name), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE addresses (id INT AUTO_INCREMENT NOT NULL, street VARCHAR(191) NOT NULL, number VARCHAR(255) DEFAULT NULL, complement VARCHAR(255) DEFAULT NULL, neighborhood VARCHAR(255) DEFAULT NULL, zip_code VARCHAR(10) DEFAULT NULL, reference VARCHAR(255) DEFAULT NULL, latitude NUMERIC(10, 8) DEFAULT NULL, longitude NUMERIC(11, 8) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, city_id INT NOT NULL, INDEX IDX_6FCA75168BAC62AF (city_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE cities (id INT AUTO_INCREMENT NOT NULL, area_km2 NUMERIC(15, 2) DEFAULT NULL, gdp NUMERIC(18, 2) DEFAULT NULL, population INT DEFAULT NULL, annual_revenue NUMERIC(18, 2) DEFAULT NULL, tom_code VARCHAR(20) DEFAULT NULL, ibge_code VARCHAR(20) DEFAULT NULL, ibge_code7 VARCHAR(20) DEFAULT NULL, zip_code VARCHAR(10) DEFAULT NULL, tom_name VARCHAR(191) DEFAULT NULL, ibge_name VARCHAR(191) NOT NULL, size VARCHAR(50) DEFAULT NULL, is_capital TINYINT DEFAULT 0 NOT NULL, microregion_id INT DEFAULT NULL, state_id INT NOT NULL, INDEX IDX_D95DB16BC4BB49CF (microregion_id), INDEX IDX_D95DB16B5D83CC1 (state_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE comment (id INT AUTO_INCREMENT NOT NULL, content LONGTEXT NOT NULL, published_at DATETIME NOT NULL, post_id INT NOT NULL, author_id INT NOT NULL, INDEX IDX_9474526C4B89032C (post_id), INDEX IDX_9474526CF675F31B (author_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE contact_issue_types (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, description VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_A5165FDD5E237E06 (name), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE contact_statuses (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, description VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_D1D582865E237E06 (name), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE contact_types (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, description VARCHAR(255) DEFAULT NULL, category VARCHAR(50) DEFAULT NULL, UNIQUE INDEX UNIQ_741A993F5E237E06 (name), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE countries (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(191) NOT NULL, iso2 VARCHAR(2) NOT NULL, iso3 VARCHAR(3) NOT NULL, numeric_code VARCHAR(3) DEFAULT NULL, phone_code VARCHAR(10) DEFAULT NULL, currency VARCHAR(10) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_5D66EBAD1B6F9774 (iso2), UNIQUE INDEX UNIQ_5D66EBAD6C68A7E2 (iso3), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE coverage_types (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, description VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_3ABE0C9D5E237E06 (name), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE interaction_statuses (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, description VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_712509E85E237E06 (name), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE mesoregions (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(191) NOT NULL, ibge_code VARCHAR(20) NOT NULL, municipalities_count INT DEFAULT NULL, state_id INT NOT NULL, UNIQUE INDEX UNIQ_38E74E6D7E3DDF8B (ibge_code), INDEX IDX_38E74E6D5D83CC1 (state_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE microregions (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(191) NOT NULL, ibge_code VARCHAR(20) NOT NULL, mesoregion_id INT NOT NULL, UNIQUE INDEX UNIQ_34F4438E7E3DDF8B (ibge_code), INDEX IDX_34F4438ECF59994E (mesoregion_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE organization_addresses (id INT AUTO_INCREMENT NOT NULL, is_primary TINYINT DEFAULT 0 NOT NULL, organization_id INT NOT NULL, address_id INT NOT NULL, address_type_id INT NOT NULL, INDEX IDX_57FDAF1032C8A3DE (organization_id), INDEX IDX_57FDAF10F5B7AF75 (address_id), INDEX IDX_57FDAF109EA97B0B (address_type_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE organization_contact_interactions (id INT AUTO_INCREMENT NOT NULL, contacted_at DATETIME NOT NULL, subject VARCHAR(191) DEFAULT NULL, message LONGTEXT DEFAULT NULL, response_received TINYINT DEFAULT 0 NOT NULL, response_text LONGTEXT DEFAULT NULL, next_contact_at DATETIME DEFAULT NULL, notes LONGTEXT DEFAULT NULL, organization_contact_id INT NOT NULL, interaction_status_id INT DEFAULT NULL, performed_by_id INT DEFAULT NULL, INDEX IDX_C3C27699EFAB7A5 (organization_contact_id), INDEX IDX_C3C27699AFAC9F12 (interaction_status_id), INDEX IDX_C3C276992E65C292 (performed_by_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE organization_contacts (id INT AUTO_INCREMENT NOT NULL, value VARCHAR(191) NOT NULL, label VARCHAR(100) DEFAULT NULL, is_primary TINYINT DEFAULT 0 NOT NULL, is_public TINYINT DEFAULT 0 NOT NULL, notes LONGTEXT DEFAULT NULL, deactivated_at DATETIME DEFAULT NULL, deactivation_reason LONGTEXT DEFAULT NULL, organization_id INT NOT NULL, contact_type_id INT NOT NULL, status_id INT DEFAULT NULL, issue_type_id INT DEFAULT NULL, INDEX IDX_DC58D7D132C8A3DE (organization_id), INDEX IDX_DC58D7D15F63AD12 (contact_type_id), INDEX IDX_DC58D7D16BF700BD (status_id), INDEX IDX_DC58D7D160B4C972 (issue_type_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE organization_coverages (id INT AUTO_INCREMENT NOT NULL, notes LONGTEXT DEFAULT NULL, is_primary TINYINT DEFAULT 0 NOT NULL, organization_id INT NOT NULL, city_id INT DEFAULT NULL, state_id INT DEFAULT NULL, region_id INT DEFAULT NULL, coverage_type_id INT NOT NULL, INDEX IDX_F90CFB2E32C8A3DE (organization_id), INDEX IDX_F90CFB2E8BAC62AF (city_id), INDEX IDX_F90CFB2E5D83CC1 (state_id), INDEX IDX_F90CFB2E98260155 (region_id), INDEX IDX_F90CFB2E43375A16 (coverage_type_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE organization_thematic_areas (id INT AUTO_INCREMENT NOT NULL, notes LONGTEXT DEFAULT NULL, is_primary TINYINT DEFAULT 0 NOT NULL, organization_id INT NOT NULL, thematic_area_id INT NOT NULL, INDEX IDX_28F1802232C8A3DE (organization_id), INDEX IDX_28F18022328F8B8E (thematic_area_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE organization_types (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, description VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_D7B76FF75E237E06 (name), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE organizations (id INT AUTO_INCREMENT NOT NULL, legal_name VARCHAR(191) NOT NULL, trade_name VARCHAR(191) DEFAULT NULL, cnpj VARCHAR(18) NOT NULL, acronym VARCHAR(50) DEFAULT NULL, status VARCHAR(50) DEFAULT NULL, notes LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, parent_id INT DEFAULT NULL, organization_type_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_427C1C7FC8C6906B (cnpj), INDEX IDX_427C1C7F727ACA70 (parent_id), INDEX IDX_427C1C7F89E04D0 (organization_type_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE person_addresses (id INT AUTO_INCREMENT NOT NULL, is_primary TINYINT DEFAULT 0 NOT NULL, person_id INT NOT NULL, address_id INT NOT NULL, address_type_id INT NOT NULL, INDEX IDX_D7E396BC217BBB47 (person_id), INDEX IDX_D7E396BCF5B7AF75 (address_id), INDEX IDX_D7E396BC9EA97B0B (address_type_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE person_contact_interactions (id INT AUTO_INCREMENT NOT NULL, contacted_at DATETIME NOT NULL, subject VARCHAR(191) DEFAULT NULL, message LONGTEXT DEFAULT NULL, response_received TINYINT DEFAULT 0 NOT NULL, response_text LONGTEXT DEFAULT NULL, next_contact_at DATETIME DEFAULT NULL, notes LONGTEXT DEFAULT NULL, person_contact_id INT NOT NULL, interaction_status_id INT DEFAULT NULL, performed_by_id INT DEFAULT NULL, INDEX IDX_9B7AEBDDD11A465F (person_contact_id), INDEX IDX_9B7AEBDDAFAC9F12 (interaction_status_id), INDEX IDX_9B7AEBDD2E65C292 (performed_by_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE person_contacts (id INT AUTO_INCREMENT NOT NULL, value VARCHAR(191) NOT NULL, label VARCHAR(100) DEFAULT NULL, is_primary TINYINT DEFAULT 0 NOT NULL, is_public TINYINT DEFAULT 0 NOT NULL, notes LONGTEXT DEFAULT NULL, deactivated_at DATETIME DEFAULT NULL, deactivation_reason LONGTEXT DEFAULT NULL, person_id INT NOT NULL, contact_type_id INT NOT NULL, status_id INT DEFAULT NULL, issue_type_id INT DEFAULT NULL, INDEX IDX_A706B044217BBB47 (person_id), INDEX IDX_A706B0445F63AD12 (contact_type_id), INDEX IDX_A706B0446BF700BD (status_id), INDEX IDX_A706B04460B4C972 (issue_type_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE person_coverages (id INT AUTO_INCREMENT NOT NULL, notes LONGTEXT DEFAULT NULL, is_primary TINYINT DEFAULT 0 NOT NULL, person_id INT NOT NULL, city_id INT DEFAULT NULL, state_id INT DEFAULT NULL, region_id INT DEFAULT NULL, coverage_type_id INT NOT NULL, INDEX IDX_7912C282217BBB47 (person_id), INDEX IDX_7912C2828BAC62AF (city_id), INDEX IDX_7912C2825D83CC1 (state_id), INDEX IDX_7912C28298260155 (region_id), INDEX IDX_7912C28243375A16 (coverage_type_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE person_organization_roles (id INT AUTO_INCREMENT NOT NULL, start_date DATETIME DEFAULT NULL, end_date DATETIME DEFAULT NULL, person_organization_id INT NOT NULL, role_id INT NOT NULL, INDEX IDX_E8D00FA7F34C6345 (person_organization_id), INDEX IDX_E8D00FA7D60322AC (role_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE person_organizations (id INT AUTO_INCREMENT NOT NULL, start_date DATETIME NOT NULL, end_date DATETIME DEFAULT NULL, status VARCHAR(50) DEFAULT NULL, notes LONGTEXT DEFAULT NULL, person_id INT NOT NULL, organization_id INT NOT NULL, INDEX IDX_4AF22FB2217BBB47 (person_id), INDEX IDX_4AF22FB232C8A3DE (organization_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE person_thematic_areas (id INT AUTO_INCREMENT NOT NULL, notes LONGTEXT DEFAULT NULL, is_primary TINYINT DEFAULT 0 NOT NULL, person_id INT NOT NULL, thematic_area_id INT NOT NULL, INDEX IDX_3D3E53AF217BBB47 (person_id), INDEX IDX_3D3E53AF328F8B8E (thematic_area_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE persons (id INT AUTO_INCREMENT NOT NULL, full_name VARCHAR(191) NOT NULL, cpf VARCHAR(14) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_A25CC7D33E3E11F0 (cpf), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE post (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(191) NOT NULL, slug VARCHAR(191) NOT NULL, summary VARCHAR(191) NOT NULL, content LONGTEXT NOT NULL, published_at DATETIME NOT NULL, author_id INT NOT NULL, INDEX IDX_5A8A6C8DF675F31B (author_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE post_tag (post_id INT NOT NULL, tag_id INT NOT NULL, INDEX IDX_5ACE3AF04B89032C (post_id), INDEX IDX_5ACE3AF0BAD26311 (tag_id), PRIMARY KEY (post_id, tag_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE regions (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(191) NOT NULL, country_id INT NOT NULL, INDEX IDX_A26779F3F92F3E70 (country_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE roles (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, description VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_B63E2EC75E237E06 (name), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE states (id INT AUTO_INCREMENT NOT NULL, uf VARCHAR(2) NOT NULL, name VARCHAR(191) NOT NULL, area_km2 NUMERIC(15, 2) DEFAULT NULL, gdp NUMERIC(18, 2) DEFAULT NULL, population INT DEFAULT NULL, size VARCHAR(50) DEFAULT NULL, annual_revenue NUMERIC(18, 2) DEFAULT NULL, capital_city_id INT DEFAULT NULL, region_id INT NOT NULL, UNIQUE INDEX UNIQ_31C2774DB7405B21 (uf), INDEX IDX_31C2774DCA02080B (capital_city_id), INDEX IDX_31C2774D98260155 (region_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE tag (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(191) NOT NULL, UNIQUE INDEX UNIQ_389B7835E237E06 (name), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE thematic_areas (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(150) NOT NULL, description VARCHAR(255) DEFAULT NULL, parent_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_D8E363915E237E06 (name), INDEX IDX_D8E36391727ACA70 (parent_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, full_name VARCHAR(191) NOT NULL, username VARCHAR(191) NOT NULL, email VARCHAR(191) NOT NULL, password VARCHAR(191) NOT NULL, roles JSON NOT NULL, UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE addresses ADD CONSTRAINT FK_6FCA75168BAC62AF FOREIGN KEY (city_id) REFERENCES cities (id)');
        $this->addSql('ALTER TABLE cities ADD CONSTRAINT FK_D95DB16BC4BB49CF FOREIGN KEY (microregion_id) REFERENCES microregions (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE cities ADD CONSTRAINT FK_D95DB16B5D83CC1 FOREIGN KEY (state_id) REFERENCES states (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C4B89032C FOREIGN KEY (post_id) REFERENCES post (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CF675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE mesoregions ADD CONSTRAINT FK_38E74E6D5D83CC1 FOREIGN KEY (state_id) REFERENCES states (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE microregions ADD CONSTRAINT FK_34F4438ECF59994E FOREIGN KEY (mesoregion_id) REFERENCES mesoregions (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE organization_addresses ADD CONSTRAINT FK_57FDAF1032C8A3DE FOREIGN KEY (organization_id) REFERENCES organizations (id)');
        $this->addSql('ALTER TABLE organization_addresses ADD CONSTRAINT FK_57FDAF10F5B7AF75 FOREIGN KEY (address_id) REFERENCES addresses (id)');
        $this->addSql('ALTER TABLE organization_addresses ADD CONSTRAINT FK_57FDAF109EA97B0B FOREIGN KEY (address_type_id) REFERENCES address_types (id)');
        $this->addSql('ALTER TABLE organization_contact_interactions ADD CONSTRAINT FK_C3C27699EFAB7A5 FOREIGN KEY (organization_contact_id) REFERENCES organization_contacts (id)');
        $this->addSql('ALTER TABLE organization_contact_interactions ADD CONSTRAINT FK_C3C27699AFAC9F12 FOREIGN KEY (interaction_status_id) REFERENCES interaction_statuses (id)');
        $this->addSql('ALTER TABLE organization_contact_interactions ADD CONSTRAINT FK_C3C276992E65C292 FOREIGN KEY (performed_by_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE organization_contacts ADD CONSTRAINT FK_DC58D7D132C8A3DE FOREIGN KEY (organization_id) REFERENCES organizations (id)');
        $this->addSql('ALTER TABLE organization_contacts ADD CONSTRAINT FK_DC58D7D15F63AD12 FOREIGN KEY (contact_type_id) REFERENCES contact_types (id)');
        $this->addSql('ALTER TABLE organization_contacts ADD CONSTRAINT FK_DC58D7D16BF700BD FOREIGN KEY (status_id) REFERENCES contact_statuses (id)');
        $this->addSql('ALTER TABLE organization_contacts ADD CONSTRAINT FK_DC58D7D160B4C972 FOREIGN KEY (issue_type_id) REFERENCES contact_issue_types (id)');
        $this->addSql('ALTER TABLE organization_coverages ADD CONSTRAINT FK_F90CFB2E32C8A3DE FOREIGN KEY (organization_id) REFERENCES organizations (id)');
        $this->addSql('ALTER TABLE organization_coverages ADD CONSTRAINT FK_F90CFB2E8BAC62AF FOREIGN KEY (city_id) REFERENCES cities (id)');
        $this->addSql('ALTER TABLE organization_coverages ADD CONSTRAINT FK_F90CFB2E5D83CC1 FOREIGN KEY (state_id) REFERENCES states (id)');
        $this->addSql('ALTER TABLE organization_coverages ADD CONSTRAINT FK_F90CFB2E98260155 FOREIGN KEY (region_id) REFERENCES regions (id)');
        $this->addSql('ALTER TABLE organization_coverages ADD CONSTRAINT FK_F90CFB2E43375A16 FOREIGN KEY (coverage_type_id) REFERENCES coverage_types (id)');
        $this->addSql('ALTER TABLE organization_thematic_areas ADD CONSTRAINT FK_28F1802232C8A3DE FOREIGN KEY (organization_id) REFERENCES organizations (id)');
        $this->addSql('ALTER TABLE organization_thematic_areas ADD CONSTRAINT FK_28F18022328F8B8E FOREIGN KEY (thematic_area_id) REFERENCES thematic_areas (id)');
        $this->addSql('ALTER TABLE organizations ADD CONSTRAINT FK_427C1C7F727ACA70 FOREIGN KEY (parent_id) REFERENCES organizations (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE organizations ADD CONSTRAINT FK_427C1C7F89E04D0 FOREIGN KEY (organization_type_id) REFERENCES organization_types (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE person_addresses ADD CONSTRAINT FK_D7E396BC217BBB47 FOREIGN KEY (person_id) REFERENCES persons (id)');
        $this->addSql('ALTER TABLE person_addresses ADD CONSTRAINT FK_D7E396BCF5B7AF75 FOREIGN KEY (address_id) REFERENCES addresses (id)');
        $this->addSql('ALTER TABLE person_addresses ADD CONSTRAINT FK_D7E396BC9EA97B0B FOREIGN KEY (address_type_id) REFERENCES address_types (id)');
        $this->addSql('ALTER TABLE person_contact_interactions ADD CONSTRAINT FK_9B7AEBDDD11A465F FOREIGN KEY (person_contact_id) REFERENCES person_contacts (id)');
        $this->addSql('ALTER TABLE person_contact_interactions ADD CONSTRAINT FK_9B7AEBDDAFAC9F12 FOREIGN KEY (interaction_status_id) REFERENCES interaction_statuses (id)');
        $this->addSql('ALTER TABLE person_contact_interactions ADD CONSTRAINT FK_9B7AEBDD2E65C292 FOREIGN KEY (performed_by_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE person_contacts ADD CONSTRAINT FK_A706B044217BBB47 FOREIGN KEY (person_id) REFERENCES persons (id)');
        $this->addSql('ALTER TABLE person_contacts ADD CONSTRAINT FK_A706B0445F63AD12 FOREIGN KEY (contact_type_id) REFERENCES contact_types (id)');
        $this->addSql('ALTER TABLE person_contacts ADD CONSTRAINT FK_A706B0446BF700BD FOREIGN KEY (status_id) REFERENCES contact_statuses (id)');
        $this->addSql('ALTER TABLE person_contacts ADD CONSTRAINT FK_A706B04460B4C972 FOREIGN KEY (issue_type_id) REFERENCES contact_issue_types (id)');
        $this->addSql('ALTER TABLE person_coverages ADD CONSTRAINT FK_7912C282217BBB47 FOREIGN KEY (person_id) REFERENCES persons (id)');
        $this->addSql('ALTER TABLE person_coverages ADD CONSTRAINT FK_7912C2828BAC62AF FOREIGN KEY (city_id) REFERENCES cities (id)');
        $this->addSql('ALTER TABLE person_coverages ADD CONSTRAINT FK_7912C2825D83CC1 FOREIGN KEY (state_id) REFERENCES states (id)');
        $this->addSql('ALTER TABLE person_coverages ADD CONSTRAINT FK_7912C28298260155 FOREIGN KEY (region_id) REFERENCES regions (id)');
        $this->addSql('ALTER TABLE person_coverages ADD CONSTRAINT FK_7912C28243375A16 FOREIGN KEY (coverage_type_id) REFERENCES coverage_types (id)');
        $this->addSql('ALTER TABLE person_organization_roles ADD CONSTRAINT FK_E8D00FA7F34C6345 FOREIGN KEY (person_organization_id) REFERENCES person_organizations (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE person_organization_roles ADD CONSTRAINT FK_E8D00FA7D60322AC FOREIGN KEY (role_id) REFERENCES roles (id)');
        $this->addSql('ALTER TABLE person_organizations ADD CONSTRAINT FK_4AF22FB2217BBB47 FOREIGN KEY (person_id) REFERENCES persons (id)');
        $this->addSql('ALTER TABLE person_organizations ADD CONSTRAINT FK_4AF22FB232C8A3DE FOREIGN KEY (organization_id) REFERENCES organizations (id)');
        $this->addSql('ALTER TABLE person_thematic_areas ADD CONSTRAINT FK_3D3E53AF217BBB47 FOREIGN KEY (person_id) REFERENCES persons (id)');
        $this->addSql('ALTER TABLE person_thematic_areas ADD CONSTRAINT FK_3D3E53AF328F8B8E FOREIGN KEY (thematic_area_id) REFERENCES thematic_areas (id)');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8DF675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE post_tag ADD CONSTRAINT FK_5ACE3AF04B89032C FOREIGN KEY (post_id) REFERENCES post (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE post_tag ADD CONSTRAINT FK_5ACE3AF0BAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE regions ADD CONSTRAINT FK_A26779F3F92F3E70 FOREIGN KEY (country_id) REFERENCES countries (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE states ADD CONSTRAINT FK_31C2774DCA02080B FOREIGN KEY (capital_city_id) REFERENCES cities (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE states ADD CONSTRAINT FK_31C2774D98260155 FOREIGN KEY (region_id) REFERENCES regions (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE thematic_areas ADD CONSTRAINT FK_D8E36391727ACA70 FOREIGN KEY (parent_id) REFERENCES thematic_areas (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE addresses DROP FOREIGN KEY FK_6FCA75168BAC62AF');
        $this->addSql('ALTER TABLE cities DROP FOREIGN KEY FK_D95DB16BC4BB49CF');
        $this->addSql('ALTER TABLE cities DROP FOREIGN KEY FK_D95DB16B5D83CC1');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C4B89032C');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CF675F31B');
        $this->addSql('ALTER TABLE mesoregions DROP FOREIGN KEY FK_38E74E6D5D83CC1');
        $this->addSql('ALTER TABLE microregions DROP FOREIGN KEY FK_34F4438ECF59994E');
        $this->addSql('ALTER TABLE organization_addresses DROP FOREIGN KEY FK_57FDAF1032C8A3DE');
        $this->addSql('ALTER TABLE organization_addresses DROP FOREIGN KEY FK_57FDAF10F5B7AF75');
        $this->addSql('ALTER TABLE organization_addresses DROP FOREIGN KEY FK_57FDAF109EA97B0B');
        $this->addSql('ALTER TABLE organization_contact_interactions DROP FOREIGN KEY FK_C3C27699EFAB7A5');
        $this->addSql('ALTER TABLE organization_contact_interactions DROP FOREIGN KEY FK_C3C27699AFAC9F12');
        $this->addSql('ALTER TABLE organization_contact_interactions DROP FOREIGN KEY FK_C3C276992E65C292');
        $this->addSql('ALTER TABLE organization_contacts DROP FOREIGN KEY FK_DC58D7D132C8A3DE');
        $this->addSql('ALTER TABLE organization_contacts DROP FOREIGN KEY FK_DC58D7D15F63AD12');
        $this->addSql('ALTER TABLE organization_contacts DROP FOREIGN KEY FK_DC58D7D16BF700BD');
        $this->addSql('ALTER TABLE organization_contacts DROP FOREIGN KEY FK_DC58D7D160B4C972');
        $this->addSql('ALTER TABLE organization_coverages DROP FOREIGN KEY FK_F90CFB2E32C8A3DE');
        $this->addSql('ALTER TABLE organization_coverages DROP FOREIGN KEY FK_F90CFB2E8BAC62AF');
        $this->addSql('ALTER TABLE organization_coverages DROP FOREIGN KEY FK_F90CFB2E5D83CC1');
        $this->addSql('ALTER TABLE organization_coverages DROP FOREIGN KEY FK_F90CFB2E98260155');
        $this->addSql('ALTER TABLE organization_coverages DROP FOREIGN KEY FK_F90CFB2E43375A16');
        $this->addSql('ALTER TABLE organization_thematic_areas DROP FOREIGN KEY FK_28F1802232C8A3DE');
        $this->addSql('ALTER TABLE organization_thematic_areas DROP FOREIGN KEY FK_28F18022328F8B8E');
        $this->addSql('ALTER TABLE organizations DROP FOREIGN KEY FK_427C1C7F727ACA70');
        $this->addSql('ALTER TABLE organizations DROP FOREIGN KEY FK_427C1C7F89E04D0');
        $this->addSql('ALTER TABLE person_addresses DROP FOREIGN KEY FK_D7E396BC217BBB47');
        $this->addSql('ALTER TABLE person_addresses DROP FOREIGN KEY FK_D7E396BCF5B7AF75');
        $this->addSql('ALTER TABLE person_addresses DROP FOREIGN KEY FK_D7E396BC9EA97B0B');
        $this->addSql('ALTER TABLE person_contact_interactions DROP FOREIGN KEY FK_9B7AEBDDD11A465F');
        $this->addSql('ALTER TABLE person_contact_interactions DROP FOREIGN KEY FK_9B7AEBDDAFAC9F12');
        $this->addSql('ALTER TABLE person_contact_interactions DROP FOREIGN KEY FK_9B7AEBDD2E65C292');
        $this->addSql('ALTER TABLE person_contacts DROP FOREIGN KEY FK_A706B044217BBB47');
        $this->addSql('ALTER TABLE person_contacts DROP FOREIGN KEY FK_A706B0445F63AD12');
        $this->addSql('ALTER TABLE person_contacts DROP FOREIGN KEY FK_A706B0446BF700BD');
        $this->addSql('ALTER TABLE person_contacts DROP FOREIGN KEY FK_A706B04460B4C972');
        $this->addSql('ALTER TABLE person_coverages DROP FOREIGN KEY FK_7912C282217BBB47');
        $this->addSql('ALTER TABLE person_coverages DROP FOREIGN KEY FK_7912C2828BAC62AF');
        $this->addSql('ALTER TABLE person_coverages DROP FOREIGN KEY FK_7912C2825D83CC1');
        $this->addSql('ALTER TABLE person_coverages DROP FOREIGN KEY FK_7912C28298260155');
        $this->addSql('ALTER TABLE person_coverages DROP FOREIGN KEY FK_7912C28243375A16');
        $this->addSql('ALTER TABLE person_organization_roles DROP FOREIGN KEY FK_E8D00FA7F34C6345');
        $this->addSql('ALTER TABLE person_organization_roles DROP FOREIGN KEY FK_E8D00FA7D60322AC');
        $this->addSql('ALTER TABLE person_organizations DROP FOREIGN KEY FK_4AF22FB2217BBB47');
        $this->addSql('ALTER TABLE person_organizations DROP FOREIGN KEY FK_4AF22FB232C8A3DE');
        $this->addSql('ALTER TABLE person_thematic_areas DROP FOREIGN KEY FK_3D3E53AF217BBB47');
        $this->addSql('ALTER TABLE person_thematic_areas DROP FOREIGN KEY FK_3D3E53AF328F8B8E');
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8DF675F31B');
        $this->addSql('ALTER TABLE post_tag DROP FOREIGN KEY FK_5ACE3AF04B89032C');
        $this->addSql('ALTER TABLE post_tag DROP FOREIGN KEY FK_5ACE3AF0BAD26311');
        $this->addSql('ALTER TABLE regions DROP FOREIGN KEY FK_A26779F3F92F3E70');
        $this->addSql('ALTER TABLE states DROP FOREIGN KEY FK_31C2774DCA02080B');
        $this->addSql('ALTER TABLE states DROP FOREIGN KEY FK_31C2774D98260155');
        $this->addSql('ALTER TABLE thematic_areas DROP FOREIGN KEY FK_D8E36391727ACA70');
        $this->addSql('DROP TABLE address_types');
        $this->addSql('DROP TABLE addresses');
        $this->addSql('DROP TABLE cities');
        $this->addSql('DROP TABLE comment');
        $this->addSql('DROP TABLE contact_issue_types');
        $this->addSql('DROP TABLE contact_statuses');
        $this->addSql('DROP TABLE contact_types');
        $this->addSql('DROP TABLE countries');
        $this->addSql('DROP TABLE coverage_types');
        $this->addSql('DROP TABLE interaction_statuses');
        $this->addSql('DROP TABLE mesoregions');
        $this->addSql('DROP TABLE microregions');
        $this->addSql('DROP TABLE organization_addresses');
        $this->addSql('DROP TABLE organization_contact_interactions');
        $this->addSql('DROP TABLE organization_contacts');
        $this->addSql('DROP TABLE organization_coverages');
        $this->addSql('DROP TABLE organization_thematic_areas');
        $this->addSql('DROP TABLE organization_types');
        $this->addSql('DROP TABLE organizations');
        $this->addSql('DROP TABLE person_addresses');
        $this->addSql('DROP TABLE person_contact_interactions');
        $this->addSql('DROP TABLE person_contacts');
        $this->addSql('DROP TABLE person_coverages');
        $this->addSql('DROP TABLE person_organization_roles');
        $this->addSql('DROP TABLE person_organizations');
        $this->addSql('DROP TABLE person_thematic_areas');
        $this->addSql('DROP TABLE persons');
        $this->addSql('DROP TABLE post');
        $this->addSql('DROP TABLE post_tag');
        $this->addSql('DROP TABLE regions');
        $this->addSql('DROP TABLE roles');
        $this->addSql('DROP TABLE states');
        $this->addSql('DROP TABLE tag');
        $this->addSql('DROP TABLE thematic_areas');
        $this->addSql('DROP TABLE user');
    }
}
