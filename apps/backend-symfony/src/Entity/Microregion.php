<?php

namespace App\Entity;

use App\Repository\MicroregionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MicroregionRepository::class)]
#[ORM\Table(name: 'microregions')]
class Microregion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 191)]
    private ?string $name = null;

    #[ORM\Column(length: 20, unique: true)]
    private ?string $ibgeCode = null;

    #[ORM\ManyToOne(targetEntity: Mesoregion::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Mesoregion $mesoregion = null;

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

    public function getMesoregion(): ?Mesoregion
    {
        return $this->mesoregion;
    }

    public function setMesoregion(?Mesoregion $mesoregion): static
    {
        $this->mesoregion = $mesoregion;

        return $this;
    }
}