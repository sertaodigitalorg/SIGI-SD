<?php

namespace App\Entity;

use App\Repository\PersonAddressRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PersonAddressRepository::class)]
#[ORM\Table(name: 'person_addresses')]
class PersonAddress
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Person::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Person $person = null;

    #[ORM\ManyToOne(targetEntity: Address::class, cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Address $address = null;

    #[ORM\ManyToOne(targetEntity: AddressType::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?AddressType $addressType = null;

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

    public function getAddress(): ?Address
    {
        return $this->address;
    }

    public function setAddress(?Address $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getAddressType(): ?AddressType
    {
        return $this->addressType;
    }

    public function setAddressType(?AddressType $addressType): static
    {
        $this->addressType = $addressType;

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