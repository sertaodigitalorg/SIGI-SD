<?php

namespace App\Entity;

use App\Repository\OrganizationContactInteractionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrganizationContactInteractionRepository::class)]
#[ORM\Table(name: 'organization_contact_interactions')]
class OrganizationContactInteraction
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: OrganizationContact::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?OrganizationContact $organizationContact = null;

    #[ORM\ManyToOne(targetEntity: InteractionStatus::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?InteractionStatus $interactionStatus = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $contactedAt = null;

    #[ORM\Column(length: 191, nullable: true)]
    private ?string $subject = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $message = null;

    #[ORM\Column(options: ['default' => false])]
    private bool $responseReceived = false;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $responseText = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $nextContactAt = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $notes = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?User $performedBy = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrganizationContact(): ?OrganizationContact
    {
        return $this->organizationContact;
    }

    public function setOrganizationContact(?OrganizationContact $organizationContact): static
    {
        $this->organizationContact = $organizationContact;

        return $this;
    }

    public function getInteractionStatus(): ?InteractionStatus
    {
        return $this->interactionStatus;
    }

    public function setInteractionStatus(?InteractionStatus $interactionStatus): static
    {
        $this->interactionStatus = $interactionStatus;

        return $this;
    }

    public function getContactedAt(): ?\DateTimeImmutable
    {
        return $this->contactedAt;
    }

    public function setContactedAt(\DateTimeImmutable $contactedAt): static
    {
        $this->contactedAt = $contactedAt;

        return $this;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(?string $subject): static
    {
        $this->subject = $subject;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): static
    {
        $this->message = $message;

        return $this;
    }

    public function isResponseReceived(): bool
    {
        return $this->responseReceived;
    }

    public function setResponseReceived(bool $responseReceived): static
    {
        $this->responseReceived = $responseReceived;

        return $this;
    }

    public function getResponseText(): ?string
    {
        return $this->responseText;
    }

    public function setResponseText(?string $responseText): static
    {
        $this->responseText = $responseText;

        return $this;
    }

    public function getNextContactAt(): ?\DateTimeImmutable
    {
        return $this->nextContactAt;
    }

    public function setNextContactAt(?\DateTimeImmutable $nextContactAt): static
    {
        $this->nextContactAt = $nextContactAt;

        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): static
    {
        $this->notes = $notes;

        return $this;
    }

    public function getPerformedBy(): ?User
    {
        return $this->performedBy;
    }

    public function setPerformedBy(?User $performedBy): static
    {
        $this->performedBy = $performedBy;

        return $this;
    }

    public function __toString(): string
    {
        if ($this->subject) {
            return $this->subject;
        }

        if ($this->contactedAt) {
            return $this->contactedAt->format('d/m/Y H:i');
        }

        return 'Interação';
    }
}