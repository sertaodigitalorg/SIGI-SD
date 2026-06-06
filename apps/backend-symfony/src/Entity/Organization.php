<?php

namespace App\Entity;

use App\Repository\OrganizationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[ORM\Entity(repositoryClass: OrganizationRepository::class)]
#[ORM\Table(name: 'organizations')]
#[UniqueEntity(fields: ['cnpj'], message: 'Já existe uma organização com este CNPJ.')]
class Organization implements \Stringable
{
    public const STATUS_ACTIVE = 'Ativa';
    public const STATUS_INACTIVE = 'Inativa';
    public const STATUS_PLANNED = 'Planejada';
    public const STATUS_ARCHIVED = 'Arquivada';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 191)]
    #[Assert\NotBlank(message: 'Informe a razão social.')]
    private ?string $legalName = null;

    #[ORM\Column(length: 191, nullable: true)]
    private ?string $tradeName = null;

    #[ORM\Column(type: 'string', length: 18, nullable: true, unique: true)]
    #[Assert\Regex(
        pattern: '/^\d{2}\.\d{3}\.\d{3}\/\d{4}\-\d{2}$/',
        message: 'CNPJ inválido',
    )]
    private ?string $cnpj = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $acronym = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $status = self::STATUS_ACTIVE;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $notes = null;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'children')]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    private ?self $parent = null;

    /**
     * @var Collection<int, self>
     */
    #[ORM\OneToMany(mappedBy: 'parent', targetEntity: self::class)]
    #[ORM\OrderBy(['legalName' => 'ASC'])]
    private Collection $children;

    #[ORM\ManyToOne(targetEntity: OrganizationType::class, inversedBy: 'organizations')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?OrganizationType $organizationType = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->children = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->status = self::STATUS_ACTIVE;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLegalName(): ?string
    {
        return $this->legalName;
    }

    public function setLegalName(string $legalName): static
    {
        $this->legalName = $legalName;

        return $this;
    }

    public function getTradeName(): ?string
    {
        return $this->tradeName;
    }

    public function setTradeName(?string $tradeName): static
    {
        $this->tradeName = $tradeName;

        return $this;
    }

    public function getCnpj(): ?string
    {
        return $this->cnpj;
    }

    public function setCnpj(?string $cnpj): static
    {
        if ($cnpj !== null && trim($cnpj) === '') {
            $cnpj = null;
        }

        $this->cnpj = $cnpj;

        return $this;
    }

    public function getAcronym(): ?string
    {
        return $this->acronym;
    }

    public function setAcronym(?string $acronym): static
    {
        $this->acronym = $acronym;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): static
    {
        $this->status = $status;

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

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): static
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function addChild(self $child): static
    {
        if (!$this->children->contains($child)) {
            $this->children->add($child);
            $child->setParent($this);
        }

        return $this;
    }

    public function removeChild(self $child): static
    {
        if ($this->children->removeElement($child) && $child->getParent() === $this) {
            $child->setParent(null);
        }

        return $this;
    }

    public function getOrganizationType(): ?OrganizationType
    {
        return $this->organizationType;
    }

    public function setOrganizationType(?OrganizationType $organizationType): static
    {
        $this->organizationType = $organizationType;

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
        return $this->tradeName ?: (string) $this->legalName;
    }

    /**
     * @return array<int, string>
     */
    public static function getAvailableStatuses(): array
    {
        return [
            self::STATUS_ACTIVE,
            self::STATUS_INACTIVE,
            self::STATUS_PLANNED,
            self::STATUS_ARCHIVED,
        ];
    }

    #[Assert\Callback]
    public function validateHierarchy(ExecutionContextInterface $context): void
    {
        if (null === $this->parent) {
            return;
        }

        if ($this->isSameOrganization($this->parent)) {
            $context->buildViolation('A organização pai deve ser diferente da organização atual.')
                ->atPath('parent')
                ->addViolation();

            return;
        }

        $visited = [];
        $ancestor = $this->parent;

        while (null !== $ancestor) {
            $key = spl_object_hash($ancestor);
            if (isset($visited[$key])) {
                break;
            }

            $visited[$key] = true;

            if ($this->isSameOrganization($ancestor)) {
                $context->buildViolation('A hierarquia informada cria um ciclo entre organizações.')
                    ->atPath('parent')
                    ->addViolation();

                return;
            }

            $ancestor = $ancestor->getParent();
        }
    }

    private function isSameOrganization(?self $organization): bool
    {
        if (null === $organization) {
            return false;
        }

        if ($this === $organization) {
            return true;
        }

        return null !== $this->id && null !== $organization->getId() && $this->id === $organization->getId();
    }
}