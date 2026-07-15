<?php

namespace App\Entity;

use App\Repository\RequestEventRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RequestEventRepository::class)]
#[ORM\Table(name: 'request_events')]
#[ORM\Index(columns: ['event_type'], name: 'idx_request_event_type')]
#[ORM\Index(columns: ['transition_name'], name: 'idx_request_event_transition')]
#[ORM\Index(columns: ['actor_type'], name: 'idx_request_event_actor_type')]
#[ORM\Index(columns: ['created_at'], name: 'idx_request_event_created_at')]
class RequestEvent
{
    public const ACTOR_CITIZEN = 'citizen';
    public const ACTOR_AI = 'ai';
    public const ACTOR_HUMAN = 'human';
    public const ACTOR_SYSTEM = 'system';
    public const ACTOR_INTEGRATION = 'integration';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: ServiceRequest::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'CASCADE')]
    private ?ServiceRequest $serviceRequest = null;

    #[ORM\ManyToOne(targetEntity: Conversation::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'CASCADE')]
    private ?Conversation $conversation = null;

    #[ORM\Column(length: 100)]
    private ?string $eventType = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $transitionName = null;

    #[ORM\Column(length: 64, nullable: true)]
    private ?string $fromState = null;

    #[ORM\Column(length: 64, nullable: true)]
    private ?string $toState = null;

    #[ORM\Column(length: 32)]
    private string $actorType = self::ACTOR_SYSTEM;

    #[ORM\Column(length: 191, nullable: true)]
    private ?string $actorId = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $reason = null;

    #[ORM\Column(length: 64, nullable: true)]
    private ?string $result = null;

    /**
     * @var array<string, mixed>
     */
    #[ORM\Column(type: Types::JSON)]
    private array $metadata = [];

    #[ORM\Column(length: 64, nullable: true)]
    private ?string $previousEventHash = null;

    #[ORM\Column(length: 64)]
    private ?string $eventHash = null;

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

    public function setServiceRequest(?ServiceRequest $serviceRequest): static
    {
        $this->serviceRequest = $serviceRequest;

        return $this;
    }

    public function setConversation(?Conversation $conversation): static
    {
        $this->conversation = $conversation;

        return $this;
    }

    public function getEventType(): ?string
    {
        return $this->eventType;
    }

    public function setEventType(string $eventType): static
    {
        $this->eventType = $this->normalizeString($eventType, 100);

        return $this;
    }

    public function setTransitionName(?string $transitionName): static
    {
        $this->transitionName = $this->normalizeNullableString($transitionName, 100);

        return $this;
    }

    public function setStates(?string $fromState, ?string $toState): static
    {
        $this->fromState = $this->normalizeNullableString($fromState, 64);
        $this->toState = $this->normalizeNullableString($toState, 64);

        return $this;
    }

    public function getActorType(): string
    {
        return $this->actorType;
    }

    public function setActorType(string $actorType): static
    {
        $this->actorType = $this->normalizeString($actorType, 32);

        return $this;
    }

    public function setActorId(?string $actorId): static
    {
        $this->actorId = $this->normalizeNullableString($actorId, 191);

        return $this;
    }

    public function setReason(?string $reason): static
    {
        $this->reason = null === $reason ? null : trim($reason);

        return $this;
    }

    public function setResult(?string $result): static
    {
        $this->result = $this->normalizeNullableString($result, 64);

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

    public function getPreviousEventHash(): ?string
    {
        return $this->previousEventHash;
    }

    public function setPreviousEventHash(?string $previousEventHash): static
    {
        $this->previousEventHash = $this->normalizeNullableString($previousEventHash, 64);

        return $this;
    }

    public function getEventHash(): ?string
    {
        return $this->eventHash;
    }

    public function setEventHash(string $eventHash): static
    {
        $this->eventHash = $this->normalizeString($eventHash, 64);

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
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