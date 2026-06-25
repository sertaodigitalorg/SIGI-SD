<?php

namespace App\Entity;

use App\Repository\PersonRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PersonRepository::class)]
#[ORM\Table(name: 'persons')]
class Person
{
    public const TYPE_UNKNOWN = 'unknown';
    public const TYPE_INDIVIDUAL = 'individual';
    public const TYPE_ORGANIZATION = 'organization';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 191)]
    #[Assert\NotBlank]
    private ?string $fullName = null;

    #[ORM\Column(type: 'string', length: 14, nullable: true, unique: true)]
    #[Assert\Regex(
        pattern: '/^\d{3}\.\d{3}\.\d{3}\-\d{2}$/',
        message: 'CPF inválido',
    )]
    private ?string $cpf = null;

    #[ORM\Column(length: 32, options: ['default' => self::TYPE_UNKNOWN])]
    private string $personType = self::TYPE_UNKNOWN;

    #[ORM\Column(length: 32, nullable: true)]
    private ?string $documentType = null;

    #[ORM\Column(length: 32, nullable: true)]
    private ?string $documentNumber = null;

    #[ORM\Column(length: 191, nullable: true)]
    private ?string $primaryEmail = null;

    #[ORM\Column(length: 64, nullable: true)]
    private ?string $primaryPhone = null;

    #[ORM\Column(length: 191, nullable: true, unique: true)]
    private ?string $chatwootContactId = null;

    #[ORM\Column(length: 64, nullable: true)]
    private ?string $source = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function setFullName(string $fullName): static
    {
        $this->fullName = $fullName;

        return $this;
    }

    public function getCpf(): ?string
    {
        return $this->cpf;
    }

    public function setCpf(?string $cpf): static
    {
        // Normalize empty string to null to ensure optional behavior
        if ($cpf !== null && trim($cpf) === '') {
            $cpf = null;
        }

        $this->cpf = $cpf;

        return $this;
    }

    public function getPersonType(): string
    {
        return $this->personType;
    }

    public function setPersonType(?string $personType): static
    {
        $this->personType = in_array($personType, [self::TYPE_UNKNOWN, self::TYPE_INDIVIDUAL, self::TYPE_ORGANIZATION], true)
            ? $personType
            : self::TYPE_UNKNOWN;

        return $this;
    }

    public function getDocumentType(): ?string
    {
        return $this->documentType;
    }

    public function setDocumentType(?string $documentType): static
    {
        $this->documentType = $this->normalizeNullableString($documentType, 32);

        return $this;
    }

    public function getDocumentNumber(): ?string
    {
        return $this->documentNumber;
    }

    public function setDocumentNumber(?string $documentNumber): static
    {
        $this->documentNumber = $this->normalizeNullableString($documentNumber, 32);

        return $this;
    }

    public function getPrimaryEmail(): ?string
    {
        return $this->primaryEmail;
    }

    public function setPrimaryEmail(?string $primaryEmail): static
    {
        $primaryEmail = $this->normalizeNullableString($primaryEmail);
        $this->primaryEmail = null === $primaryEmail ? null : mb_strtolower($primaryEmail);

        return $this;
    }

    public function getPrimaryPhone(): ?string
    {
        return $this->primaryPhone;
    }

    public function setPrimaryPhone(?string $primaryPhone): static
    {
        $this->primaryPhone = $this->normalizeNullableString($primaryPhone, 64);

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

    public function getSource(): ?string
    {
        return $this->source;
    }

    public function setSource(?string $source): static
    {
        $this->source = $this->normalizeNullableString($source, 64);

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

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function __toString(): string
    {
        return $this->fullName ?? '';
    }

    private function normalizeNullableString(?string $value, int $length = 191): ?string
    {
        if (null === $value) {
            return null;
        }

        $value = trim($value);

        return '' === $value ? null : mb_substr($value, 0, $length);
    }
}
