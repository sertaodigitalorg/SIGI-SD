<?php

namespace App\Entity;

use App\Repository\ExternalIntegrationLogRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ExternalIntegrationLogRepository::class)]
#[ORM\Table(name: 'external_integration_logs')]
#[ORM\Index(columns: ['system_name'], name: 'idx_external_integration_system')]
#[ORM\Index(columns: ['operation'], name: 'idx_external_integration_operation')]
#[ORM\Index(columns: ['success'], name: 'idx_external_integration_success')]
#[ORM\Index(columns: ['created_at'], name: 'idx_external_integration_created_at')]
class ExternalIntegrationLog
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: ServiceRequest::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?ServiceRequest $serviceRequest = null;

    #[ORM\ManyToOne(targetEntity: Conversation::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Conversation $conversation = null;

    #[ORM\Column(length: 100)]
    private ?string $systemName = null;

    #[ORM\Column(length: 100)]
    private ?string $operation = null;

    #[ORM\Column(length: 191, nullable: true)]
    private ?string $requestReference = null;

    #[ORM\Column(length: 64, nullable: true)]
    private ?string $responseStatus = null;

    #[ORM\Column(nullable: true)]
    private ?int $durationMs = null;

    #[ORM\Column]
    private bool $success = false;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $errorCode = null;

    /**
     * @var array<string, mixed>
     */
    #[ORM\Column(type: Types::JSON)]
    private array $requestMetadata = [];

    /**
     * @var array<string, mixed>
     */
    #[ORM\Column(type: Types::JSON)]
    private array $responseMetadata = [];

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

    public function setSystemName(string $systemName): static
    {
        $this->systemName = $this->normalizeString($systemName, 100);

        return $this;
    }

    public function setOperation(string $operation): static
    {
        $this->operation = $this->normalizeString($operation, 100);

        return $this;
    }

    public function setRequestReference(?string $requestReference): static
    {
        $this->requestReference = $this->normalizeNullableString($requestReference, 191);

        return $this;
    }

    public function setResponseStatus(?string $responseStatus): static
    {
        $this->responseStatus = $this->normalizeNullableString($responseStatus, 64);

        return $this;
    }

    public function setDurationMs(?int $durationMs): static
    {
        $this->durationMs = null === $durationMs ? null : max(0, $durationMs);

        return $this;
    }

    public function setSuccess(bool $success): static
    {
        $this->success = $success;

        return $this;
    }

    public function setErrorCode(?string $errorCode): static
    {
        $this->errorCode = $this->normalizeNullableString($errorCode, 100);

        return $this;
    }

    /**
     * @param array<string, mixed> $requestMetadata
     */
    public function setRequestMetadata(array $requestMetadata): static
    {
        $this->requestMetadata = $requestMetadata;

        return $this;
    }

    /**
     * @param array<string, mixed> $responseMetadata
     */
    public function setResponseMetadata(array $responseMetadata): static
    {
        $this->responseMetadata = $responseMetadata;

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