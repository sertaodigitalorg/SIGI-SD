<?php

namespace App\Entity;

use App\Repository\ConversationMessageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ConversationMessageRepository::class)]
#[ORM\Table(name: 'conversation_messages')]
#[ORM\UniqueConstraint(name: 'uniq_conversation_chatwoot_message', columns: ['conversation_id', 'chatwoot_message_id'])]
#[ORM\Index(columns: ['direction'], name: 'idx_conversation_message_direction')]
#[ORM\Index(columns: ['sender_type'], name: 'idx_conversation_message_sender_type')]
#[ORM\Index(columns: ['message_type'], name: 'idx_conversation_message_type')]
#[ORM\Index(columns: ['created_at'], name: 'idx_conversation_message_created_at')]
class ConversationMessage
{
    public const DIRECTION_INCOMING = 'incoming';
    public const DIRECTION_OUTGOING = 'outgoing';
    public const SENDER_CITIZEN = 'citizen';
    public const SENDER_AI = 'ai';
    public const SENDER_HUMAN_AGENT = 'human_agent';
    public const SENDER_SYSTEM = 'system';
    public const SENDER_EXTERNAL_SYSTEM = 'external_system';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Conversation::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Conversation $conversation = null;

    #[ORM\ManyToOne(targetEntity: ServiceRequest::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?ServiceRequest $serviceRequest = null;

    #[ORM\Column(length: 191, nullable: true)]
    private ?string $chatwootMessageId = null;

    #[ORM\Column(length: 16)]
    private string $direction = self::DIRECTION_INCOMING;

    #[ORM\Column(length: 32)]
    private string $senderType = self::SENDER_CITIZEN;

    #[ORM\Column(length: 191, nullable: true)]
    private ?string $senderId = null;

    #[ORM\Column(length: 64, nullable: true)]
    private ?string $messageType = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $content = null;

    /**
     * @var array<int, array<string, mixed>>
     */
    #[ORM\Column(type: Types::JSON)]
    private array $attachments = [];

    #[ORM\Column(length: 64, nullable: true)]
    private ?string $deliveryStatus = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $sentAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $receivedAt = null;

    /**
     * @var array<string, mixed>
     */
    #[ORM\Column(type: Types::JSON)]
    private array $metadata = [];

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

    public function getConversation(): ?Conversation
    {
        return $this->conversation;
    }

    public function setConversation(Conversation $conversation): static
    {
        $this->conversation = $conversation;

        return $this;
    }

    public function getServiceRequest(): ?ServiceRequest
    {
        return $this->serviceRequest;
    }

    public function setServiceRequest(?ServiceRequest $serviceRequest): static
    {
        $this->serviceRequest = $serviceRequest;

        return $this;
    }

    public function getChatwootMessageId(): ?string
    {
        return $this->chatwootMessageId;
    }

    public function setChatwootMessageId(?string $chatwootMessageId): static
    {
        $this->chatwootMessageId = $this->normalizeNullableString($chatwootMessageId, 191);

        return $this;
    }

    public function getDirection(): string
    {
        return $this->direction;
    }

    public function setDirection(string $direction): static
    {
        $this->direction = $this->normalizeString($direction, 16);

        return $this;
    }

    public function getSenderType(): string
    {
        return $this->senderType;
    }

    public function setSenderType(string $senderType): static
    {
        $this->senderType = $this->normalizeString($senderType, 32);

        return $this;
    }

    public function getSenderId(): ?string
    {
        return $this->senderId;
    }

    public function setSenderId(?string $senderId): static
    {
        $this->senderId = $this->normalizeNullableString($senderId, 191);

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): static
    {
        $this->content = null === $content ? null : trim($content);

        return $this;
    }

    public function setMessageType(?string $messageType): static
    {
        $this->messageType = $this->normalizeNullableString($messageType, 64);

        return $this;
    }

    public function setDeliveryStatus(?string $deliveryStatus): static
    {
        $this->deliveryStatus = $this->normalizeNullableString($deliveryStatus, 64);

        return $this;
    }

    public function setSentAt(?\DateTimeImmutable $sentAt): static
    {
        $this->sentAt = $sentAt;

        return $this;
    }

    public function setReceivedAt(?\DateTimeImmutable $receivedAt): static
    {
        $this->receivedAt = $receivedAt;

        return $this;
    }
    /**
     * @return array<int, array<string, mixed>>
     */
    public function getAttachments(): array
    {
        return $this->attachments;
    }

    /**
     * @param array<int, array<string, mixed>> $attachments
     */
    public function setAttachments(array $attachments): static
    {
        $this->attachments = array_values($attachments);

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function getMetadata(): array
    {
        return $this->metadata;
    }

    /**
     * @param array<string, mixed> $metadata
     */
    public function setMetadata(array $metadata): static
    {
        $this->metadata = $metadata;

        return $this;
    }

    private function normalizeString(string $value, int $maxLength): string
    {
        return mb_substr(trim($value), 0, $maxLength);
    }

    private function normalizeNullableString(?string $value, int $maxLength): ?string
    {
        if (null === $value) {
            return null;
        }

        $value = trim($value);

        return '' === $value ? null : mb_substr($value, 0, $maxLength);
    }
}