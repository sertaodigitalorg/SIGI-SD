<?php

namespace App\Entity;

use App\Repository\ProtocolSettingsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProtocolSettingsRepository::class)]
#[ORM\Table(name: 'protocol_settings')]
class ProtocolSettings
{
    public const SCOPE_DAILY = 'daily';
    public const SCOPE_GLOBAL = 'global';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 16)]
    private string $sequenceScope = self::SCOPE_DAILY;

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

    public function getSequenceScope(): string
    {
        return $this->sequenceScope;
    }

    public function setSequenceScope(string $sequenceScope): static
    {
        $this->sequenceScope = in_array($sequenceScope, self::getAvailableScopes(), true)
            ? $sequenceScope
            : self::SCOPE_DAILY;

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

    /**
     * @return array<int, string>
     */
    public static function getAvailableScopes(): array
    {
        return [self::SCOPE_DAILY, self::SCOPE_GLOBAL];
    }
}
