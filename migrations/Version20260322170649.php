<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260322170649 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cities ADD CONSTRAINT FK_D95DB16BC4BB49CF FOREIGN KEY (microregion_id) REFERENCES microregions (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE cities ADD CONSTRAINT FK_D95DB16B5D83CC1 FOREIGN KEY (state_id) REFERENCES states (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C4B89032C FOREIGN KEY (post_id) REFERENCES post (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CF675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE mesoregions ADD CONSTRAINT FK_38E74E6D5D83CC1 FOREIGN KEY (state_id) REFERENCES states (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE microregions ADD CONSTRAINT FK_34F4438ECF59994E FOREIGN KEY (mesoregion_id) REFERENCES mesoregions (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8DF675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE post_tag ADD CONSTRAINT FK_5ACE3AF04B89032C FOREIGN KEY (post_id) REFERENCES post (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE post_tag ADD CONSTRAINT FK_5ACE3AF0BAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE regions ADD CONSTRAINT FK_A26779F3F92F3E70 FOREIGN KEY (country_id) REFERENCES countries (id) ON DELETE CASCADE');
        $this->addSql('DROP INDEX IDX_31C2774DF92F3E70 ON states');
        $this->addSql('ALTER TABLE states DROP country_id');
        $this->addSql('ALTER TABLE states ADD CONSTRAINT FK_31C2774DCA02080B FOREIGN KEY (capital_city_id) REFERENCES cities (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE states ADD CONSTRAINT FK_31C2774D98260155 FOREIGN KEY (region_id) REFERENCES regions (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cities DROP FOREIGN KEY FK_D95DB16BC4BB49CF');
        $this->addSql('ALTER TABLE cities DROP FOREIGN KEY FK_D95DB16B5D83CC1');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C4B89032C');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CF675F31B');
        $this->addSql('ALTER TABLE mesoregions DROP FOREIGN KEY FK_38E74E6D5D83CC1');
        $this->addSql('ALTER TABLE microregions DROP FOREIGN KEY FK_34F4438ECF59994E');
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8DF675F31B');
        $this->addSql('ALTER TABLE post_tag DROP FOREIGN KEY FK_5ACE3AF04B89032C');
        $this->addSql('ALTER TABLE post_tag DROP FOREIGN KEY FK_5ACE3AF0BAD26311');
        $this->addSql('ALTER TABLE regions DROP FOREIGN KEY FK_A26779F3F92F3E70');
        $this->addSql('ALTER TABLE states DROP FOREIGN KEY FK_31C2774DCA02080B');
        $this->addSql('ALTER TABLE states DROP FOREIGN KEY FK_31C2774D98260155');
        $this->addSql('ALTER TABLE states ADD country_id INT NOT NULL');
        $this->addSql('CREATE INDEX IDX_31C2774DF92F3E70 ON states (country_id)');
    }
}
