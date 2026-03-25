<?php

namespace App\Entity;

use App\Repository\MesoregionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MesoregionRepository::class)]
#[ORM\Table(name: 'mesoregions')]
class Mesoregion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 191)]
    private ?string $name = null;

    #[ORM\Column(length: 20, unique: true)]
    private ?string $ibgeCode = null;

    #[ORM\Column(nullable: true)]
    private ?int $municipalitiesCount = null;

    #[ORM\ManyToOne(targetEntity: State::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?State $state = null;

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
        $this->name = $name;

        return $this;
    }

    public function getIbgeCode(): ?string
    {
        return $this->ibgeCode;
    }

    public function setIbgeCode(string $ibgeCode): static
    {
        $this->ibgeCode = $ibgeCode;

        return $this;
    }

    public function getMunicipalitiesCount(): ?int
    {
        return $this->municipalitiesCount;
    }

    public function setMunicipalitiesCount(?int $municipalitiesCount): static
    {
        $this->municipalitiesCount = $municipalitiesCount;

        return $this;
    }

    public function getState(): ?State
    {
        return $this->state;
    }

    public function setState(?State $state): static
    {
        $this->state = $state;

        return $this;
    }
}