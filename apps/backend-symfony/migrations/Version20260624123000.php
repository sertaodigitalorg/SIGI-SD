<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260624123000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add Chatwoot account and inbox identifiers to integration accounts.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE chatwoot_accounts ADD account_id VARCHAR(64) DEFAULT NULL');
        $this->addSql('ALTER TABLE chatwoot_accounts ADD inbox_id VARCHAR(64) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE chatwoot_accounts DROP account_id');
        $this->addSql('ALTER TABLE chatwoot_accounts DROP inbox_id');
    }
}
