<?php

namespace App\Entity;

use App\Repository\AiExecutionLogRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AiExecutionLogRepository::class)]
#[ORM\Table(name: 'ai_execution_logs')]
#[ORM\Index(columns: ['operation'], name: 'idx_ai_execution_operation')]
#[ORM\Index(columns: ['model'], name: 'idx_ai_execution_model')]
#[ORM\Index(columns: ['success'], name: 'idx_ai_execution_success')]
#[ORM\Index(columns: ['created_at'], name: 'idx_ai_execution_created_at')]
class AiExecutionLog
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
    private ?string $model = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $modelVersion = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $promptKey = null;

    #[ORM\Column(nullable: true)]
    private ?int $promptVersion = null;

    #[ORM\Column(length: 100)]
    private ?string $operation = null;

    #[ORM\Column(length: 64, nullable: true)]
    private ?string $inputHash = null;

    /**
     * @var array<string, mixed>
     */
    #[ORM\Column(type: Types::JSON)]
    private array $structuredResult = [];

    #[ORM\Column(nullable: true)]
    private ?float $confidence = null;

    /**
     * @var array<int, string>
     */
    #[ORM\Column(type: Types::JSON)]
    private array $knowledgeSources = [];

    #[ORM\Column(nullable: true)]
    private ?int $durationMs = null;

    #[ORM\Column]
    private bool $success = false;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $errorCode = null;

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

    public function setModel(string $model): static
    {
        $this->model = $this->normalizeString($model, 100);

        return $this;
    }

    public function setModelVersion(?string $modelVersion): static
    {
        $this->modelVersion = $this->normalizeNullableString($modelVersion, 100);

        return $this;
    }

    public function setPromptKey(?string $promptKey): static
    {
        $this->promptKey = $this->normalizeNullableString($promptKey, 100);

        return $this;
    }

    public function setPromptVersion(?int $promptVersion): static
    {
        $this->promptVersion = $promptVersion;

        return $this;
    }

    public function setOperation(string $operation): static
    {
        $this->operation = $this->normalizeString($operation, 100);

        return $this;
    }

    public function setInputHash(?string $inputHash): static
    {
        $this->inputHash = $this->normalizeNullableString($inputHash, 64);

        return $this;
    }

    /**
     * @param array<string, mixed> $structuredResult
     */
    public function setStructuredResult(array $structuredResult): static
    {
        $this->structuredResult = $structuredResult;

        return $this;
    }

    public function setConfidence(?float $confidence): static
    {
        $this->confidence = null === $confidence ? null : max(0.0, min(1.0, $confidence));

        return $this;
    }

    /**
     * @param array<int, string> $knowledgeSources
     */
    public function setKnowledgeSources(array $knowledgeSources): static
    {
        $this->knowledgeSources = array_values(array_filter(array_map(
            static fn (mixed $source): ?string => is_scalar($source) && '' !== trim((string) $source) ? trim((string) $source) : null,
            $knowledgeSources
        )));

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