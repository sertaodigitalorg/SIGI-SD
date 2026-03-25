<?php

namespace App\Entity;

use App\Repository\CityRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CityRepository::class)]
#[ORM\Table(name: 'cities')]
class City
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Microregion::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Microregion $microregion = null;

    #[ORM\ManyToOne(targetEntity: State::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?State $state = null;

    #[ORM\Column(type: 'decimal', precision: 15, scale: 2, nullable: true)]
    private ?string $areaKm2 = null;

    #[ORM\Column(type: 'decimal', precision: 18, scale: 2, nullable: true)]
    private ?string $gdp = null;

    #[ORM\Column(nullable: true)]
    private ?int $population = null;

    #[ORM\Column(type: 'decimal', precision: 18, scale: 2, nullable: true)]
    private ?string $annualRevenue = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $tomCode = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $ibgeCode = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $ibgeCode7 = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $zipCode = null;

    #[ORM\Column(length: 191, nullable: true)]
    private ?string $tomName = null;

    #[ORM\Column(length: 191)]
    private ?string $ibgeName = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $size = null;

    #[ORM\Column(options: ['default' => false])]
    private ?bool $isCapital = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMicroregion(): ?Microregion
    {
        return $this->microregion;
    }

    public function setMicroregion(?Microregion $microregion): static
    {
        $this->microregion = $microregion;

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

    public function getAreaKm2(): ?string
    {
        return $this->areaKm2;
    }

    public function setAreaKm2(?string $areaKm2): static
    {
        $this->areaKm2 = $areaKm2;

        return $this;
    }

    public function getGdp(): ?string
    {
        return $this->gdp;
    }

    public function setGdp(?string $gdp): static
    {
        $this->gdp = $gdp;

        return $this;
    }

    public function getPopulation(): ?int
    {
        return $this->population;
    }

    public function setPopulation(?int $population): static
    {
        $this->population = $population;

        return $this;
    }

    public function getAnnualRevenue(): ?string
    {
        return $this->annualRevenue;
    }

    public function setAnnualRevenue(?string $annualRevenue): static
    {
        $this->annualRevenue = $annualRevenue;

        return $this;
    }

    public function getTomCode(): ?string
    {
        return $this->tomCode;
    }

    public function setTomCode(?string $tomCode): static
    {
        $this->tomCode = $tomCode;

        return $this;
    }

    public function getIbgeCode(): ?string
    {
        return $this->ibgeCode;
    }

    public function setIbgeCode(?string $ibgeCode): static
    {
        $this->ibgeCode = $ibgeCode;

        return $this;
    }

    public function getIbgeCode7(): ?string
    {
        return $this->ibgeCode7;
    }

    public function setIbgeCode7(?string $ibgeCode7): static
    {
        $this->ibgeCode7 = $ibgeCode7;

        return $this;
    }

    public function getZipCode(): ?string
    {
        return $this->zipCode;
    }

    public function setZipCode(?string $zipCode): static
    {
        $this->zipCode = $zipCode;

        return $this;
    }

    public function getTomName(): ?string
    {
        return $this->tomName;
    }

    public function setTomName(?string $tomName): static
    {
        $this->tomName = $tomName;

        return $this;
    }

    public function getIbgeName(): ?string
    {
        return $this->ibgeName;
    }

    public function setIbgeName(string $ibgeName): static
    {
        $this->ibgeName = $ibgeName;

        return $this;
    }

    public function getSize(): ?string
    {
        return $this->size;
    }

    public function setSize(?string $size): static
    {
        $this->size = $size;

        return $this;
    }

    public function isCapital(): ?bool
    {
        return $this->isCapital;
    }

    public function setIsCapital(bool $isCapital): static
    {
        $this->isCapital = $isCapital;

        return $this;
    }
}