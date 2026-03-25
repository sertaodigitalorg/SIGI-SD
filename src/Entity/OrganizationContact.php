<?php

namespace App\Entity;

use App\Repository\OrganizationContactRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrganizationContactRepository::class)]
#[ORM\Table(name: 'organization_contacts')]
class OrganizationContact
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Organization::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Organization $organization = null;

    #[ORM\ManyToOne(targetEntity: ContactType::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?ContactType $contactType = null;

    #[ORM\Column(length: 191)]
    private ?string $value = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $label = null;

    #[ORM\Column(options: ['default' => false])]
    private bool $isPrimary = false;

    #[ORM\Column(options: ['default' => false])]
    private bool $isPublic = false;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $notes = null;

    #[ORM\ManyToOne(targetEntity: ContactStatus::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?ContactStatus $status = null;

    #[ORM\ManyToOne(targetEntity: ContactIssueType::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?ContactIssueType $issueType = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $deactivatedAt = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $deactivationReason = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrganization(): ?Organization
    {
        return $this->organization;
    }

    public function setOrganization(?Organization $organization): static
    {
        $this->organization = $organization;

        return $this;
    }

    public function getContactType(): ?ContactType
    {
        return $this->contactType;
    }

    public function setContactType(?ContactType $contactType): static
    {
        $this->contactType = $contactType;

        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): static
    {
        $this->value = $value;

        return $this;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(?string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function isPrimary(): bool
    {
        return $this->isPrimary;
    }

    public function setIsPrimary(bool $isPrimary): static
    {
        $this->isPrimary = $isPrimary;

        return $this;
    }

    public function isPublic(): bool
    {
        return $this->isPublic;
    }

    public function setIsPublic(bool $isPublic): static
    {
        $this->isPublic = $isPublic;

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

    public function getStatus(): ?ContactStatus
    {
        return $this->status;
    }

    public function setStatus(?ContactStatus $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getIssueType(): ?ContactIssueType
    {
        return $this->issueType;
    }

    public function setIssueType(?ContactIssueType $issueType): static
    {
        $this->issueType = $issueType;

        return $this;
    }

    public function getDeactivatedAt(): ?\DateTimeImmutable
    {
        return $this->deactivatedAt;
    }

    public function setDeactivatedAt(?\DateTimeImmutable $deactivatedAt): static
    {
        $this->deactivatedAt = $deactivatedAt;

        return $this;
    }

    public function getDeactivationReason(): ?string
    {
        return $this->deactivationReason;
    }

    public function setDeactivationReason(?string $deactivationReason): static
    {
        $this->deactivationReason = $deactivationReason;

        return $this;
    }
}