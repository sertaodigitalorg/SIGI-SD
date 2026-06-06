<?php

namespace App\Entity;

use App\Repository\PersonThematicAreaRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PersonThematicAreaRepository::class)]
#[ORM\Table(name: 'person_thematic_areas')]
class PersonThematicArea
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Person::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Person $person = null;

    #[ORM\ManyToOne(targetEntity: ThematicArea::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?ThematicArea $thematicArea = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $notes = null;

    #[ORM\Column(options: ['default' => false])]
    private bool $isPrimary = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPerson(): ?Person
    {
        return $this->person;
    }

    public function setPerson(?Person $person): static
    {
        $this->person = $person;

        return $this;
    }

    public function getThematicArea(): ?ThematicArea
    {
        return $this->thematicArea;
    }

    public function setThematicArea(?ThematicArea $thematicArea): static
    {
        $this->thematicArea = $thematicArea;

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

    public function isPrimary(): bool
    {
        return $this->isPrimary;
    }

    public function setIsPrimary(bool $isPrimary): static
    {
        $this->isPrimary = $isPrimary;

        return $this;
    }
}