<?php

namespace App\Entity;

use App\Repository\ServiceRequestRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ServiceRequestRepository::class)]
#[ORM\Table(name: 'service_requests')]
#[ORM\UniqueConstraint(name: 'uniq_service_request_protocol', columns: ['protocol'])]
#[ORM\Index(columns: ['workflow_key', 'current_state'], name: 'idx_service_request_workflow_state')]
#[ORM\Index(columns: ['status'], name: 'idx_service_request_status')]
#[ORM\Index(columns: ['service_type'], name: 'idx_service_request_service_type')]
#[ORM\Index(columns: ['intent'], name: 'idx_service_request_intent')]
#[ORM\Index(columns: ['assigned_agent_id'], name: 'idx_service_request_agent')]
#[ORM\Index(columns: ['assigned_team_id'], name: 'idx_service_request_team')]
class ServiceRequest
{
    public const STATUS_OPEN = 'open';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_CLOSED = 'closed';
    public const DEFAULT_WORKFLOW_KEY = 'sigi_service_request';
    public const DEFAULT_STATE = 'new';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 64)]
    private ?string $protocol = null;

    #[ORM\ManyToOne(targetEntity: Conversation::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Conversation $conversation = null;

    #[ORM\ManyToOne(targetEntity: AttendanceProtocol::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?AttendanceProtocol $legacyAttendanceProtocol = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $serviceType = null;

    #[ORM\Column(length: 100)]
    private string $workflowKey = self::DEFAULT_WORKFLOW_KEY;

    #[ORM\Column]
    private int $workflowVersion = 1;

    #[ORM\Column(length: 64)]
    private string $currentState = self::DEFAULT_STATE;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $currentStep = null;

    #[ORM\Column(length: 32)]
    private string $status = self::STATUS_OPEN;

    #[ORM\Column(length: 32, nullable: true)]
    private ?string $priority = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $sector = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $intent = null;

    #[ORM\Column(nullable: true)]
    private ?float $confidence = null;

    /**
     * @var array<string, mixed>
     */
    #[ORM\Column(type: Types::JSON)]
    private array $collectedData = [];

    /**
     * @var array<string, mixed>
     */
    #[ORM\Column(type: Types::JSON)]
    private array $context = [];

    #[ORM\Column(length: 191, nullable: true)]
    private ?string $assignedAgentId = null;

    #[ORM\Column(length: 191, nullable: true)]
    private ?string $assignedTeamId = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $completedAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $closedAt = null;

    public function __construct()
    {
        $now = new \DateTimeImmutable();
        $this->createdAt = $now;
        $this->updatedAt = $now;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProtocol(): ?string
    {
        return $this->protocol;
    }

    public function setProtocol(string $protocol): static
    {
        $this->protocol = $this->normalizeString($protocol, 64);

        return $this;
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

    public function getLegacyAttendanceProtocol(): ?AttendanceProtocol
    {
        return $this->legacyAttendanceProtocol;
    }

    public function setLegacyAttendanceProtocol(?AttendanceProtocol $legacyAttendanceProtocol): static
    {
        $this->legacyAttendanceProtocol = $legacyAttendanceProtocol;

        return $this;
    }

    public function getServiceType(): ?string
    {
        return $this->serviceType;
    }

    public function setServiceType(?string $serviceType): static
    {
        $this->serviceType = $this->normalizeNullableString($serviceType, 100);

        return $this;
    }

    public function getWorkflowKey(): string
    {
        return $this->workflowKey;
    }

    public function setWorkflowKey(string $workflowKey): static
    {
        $this->workflowKey = $this->normalizeString($workflowKey, 100);

        return $this;
    }

    public function getWorkflowVersion(): int
    {
        return $this->workflowVersion;
    }

    public function setWorkflowVersion(int $workflowVersion): static
    {
        $this->workflowVersion = max(1, $workflowVersion);

        return $this;
    }

    public function getCurrentState(): string
    {
        return $this->currentState;
    }

    public function setCurrentState(string $currentState): static
    {
        $this->currentState = $this->normalizeString($currentState, 64);

        return $this;
    }

    public function getCurrentStep(): ?string
    {
        return $this->currentStep;
    }

    public function setCurrentStep(?string $currentStep): static
    {
        $this->currentStep = $this->normalizeNullableString($currentStep, 100);

        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $this->normalizeString($status, 32);

        return $this;
    }

    public function getPriority(): ?string
    {
        return $this->priority;
    }

    public function setPriority(?string $priority): static
    {
        $this->priority = $this->normalizeNullableString($priority, 32);

        return $this;
    }

    public function getSector(): ?string
    {
        return $this->sector;
    }

    public function setSector(?string $sector): static
    {
        $this->sector = $this->normalizeNullableString($sector, 100);

        return $this;
    }

    public function getIntent(): ?string
    {
        return $this->intent;
    }

    public function setIntent(?string $intent): static
    {
        $this->intent = $this->normalizeNullableString($intent, 100);

        return $this;
    }

    public function getConfidence(): ?float
    {
        return $this->confidence;
    }

    public function setConfidence(?float $confidence): static
    {
        $this->confidence = null === $confidence ? null : max(0.0, min(1.0, $confidence));

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function getCollectedData(): array
    {
        return $this->collectedData;
    }

    /**
     * @param array<string, mixed> $collectedData
     */
    public function setCollectedData(array $collectedData): static
    {
        $this->collectedData = $collectedData;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function getContext(): array
    {
        return $this->context;
    }

    /**
     * @param array<string, mixed> $context
     */
    public function setContext(array $context): static
    {
        $this->context = $context;

        return $this;
    }

    public function setAssignedAgentId(?string $assignedAgentId): static
    {
        $this->assignedAgentId = $this->normalizeNullableString($assignedAgentId, 191);

        return $this;
    }

    public function setAssignedTeamId(?string $assignedTeamId): static
    {
        $this->assignedTeamId = $this->normalizeNullableString($assignedTeamId, 191);

        return $this;
    }

    public function markCompleted(?\DateTimeImmutable $completedAt = null): static
    {
        $this->status = self::STATUS_COMPLETED;
        $this->completedAt = $completedAt ?? new \DateTimeImmutable();
        $this->touch();

        return $this;
    }

    public function markClosed(?\DateTimeImmutable $closedAt = null): static
    {
        $this->status = self::STATUS_CLOSED;
        $this->closedAt = $closedAt ?? new \DateTimeImmutable();
        $this->touch();

        return $this;
    }
    public function touch(): static
    {
        $this->updatedAt = new \DateTimeImmutable();

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