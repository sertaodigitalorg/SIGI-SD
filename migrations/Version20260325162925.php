<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260325162925 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE contact_types (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, description VARCHAR(255) DEFAULT NULL, category VARCHAR(50) DEFAULT NULL, UNIQUE INDEX UNIQ_741A993F5E237E06 (name), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE organization_contacts (id INT AUTO_INCREMENT NOT NULL, value VARCHAR(191) NOT NULL, label VARCHAR(100) DEFAULT NULL, is_primary TINYINT DEFAULT 0 NOT NULL, is_public TINYINT DEFAULT 0 NOT NULL, notes LONGTEXT DEFAULT NULL, organization_id INT NOT NULL, contact_type_id INT NOT NULL, INDEX IDX_DC58D7D132C8A3DE (organization_id), INDEX IDX_DC58D7D15F63AD12 (contact_type_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE person_contacts (id INT AUTO_INCREMENT NOT NULL, value VARCHAR(191) NOT NULL, label VARCHAR(100) DEFAULT NULL, is_primary TINYINT DEFAULT 0 NOT NULL, is_public TINYINT DEFAULT 0 NOT NULL, notes LONGTEXT DEFAULT NULL, person_id INT NOT NULL, contact_type_id INT NOT NULL, INDEX IDX_A706B044217BBB47 (person_id), INDEX IDX_A706B0445F63AD12 (contact_type_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE organization_contacts ADD CONSTRAINT FK_DC58D7D132C8A3DE FOREIGN KEY (organization_id) REFERENCES organizations (id)');
        $this->addSql('ALTER TABLE organization_contacts ADD CONSTRAINT FK_DC58D7D15F63AD12 FOREIGN KEY (contact_type_id) REFERENCES contact_types (id)');
        $this->addSql('ALTER TABLE person_contacts ADD CONSTRAINT FK_A706B044217BBB47 FOREIGN KEY (person_id) REFERENCES persons (id)');
        $this->addSql('ALTER TABLE person_contacts ADD CONSTRAINT FK_A706B0445F63AD12 FOREIGN KEY (contact_type_id) REFERENCES contact_types (id)');
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
        $this->addSql('ALTER TABLE organization_coverages ADD CONSTRAINT FK_F90CFB2E32C8A3DE FOREIGN KEY (organization_id) REFERENCES organizations (id)');
        $this->addSql('ALTER TABLE organization_coverages ADD CONSTRAINT FK_F90CFB2E8BAC62AF FOREIGN KEY (city_id) REFERENCES cities (id)');
        $this->addSql('ALTER TABLE organization_coverages ADD CONSTRAINT FK_F90CFB2E5D83CC1 FOREIGN KEY (state_id) REFERENCES states (id)');
        $this->addSql('ALTER TABLE organization_coverages ADD CONSTRAINT FK_F90CFB2E98260155 FOREIGN KEY (region_id) REFERENCES regions (id)');
        $this->addSql('ALTER TABLE organization_coverages ADD CONSTRAINT FK_F90CFB2E43375A16 FOREIGN KEY (coverage_type_id) REFERENCES coverage_types (id)');
        $this->addSql('ALTER TABLE organization_thematic_areas ADD CONSTRAINT FK_28F1802232C8A3DE FOREIGN KEY (organization_id) REFERENCES organizations (id)');
        $this->addSql('ALTER TABLE organization_thematic_areas ADD CONSTRAINT FK_28F18022328F8B8E FOREIGN KEY (thematic_area_id) REFERENCES thematic_areas (id)');
        $this->addSql('ALTER TABLE organizations DROP email, DROP phone');
        $this->addSql('ALTER TABLE person_addresses ADD CONSTRAINT FK_D7E396BC217BBB47 FOREIGN KEY (person_id) REFERENCES persons (id)');
        $this->addSql('ALTER TABLE person_addresses ADD CONSTRAINT FK_D7E396BCF5B7AF75 FOREIGN KEY (address_id) REFERENCES addresses (id)');
        $this->addSql('ALTER TABLE person_addresses ADD CONSTRAINT FK_D7E396BC9EA97B0B FOREIGN KEY (address_type_id) REFERENCES address_types (id)');
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
        $this->addSql('ALTER TABLE persons DROP email, DROP phone');
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
        $this->addSql('ALTER TABLE organization_contacts DROP FOREIGN KEY FK_DC58D7D132C8A3DE');
        $this->addSql('ALTER TABLE organization_contacts DROP FOREIGN KEY FK_DC58D7D15F63AD12');
        $this->addSql('ALTER TABLE person_contacts DROP FOREIGN KEY FK_A706B044217BBB47');
        $this->addSql('ALTER TABLE person_contacts DROP FOREIGN KEY FK_A706B0445F63AD12');
        $this->addSql('DROP TABLE contact_types');
        $this->addSql('DROP TABLE organization_contacts');
        $this->addSql('DROP TABLE person_contacts');
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
        $this->addSql('ALTER TABLE organization_coverages DROP FOREIGN KEY FK_F90CFB2E32C8A3DE');
        $this->addSql('ALTER TABLE organization_coverages DROP FOREIGN KEY FK_F90CFB2E8BAC62AF');
        $this->addSql('ALTER TABLE organization_coverages DROP FOREIGN KEY FK_F90CFB2E5D83CC1');
        $this->addSql('ALTER TABLE organization_coverages DROP FOREIGN KEY FK_F90CFB2E98260155');
        $this->addSql('ALTER TABLE organization_coverages DROP FOREIGN KEY FK_F90CFB2E43375A16');
        $this->addSql('ALTER TABLE organization_thematic_areas DROP FOREIGN KEY FK_28F1802232C8A3DE');
        $this->addSql('ALTER TABLE organization_thematic_areas DROP FOREIGN KEY FK_28F18022328F8B8E');
        $this->addSql('ALTER TABLE organizations ADD email VARCHAR(255) DEFAULT NULL, ADD phone VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE person_addresses DROP FOREIGN KEY FK_D7E396BC217BBB47');
        $this->addSql('ALTER TABLE person_addresses DROP FOREIGN KEY FK_D7E396BCF5B7AF75');
        $this->addSql('ALTER TABLE person_addresses DROP FOREIGN KEY FK_D7E396BC9EA97B0B');
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
        $this->addSql('ALTER TABLE persons ADD email VARCHAR(191) DEFAULT NULL, ADD phone VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8DF675F31B');
        $this->addSql('ALTER TABLE post_tag DROP FOREIGN KEY FK_5ACE3AF04B89032C');
        $this->addSql('ALTER TABLE post_tag DROP FOREIGN KEY FK_5ACE3AF0BAD26311');
        $this->addSql('ALTER TABLE regions DROP FOREIGN KEY FK_A26779F3F92F3E70');
        $this->addSql('ALTER TABLE states DROP FOREIGN KEY FK_31C2774DCA02080B');
        $this->addSql('ALTER TABLE states DROP FOREIGN KEY FK_31C2774D98260155');
        $this->addSql('ALTER TABLE thematic_areas DROP FOREIGN KEY FK_D8E36391727ACA70');
    }
}
