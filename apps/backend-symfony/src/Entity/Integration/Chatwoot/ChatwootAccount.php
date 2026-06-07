<?php

namespace App\Entity\Integration\Chatwoot;

use App\Repository\Integration\Chatwoot\ChatwootAccountRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ChatwootAccountRepository::class)]
#[ORM\Table(name: 'chatwoot_accounts')]
class ChatwootAccount implements \Stringable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 191)]
    #[Assert\NotBlank(message: 'Informe o nome da integracao.')]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Informe a URL base do Chatwoot.')]
    #[Assert\Url(message: 'Informe uma URL valida para o Chatwoot.')]
    private ?string $baseUrl = null;

    #[ORM\Column(length: 512)]
    #[Assert\NotBlank(message: 'Informe o token de API do Chatwoot.')]
    private ?string $apiToken = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Informe o secret do webhook.')]
    private ?string $webhookSecret = null;

    #[ORM\Column]
    private bool $isActive = true;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * @var Collection<int, ChatwootMessageEvent>
     */
    #[ORM\OneToMany(mappedBy: 'chatwootAccount', targetEntity: ChatwootMessageEvent::class, orphanRemoval: true)]
    #[ORM\OrderBy(['createdAt' => 'DESC'])]
    private Collection $messageEvents;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->messageEvents = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = trim($name);

        return $this;
    }

    public function getBaseUrl(): ?string
    {
        return $this->baseUrl;
    }

    public function setBaseUrl(string $baseUrl): static
    {
        $this->baseUrl = rtrim(trim($baseUrl), '/');

        return $this;
    }

    public function getApiToken(): ?string
    {
        return $this->apiToken;
    }

    public function setApiToken(string $apiToken): static
    {
        $this->apiToken = trim($apiToken);

        return $this;
    }

    public function getWebhookSecret(): ?string
    {
        return $this->webhookSecret;
    }

    public function setWebhookSecret(string $webhookSecret): static
    {
        $this->webhookSecret = trim($webhookSecret);

        return $this;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): static
    {
        $this->isActive = $isActive;

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

    public function touch(): static
    {
        $this->updatedAt = new \DateTimeImmutable();

        return $this;
    }

    /**
     * @return Collection<int, ChatwootMessageEvent>
     */
    public function getMessageEvents(): Collection
    {
        return $this->messageEvents;
    }

    public function addMessageEvent(ChatwootMessageEvent $messageEvent): static
    {
        if (!$this->messageEvents->contains($messageEvent)) {
            $this->messageEvents->add($messageEvent);
            $messageEvent->setChatwootAccount($this);
        }

        return $this;
    }

    public function removeMessageEvent(ChatwootMessageEvent $messageEvent): static
    {
        if ($this->messageEvents->removeElement($messageEvent) && $messageEvent->getChatwootAccount() === $this) {
            $messageEvent->setChatwootAccount(null);
        }

        return $this;
    }

    public function __toString(): string
    {
        return (string) $this->name;
    }
}
