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
    public const DEFAULT_PUBLIC_MESSAGE_TEMPLATE = "Olá, recebemos sua solicitação.\n\nSeu protocolo de atendimento é: {protocol}.\n\nNossa equipe dará continuidade ao atendimento por este canal.";

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 16)]
    private string $sequenceScope = self::SCOPE_DAILY;

    #[ORM\Column(options: ['default' => true])]
    private bool $sendPublicProtocolMessage = true;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $publicProtocolMessageTemplate = self::DEFAULT_PUBLIC_MESSAGE_TEMPLATE;

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

    public function shouldSendPublicProtocolMessage(): bool
    {
        return $this->sendPublicProtocolMessage;
    }

    public function setSendPublicProtocolMessage(bool $sendPublicProtocolMessage): static
    {
        $this->sendPublicProtocolMessage = $sendPublicProtocolMessage;

        return $this;
    }

    public function getPublicProtocolMessageTemplate(): string
    {
        return $this->publicProtocolMessageTemplate ?: self::DEFAULT_PUBLIC_MESSAGE_TEMPLATE;
    }

    public function setPublicProtocolMessageTemplate(?string $publicProtocolMessageTemplate): static
    {
        $publicProtocolMessageTemplate = null === $publicProtocolMessageTemplate ? null : trim($publicProtocolMessageTemplate);
        $this->publicProtocolMessageTemplate = '' === $publicProtocolMessageTemplate ? self::DEFAULT_PUBLIC_MESSAGE_TEMPLATE : $publicProtocolMessageTemplate;

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
