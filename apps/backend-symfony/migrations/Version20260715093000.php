<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260715093000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Normalize conversation workflow schema defaults and Doctrine-managed index names.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE request_events ALTER metadata DROP DEFAULT');
        $this->addSql('ALTER INDEX idx_request_event_request RENAME TO IDX_18DE94B1D42F8111');
        $this->addSql('ALTER INDEX idx_request_event_conversation RENAME TO IDX_18DE94B19AC0396');

        $this->addSql('ALTER TABLE ai_execution_logs ALTER structured_result DROP DEFAULT');
        $this->addSql('ALTER TABLE ai_execution_logs ALTER knowledge_sources DROP DEFAULT');
        $this->addSql('ALTER INDEX idx_ai_execution_request RENAME TO IDX_C4CA1863D42F8111');
        $this->addSql('ALTER INDEX idx_ai_execution_conversation RENAME TO IDX_C4CA18639AC0396');

        $this->addSql('ALTER TABLE external_integration_logs ALTER request_metadata DROP DEFAULT');
        $this->addSql('ALTER TABLE external_integration_logs ALTER response_metadata DROP DEFAULT');
        $this->addSql('ALTER INDEX idx_external_integration_request RENAME TO IDX_DE592612D42F8111');
        $this->addSql('ALTER INDEX idx_external_integration_conversation RENAME TO IDX_DE5926129AC0396');

        $this->addSql('ALTER TABLE service_requests ALTER collected_data DROP DEFAULT');
        $this->addSql('ALTER TABLE service_requests ALTER context DROP DEFAULT');
        $this->addSql('ALTER INDEX idx_service_request_conversation RENAME TO IDX_82F38D6C9AC0396');
        $this->addSql('ALTER INDEX idx_service_request_legacy_protocol RENAME TO IDX_82F38D6CA1D1A026');

        $this->addSql('ALTER TABLE conversation_messages ALTER attachments DROP DEFAULT');
        $this->addSql('ALTER TABLE conversation_messages ALTER metadata DROP DEFAULT');
        $this->addSql('ALTER INDEX idx_conversation_message_conversation RENAME TO IDX_3B4CA1869AC0396');
        $this->addSql('ALTER INDEX idx_conversation_message_request RENAME TO IDX_3B4CA186D42F8111');
    }

    public function down(Schema $schema): void
    {
        $this->addSql("ALTER TABLE conversation_messages ALTER attachments SET DEFAULT '[]'::json");
        $this->addSql("ALTER TABLE conversation_messages ALTER metadata SET DEFAULT '{}'::json");
        $this->addSql('ALTER INDEX IDX_3B4CA1869AC0396 RENAME TO idx_conversation_message_conversation');
        $this->addSql('ALTER INDEX IDX_3B4CA186D42F8111 RENAME TO idx_conversation_message_request');

        $this->addSql("ALTER TABLE service_requests ALTER collected_data SET DEFAULT '{}'::json");
        $this->addSql("ALTER TABLE service_requests ALTER context SET DEFAULT '{}'::json");
        $this->addSql('ALTER INDEX IDX_82F38D6C9AC0396 RENAME TO idx_service_request_conversation');
        $this->addSql('ALTER INDEX IDX_82F38D6CA1D1A026 RENAME TO idx_service_request_legacy_protocol');

        $this->addSql("ALTER TABLE external_integration_logs ALTER request_metadata SET DEFAULT '{}'::json");
        $this->addSql("ALTER TABLE external_integration_logs ALTER response_metadata SET DEFAULT '{}'::json");
        $this->addSql('ALTER INDEX IDX_DE592612D42F8111 RENAME TO idx_external_integration_request');
        $this->addSql('ALTER INDEX IDX_DE5926129AC0396 RENAME TO idx_external_integration_conversation');

        $this->addSql("ALTER TABLE ai_execution_logs ALTER structured_result SET DEFAULT '{}'::json");
        $this->addSql("ALTER TABLE ai_execution_logs ALTER knowledge_sources SET DEFAULT '[]'::json");
        $this->addSql('ALTER INDEX IDX_C4CA1863D42F8111 RENAME TO idx_ai_execution_request');
        $this->addSql('ALTER INDEX IDX_C4CA18639AC0396 RENAME TO idx_ai_execution_conversation');

        $this->addSql("ALTER TABLE request_events ALTER metadata SET DEFAULT '{}'::json");
        $this->addSql('ALTER INDEX IDX_18DE94B1D42F8111 RENAME TO idx_request_event_request');
        $this->addSql('ALTER INDEX IDX_18DE94B19AC0396 RENAME TO idx_request_event_conversation');
    }
}
