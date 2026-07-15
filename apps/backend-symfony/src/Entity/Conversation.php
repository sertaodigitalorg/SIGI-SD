<?php

namespace App\Entity;

use App\Repository\ConversationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ConversationRepository::class)]
#[ORM\Table(name: 'conversations')]
#[ORM\UniqueConstraint(name: 'uniq_conversation_chatwoot', columns: ['chatwoot_account_id', 'chatwoot_conversation_id'])]
#[ORM\Index(columns: ['channel'], name: 'idx_conversation_channel')]
#[ORM\Index(columns: ['current_controller'], name: 'idx_conversation_controller')]
#[ORM\Index(columns: ['automation_enabled'], name: 'idx_conversation_automation')]
#[ORM\Index(columns: ['assigned_agent_id'], name: 'idx_conversation_agent')]
#[ORM\Index(columns: ['assigned_team_id'], name: 'idx_conversation_team')]
class Conversation
{
    public const CONTROLLER_AI = 'ai';
    public const CONTROLLER_HUMAN = 'human';
    public const CONTROLLER_SYSTEM = 'system';
    public const CONTROLLER_WAITING = 'waiting';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private int $chatwootAccountId = 0;

    #[ORM\Column(nullable: true)]
    private ?int $chatwootInboxId = null;

    #[ORM\Column(length: 191)]
    private ?string $chatwootConversationId = null;

    #[ORM\Column(length: 64, nullable: true)]
    private ?string $channel = null;

    #[ORM\Column(length: 191, nullable: true)]
    private ?string $contactId = null;

    #[ORM\Column(length: 16)]
    private string $currentController = self::CONTROLLER_WAITING;

    #[ORM\Column(options: ['default' => true])]
    private bool $automationEnabled = true;

    #[ORM\Column(length: 191, nullable: true)]
    private ?string $assignedAgentId = null;

    #[ORM\Column(length: 191, nullable: true)]
    private ?string $assignedTeamId = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $lastIncomingMessageAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $lastOutgoingMessageAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

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

    public function getChatwootAccountId(): int
    {
        return $this->chatwootAccountId;
    }

    public function setChatwootAccountId(int $chatwootAccountId): static
    {
        $this->chatwootAccountId = $chatwootAccountId;

        return $this;
    }

    public function getChatwootInboxId(): ?int
    {
        return $this->chatwootInboxId;
    }

    public function setChatwootInboxId(?int $chatwootInboxId): static
    {
        $this->chatwootInboxId = $chatwootInboxId;

        return $this;
    }

    public function getChatwootConversationId(): ?string
    {
        return $this->chatwootConversationId;
    }

    public function setChatwootConversationId(string $chatwootConversationId): static
    {
        $this->chatwootConversationId = $this->normalizeString($chatwootConversationId, 191);

        return $this;
    }

    public function getChannel(): ?string
    {
        return $this->channel;
    }

    public function setChannel(?string $channel): static
    {
        $this->channel = $this->normalizeNullableString($channel, 64);

        return $this;
    }

    public function getContactId(): ?string
    {
        return $this->contactId;
    }

    public function setContactId(?string $contactId): static
    {
        $this->contactId = $this->normalizeNullableString($contactId, 191);

        return $this;
    }

    public function getCurrentController(): string
    {
        return $this->currentController;
    }

    public function setCurrentController(string $currentController): static
    {
        $this->currentController = in_array($currentController, self::getAvailableControllers(), true)
            ? $currentController
            : self::CONTROLLER_WAITING;

        return $this;
    }

    public function isAutomationEnabled(): bool
    {
        return $this->automationEnabled;
    }

    public function setAutomationEnabled(bool $automationEnabled): static
    {
        $this->automationEnabled = $automationEnabled;

        return $this;
    }

    public function getAssignedAgentId(): ?string
    {
        return $this->assignedAgentId;
    }

    public function setAssignedAgentId(?string $assignedAgentId): static
    {
        $this->assignedAgentId = $this->normalizeNullableString($assignedAgentId, 191);

        return $this;
    }

    public function getAssignedTeamId(): ?string
    {
        return $this->assignedTeamId;
    }

    public function setAssignedTeamId(?string $assignedTeamId): static
    {
        $this->assignedTeamId = $this->normalizeNullableString($assignedTeamId, 191);

        return $this;
    }

    public function getLastIncomingMessageAt(): ?\DateTimeImmutable
    {
        return $this->lastIncomingMessageAt;
    }

    public function setLastIncomingMessageAt(?\DateTimeImmutable $lastIncomingMessageAt): static
    {
        $this->lastIncomingMessageAt = $lastIncomingMessageAt;

        return $this;
    }

    public function getLastOutgoingMessageAt(): ?\DateTimeImmutable
    {
        return $this->lastOutgoingMessageAt;
    }

    public function setLastOutgoingMessageAt(?\DateTimeImmutable $lastOutgoingMessageAt): static
    {
        $this->lastOutgoingMessageAt = $lastOutgoingMessageAt;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function touch(): static
    {
        $this->updatedAt = new \DateTimeImmutable();

        return $this;
    }

    public function getClosedAt(): ?\DateTimeImmutable
    {
        return $this->closedAt;
    }

    public function setClosedAt(?\DateTimeImmutable $closedAt): static
    {
        $this->closedAt = $closedAt;

        return $this;
    }

    /**
     * @return array<int, string>
     */
    public static function getAvailableControllers(): array
    {
        return [self::CONTROLLER_AI, self::CONTROLLER_HUMAN, self::CONTROLLER_SYSTEM, self::CONTROLLER_WAITING];
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