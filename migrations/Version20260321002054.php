<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260321002054 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE cities (id INT AUTO_INCREMENT NOT NULL, area_km2 NUMERIC(15, 2) DEFAULT NULL, gdp NUMERIC(18, 2) DEFAULT NULL, population INT DEFAULT NULL, annual_revenue NUMERIC(18, 2) DEFAULT NULL, tom_code VARCHAR(20) DEFAULT NULL, ibge_code VARCHAR(20) DEFAULT NULL, ibge_code7 VARCHAR(20) DEFAULT NULL, zip_code VARCHAR(10) DEFAULT NULL, tom_name VARCHAR(191) DEFAULT NULL, ibge_name VARCHAR(191) NOT NULL, size VARCHAR(50) DEFAULT NULL, is_capital TINYINT DEFAULT 0 NOT NULL, microregion_id INT DEFAULT NULL, state_id INT NOT NULL, INDEX IDX_D95DB16BC4BB49CF (microregion_id), INDEX IDX_D95DB16B5D83CC1 (state_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE mesoregions (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(191) NOT NULL, ibge_code VARCHAR(20) NOT NULL, municipalities_count INT DEFAULT NULL, state_id INT NOT NULL, UNIQUE INDEX UNIQ_38E74E6D7E3DDF8B (ibge_code), INDEX IDX_38E74E6D5D83CC1 (state_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE microregions (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(191) NOT NULL, ibge_code VARCHAR(20) NOT NULL, mesoregion_id INT NOT NULL, UNIQUE INDEX UNIQ_34F4438E7E3DDF8B (ibge_code), INDEX IDX_34F4438ECF59994E (mesoregion_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE states (id INT AUTO_INCREMENT NOT NULL, uf VARCHAR(2) NOT NULL, name VARCHAR(191) NOT NULL, area_km2 NUMERIC(15, 2) DEFAULT NULL, gdp NUMERIC(18, 2) DEFAULT NULL, population INT DEFAULT NULL, size VARCHAR(50) DEFAULT NULL, annual_revenue NUMERIC(18, 2) DEFAULT NULL, capital_city_id INT DEFAULT NULL, country_id INT NOT NULL, region_id INT NOT NULL, UNIQUE INDEX UNIQ_31C2774DB7405B21 (uf), INDEX IDX_31C2774DCA02080B (capital_city_id), INDEX IDX_31C2774DF92F3E70 (country_id), INDEX IDX_31C2774D98260155 (region_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE cities ADD CONSTRAINT FK_D95DB16BC4BB49CF FOREIGN KEY (microregion_id) REFERENCES microregions (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE cities ADD CONSTRAINT FK_D95DB16B5D83CC1 FOREIGN KEY (state_id) REFERENCES states (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE mesoregions ADD CONSTRAINT FK_38E74E6D5D83CC1 FOREIGN KEY (state_id) REFERENCES states (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE microregions ADD CONSTRAINT FK_34F4438ECF59994E FOREIGN KEY (mesoregion_id) REFERENCES mesoregions (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE states ADD CONSTRAINT FK_31C2774DCA02080B FOREIGN KEY (capital_city_id) REFERENCES cities (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE states ADD CONSTRAINT FK_31C2774DF92F3E70 FOREIGN KEY (country_id) REFERENCES countries (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE states ADD CONSTRAINT FK_31C2774D98260155 FOREIGN KEY (region_id) REFERENCES regions (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C4B89032C FOREIGN KEY (post_id) REFERENCES post (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CF675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8DF675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE post_tag ADD CONSTRAINT FK_5ACE3AF04B89032C FOREIGN KEY (post_id) REFERENCES post (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE post_tag ADD CONSTRAINT FK_5ACE3AF0BAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE regions ADD CONSTRAINT FK_A26779F3F92F3E70 FOREIGN KEY (country_id) REFERENCES countries (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cities DROP FOREIGN KEY FK_D95DB16BC4BB49CF');
        $this->addSql('ALTER TABLE cities DROP FOREIGN KEY FK_D95DB16B5D83CC1');
        $this->addSql('ALTER TABLE mesoregions DROP FOREIGN KEY FK_38E74E6D5D83CC1');
        $this->addSql('ALTER TABLE microregions DROP FOREIGN KEY FK_34F4438ECF59994E');
        $this->addSql('ALTER TABLE states DROP FOREIGN KEY FK_31C2774DCA02080B');
        $this->addSql('ALTER TABLE states DROP FOREIGN KEY FK_31C2774DF92F3E70');
        $this->addSql('ALTER TABLE states DROP FOREIGN KEY FK_31C2774D98260155');
        $this->addSql('DROP TABLE cities');
        $this->addSql('DROP TABLE mesoregions');
        $this->addSql('DROP TABLE microregions');
        $this->addSql('DROP TABLE states');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C4B89032C');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CF675F31B');
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8DF675F31B');
        $this->addSql('ALTER TABLE post_tag DROP FOREIGN KEY FK_5ACE3AF04B89032C');
        $this->addSql('ALTER TABLE post_tag DROP FOREIGN KEY FK_5ACE3AF0BAD26311');
        $this->addSql('ALTER TABLE regions DROP FOREIGN KEY FK_A26779F3F92F3E70');
    }
}
