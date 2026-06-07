<?php

namespace App\Entity\Integration\Chatwoot;

use App\Repository\Integration\Chatwoot\ChatwootMessageEventRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ChatwootMessageEventRepository::class)]
#[ORM\Table(name: 'chatwoot_message_events')]
#[ORM\Index(columns: ['processing_status'], name: 'idx_chatwoot_events_status')]
#[ORM\Index(columns: ['event_type'], name: 'idx_chatwoot_events_type')]
#[ORM\Index(columns: ['created_at'], name: 'idx_chatwoot_events_created_at')]
#[ORM\UniqueConstraint(
    name: 'uniq_chatwoot_event_idempotency',
    columns: ['chatwoot_account_id', 'event_type', 'external_conversation_id', 'external_message_id', 'payload_hash'],
)]
class ChatwootMessageEvent
{
    public const STATUS_RECEIVED = 'received';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_PROCESSED = 'processed';
    public const STATUS_IGNORED = 'ignored';
    public const STATUS_FAILED = 'failed';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: ChatwootAccount::class, inversedBy: 'messageEvents')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?ChatwootAccount $chatwootAccount = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $eventType = null;

    #[ORM\Column(length: 191, nullable: true)]
    private ?string $externalConversationId = null;

    #[ORM\Column(length: 191, nullable: true)]
    private ?string $externalMessageId = null;

    #[ORM\Column(length: 64)]
    private ?string $payloadHash = null;

    /**
     * @var array<string, mixed>
     */
    #[ORM\Column(type: Types::JSON)]
    private array $rawPayload = [];

    #[ORM\Column(length: 32)]
    private string $processingStatus = self::STATUS_RECEIVED;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $processedAt = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $errorMessage = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getChatwootAccount(): ?ChatwootAccount
    {
        return $this->chatwootAccount;
    }

    public function setChatwootAccount(?ChatwootAccount $chatwootAccount): static
    {
        $this->chatwootAccount = $chatwootAccount;

        return $this;
    }

    public function getEventType(): ?string
    {
        return $this->eventType;
    }

    public function setEventType(?string $eventType): static
    {
        $this->eventType = $this->normalizeNullableString($eventType);

        return $this;
    }

    public function getExternalConversationId(): ?string
    {
        return $this->externalConversationId;
    }

    public function setExternalConversationId(?string $externalConversationId): static
    {
        $this->externalConversationId = $this->normalizeNullableString($externalConversationId);

        return $this;
    }

    public function getExternalMessageId(): ?string
    {
        return $this->externalMessageId;
    }

    public function setExternalMessageId(?string $externalMessageId): static
    {
        $this->externalMessageId = $this->normalizeNullableString($externalMessageId);

        return $this;
    }

    public function getPayloadHash(): ?string
    {
        return $this->payloadHash;
    }

    public function setPayloadHash(string $payloadHash): static
    {
        $this->payloadHash = $payloadHash;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function getRawPayload(): array
    {
        return $this->rawPayload;
    }

    /**
     * @param array<string, mixed> $rawPayload
     */
    public function setRawPayload(array $rawPayload): static
    {
        $this->rawPayload = $rawPayload;

        return $this;
    }

    public function getProcessingStatus(): string
    {
        return $this->processingStatus;
    }

    public function setProcessingStatus(string $processingStatus): static
    {
        $this->processingStatus = $processingStatus;

        return $this;
    }

    public function getProcessedAt(): ?\DateTimeImmutable
    {
        return $this->processedAt;
    }

    public function setProcessedAt(?\DateTimeImmutable $processedAt): static
    {
        $this->processedAt = $processedAt;

        return $this;
    }

    public function getErrorMessage(): ?string
    {
        return $this->errorMessage;
    }

    public function setErrorMessage(?string $errorMessage): static
    {
        $this->errorMessage = $this->normalizeNullableString($errorMessage);

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function markProcessing(): static
    {
        $this->processingStatus = self::STATUS_PROCESSING;
        $this->processedAt = null;
        $this->errorMessage = null;

        return $this;
    }

    public function markProcessed(): static
    {
        $this->processingStatus = self::STATUS_PROCESSED;
        $this->processedAt = new \DateTimeImmutable();
        $this->errorMessage = null;

        return $this;
    }

    public function markIgnored(?string $reason = null): static
    {
        $this->processingStatus = self::STATUS_IGNORED;
        $this->processedAt = new \DateTimeImmutable();
        $this->errorMessage = $this->normalizeNullableString($reason);

        return $this;
    }

    public function markFailed(string $errorMessage): static
    {
        $this->processingStatus = self::STATUS_FAILED;
        $this->processedAt = new \DateTimeImmutable();
        $this->errorMessage = $errorMessage;

        return $this;
    }

    /**
     * @return array<int, string>
     */
    public static function getAvailableStatuses(): array
    {
        return [
            self::STATUS_RECEIVED,
            self::STATUS_PROCESSING,
            self::STATUS_PROCESSED,
            self::STATUS_IGNORED,
            self::STATUS_FAILED,
        ];
    }

    private function normalizeNullableString(?string $value): ?string
    {
        if (null === $value) {
            return null;
        }

        $value = trim($value);

        return '' === $value ? null : $value;
    }
}
