<?php

namespace App\Entity;

use App\Repository\AttendanceProtocolRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AttendanceProtocolRepository::class)]
#[ORM\Table(name: 'attendance_protocols')]
#[ORM\UniqueConstraint(name: 'uniq_attendance_protocol_code', columns: ['protocol_code'])]
#[ORM\UniqueConstraint(name: 'uniq_attendance_chatwoot_conversation', columns: ['chatwoot_conversation_id'])]
#[ORM\Index(columns: ['status'], name: 'idx_attendance_protocol_status')]
#[ORM\Index(columns: ['source_channel'], name: 'idx_attendance_protocol_channel')]
#[ORM\Index(columns: ['responsible_team'], name: 'idx_attendance_protocol_team')]
#[ORM\Index(columns: ['responsible_agent'], name: 'idx_attendance_protocol_agent')]
#[ORM\Index(columns: ['priority'], name: 'idx_attendance_protocol_priority')]
#[ORM\Index(columns: ['created_at'], name: 'idx_attendance_protocol_created_at')]
class AttendanceProtocol
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 32)]
    private ?string $protocolCode = null;

    #[ORM\Column(length: 16)]
    private string $sequenceScope = ProtocolSettings::SCOPE_DAILY;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $sequenceDate = null;

    #[ORM\Column]
    private int $sequenceNumber = 0;

    #[ORM\Column(length: 191)]
    private ?string $chatwootConversationId = null;

    #[ORM\Column(length: 191, nullable: true)]
    private ?string $chatwootContactId = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $contactName = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $contactHandle = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $sourceChannel = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $subject = null;

    #[ORM\Column(length: 64, nullable: true)]
    private ?string $status = null;

    /**
     * @var array<int, string>
     */
    #[ORM\Column(type: Types::JSON)]
    private array $labels = [];

    #[ORM\Column(length: 191, nullable: true)]
    private ?string $responsibleTeam = null;

    #[ORM\Column(length: 191, nullable: true)]
    private ?string $responsibleAgent = null;

    #[ORM\Column(length: 64, nullable: true)]
    private ?string $priority = null;

    #[ORM\Column]
    private bool $protocolNoteSent = false;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $protocolNoteSentAt = null;

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
        $this->sequenceDate = $now;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProtocolCode(): ?string
    {
        return $this->protocolCode;
    }

    public function setProtocolCode(string $protocolCode): static
    {
        $this->protocolCode = $this->normalizeString($protocolCode);

        return $this;
    }

    public function getSequenceScope(): string
    {
        return $this->sequenceScope;
    }

    public function setSequenceScope(string $sequenceScope): static
    {
        $this->sequenceScope = $sequenceScope;

        return $this;
    }

    public function getSequenceDate(): ?\DateTimeImmutable
    {
        return $this->sequenceDate;
    }

    public function setSequenceDate(\DateTimeImmutable $sequenceDate): static
    {
        $this->sequenceDate = $sequenceDate;

        return $this;
    }

    public function getSequenceNumber(): int
    {
        return $this->sequenceNumber;
    }

    public function setSequenceNumber(int $sequenceNumber): static
    {
        $this->sequenceNumber = $sequenceNumber;

        return $this;
    }

    public function getChatwootConversationId(): ?string
    {
        return $this->chatwootConversationId;
    }

    public function setChatwootConversationId(string $chatwootConversationId): static
    {
        $this->chatwootConversationId = $this->normalizeString($chatwootConversationId);

        return $this;
    }

    public function getChatwootContactId(): ?string
    {
        return $this->chatwootContactId;
    }

    public function setChatwootContactId(?string $chatwootContactId): static
    {
        $this->chatwootContactId = $this->normalizeNullableString($chatwootContactId);

        return $this;
    }

    public function getContactName(): ?string
    {
        return $this->contactName;
    }

    public function setContactName(?string $contactName): static
    {
        $this->contactName = $this->normalizeNullableString($contactName);

        return $this;
    }

    public function getContactHandle(): ?string
    {
        return $this->contactHandle;
    }

    public function setContactHandle(?string $contactHandle): static
    {
        $this->contactHandle = $this->normalizeNullableString($contactHandle);

        return $this;
    }

    public function getSourceChannel(): ?string
    {
        return $this->sourceChannel;
    }

    public function setSourceChannel(?string $sourceChannel): static
    {
        $this->sourceChannel = $this->normalizeNullableString($sourceChannel);

        return $this;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(?string $subject): static
    {
        $this->subject = $this->normalizeNullableString($subject);

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): static
    {
        $this->status = $this->normalizeNullableString($status);

        return $this;
    }

    /**
     * @return array<int, string>
     */
    public function getLabels(): array
    {
        return $this->labels;
    }

    /**
     * @param array<int, string> $labels
     */
    public function setLabels(array $labels): static
    {
        $this->labels = array_values(array_unique(array_filter(array_map(
            static fn (mixed $label): ?string => is_scalar($label) && '' !== trim((string) $label) ? trim((string) $label) : null,
            $labels
        ))));

        return $this;
    }

    public function getResponsibleTeam(): ?string
    {
        return $this->responsibleTeam;
    }

    public function setResponsibleTeam(?string $responsibleTeam): static
    {
        $this->responsibleTeam = $this->normalizeNullableString($responsibleTeam);

        return $this;
    }

    public function getResponsibleAgent(): ?string
    {
        return $this->responsibleAgent;
    }

    public function setResponsibleAgent(?string $responsibleAgent): static
    {
        $this->responsibleAgent = $this->normalizeNullableString($responsibleAgent);

        return $this;
    }

    public function getPriority(): ?string
    {
        return $this->priority;
    }

    public function setPriority(?string $priority): static
    {
        $this->priority = $this->normalizeNullableString($priority);

        return $this;
    }

    public function isProtocolNoteSent(): bool
    {
        return $this->protocolNoteSent;
    }

    public function markProtocolNoteSent(): static
    {
        $this->protocolNoteSent = true;
        $this->protocolNoteSentAt = new \DateTimeImmutable();

        return $this;
    }

    public function getProtocolNoteSentAt(): ?\DateTimeImmutable
    {
        return $this->protocolNoteSentAt;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

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

    public function applyChatwootData(
        ?string $chatwootContactId,
        ?string $contactName,
        ?string $contactHandle,
        ?string $sourceChannel,
        ?string $subject,
        ?string $status,
        array $labels,
        ?string $responsibleTeam,
        ?string $responsibleAgent,
        ?string $priority,
        ?\DateTimeImmutable $createdAt,
        ?\DateTimeImmutable $updatedAt,
        ?\DateTimeImmutable $closedAt,
    ): static {
        $this
            ->setChatwootContactId($chatwootContactId)
            ->setContactName($contactName)
            ->setContactHandle($contactHandle)
            ->setSourceChannel($sourceChannel)
            ->setSubject($subject)
            ->setStatus($status)
            ->setLabels($labels)
            ->setResponsibleTeam($responsibleTeam)
            ->setResponsibleAgent($responsibleAgent)
            ->setPriority($priority);

        if (null !== $createdAt) {
            $this->setCreatedAt($createdAt);
        }

        $this->setUpdatedAt($updatedAt ?? new \DateTimeImmutable());
        $this->setClosedAt($closedAt);

        return $this;
    }

    private function normalizeString(string $value): string
    {
        return trim($value);
    }

    private function normalizeNullableString(?string $value): ?string
    {
        if (null === $value) {
            return null;
        }

        $value = trim($value);

        return '' === $value ? null : mb_substr($value, 0, 255);
    }
}
