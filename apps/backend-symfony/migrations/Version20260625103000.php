<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260625103000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add unified person contact data and customer protocol message tracking.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("ALTER TABLE persons ADD person_type VARCHAR(32) DEFAULT 'unknown' NOT NULL");
        $this->addSql('ALTER TABLE persons ADD document_type VARCHAR(32) DEFAULT NULL');
        $this->addSql('ALTER TABLE persons ADD document_number VARCHAR(32) DEFAULT NULL');
        $this->addSql('ALTER TABLE persons ADD primary_email VARCHAR(191) DEFAULT NULL');
        $this->addSql('ALTER TABLE persons ADD primary_phone VARCHAR(64) DEFAULT NULL');
        $this->addSql('ALTER TABLE persons ADD chatwoot_contact_id VARCHAR(191) DEFAULT NULL');
        $this->addSql('ALTER TABLE persons ADD source VARCHAR(64) DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX uniq_person_chatwoot_contact ON persons (chatwoot_contact_id)');
        $this->addSql('CREATE INDEX idx_person_primary_email ON persons (primary_email)');
        $this->addSql('CREATE INDEX idx_person_primary_phone ON persons (primary_phone)');
        $this->addSql('CREATE INDEX idx_person_document ON persons (document_type, document_number)');
        $this->addSql('UPDATE persons SET document_type = \'CPF\', document_number = cpf WHERE cpf IS NOT NULL AND cpf <> \'\'');

        $this->addSql('ALTER TABLE attendance_protocols ADD customer_protocol_message_sent BOOLEAN DEFAULT false NOT NULL');
        $this->addSql('ALTER TABLE attendance_protocols ADD customer_protocol_message_sent_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE attendance_protocols ADD person_id INT DEFAULT NULL');
        $this->addSql('CREATE INDEX idx_attendance_protocol_person ON attendance_protocols (person_id)');
        $this->addSql('ALTER TABLE attendance_protocols ADD CONSTRAINT FK_ATTENDANCE_PROTOCOL_PERSON FOREIGN KEY (person_id) REFERENCES persons (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');

        $this->addSql('ALTER TABLE protocol_settings ADD send_public_protocol_message BOOLEAN DEFAULT true NOT NULL');
        $this->addSql('ALTER TABLE protocol_settings ADD public_protocol_message_template TEXT DEFAULT NULL');
        $this->addSql("UPDATE protocol_settings SET public_protocol_message_template = 'Olá, recebemos sua solicitação.' || E'\n\n' || 'Seu protocolo de atendimento é: {protocol}.' || E'\n\n' || 'Nossa equipe dará continuidade ao atendimento por este canal.' WHERE public_protocol_message_template IS NULL");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE attendance_protocols DROP CONSTRAINT FK_ATTENDANCE_PROTOCOL_PERSON');
        $this->addSql('DROP INDEX idx_attendance_protocol_person');
        $this->addSql('ALTER TABLE attendance_protocols DROP person_id');
        $this->addSql('ALTER TABLE attendance_protocols DROP customer_protocol_message_sent_at');
        $this->addSql('ALTER TABLE attendance_protocols DROP customer_protocol_message_sent');

        $this->addSql('ALTER TABLE protocol_settings DROP public_protocol_message_template');
        $this->addSql('ALTER TABLE protocol_settings DROP send_public_protocol_message');

        $this->addSql('DROP INDEX idx_person_document');
        $this->addSql('DROP INDEX idx_person_primary_phone');
        $this->addSql('DROP INDEX idx_person_primary_email');
        $this->addSql('DROP INDEX uniq_person_chatwoot_contact');
        $this->addSql('ALTER TABLE persons DROP source');
        $this->addSql('ALTER TABLE persons DROP chatwoot_contact_id');
        $this->addSql('ALTER TABLE persons DROP primary_phone');
        $this->addSql('ALTER TABLE persons DROP primary_email');
        $this->addSql('ALTER TABLE persons DROP document_number');
        $this->addSql('ALTER TABLE persons DROP document_type');
        $this->addSql('ALTER TABLE persons DROP person_type');
    }
}
