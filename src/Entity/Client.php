<?php

namespace App\Entity;

use App\Repository\ClientRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ClientRepository::class)]
#[ORM\Table(name: 'client', schema: 'oceanlux')]

class Client
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(name:"name",length: 100,nullable: false)]
    private ?string $name = null;

    #[ORM\Column(name:"surname",length: 300,nullable: false)]
    private ?string $surname = null;

    #[ORM\Column(name:"birthdate",type: Types::DATE_MUTABLE,nullable: false)]
    private ?\DateTimeInterface $birthdate ;

    #[ORM\Column(name:"dni",length: 9,nullable: false)]
    private ?string $dni ;

    #[ORM\Column(name:"address",length: 400,nullable: false)]
    private ?string $address;

    #[ORM\OneToOne(inversedBy: 'client_id', targetEntity: UserAccount::class)]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id')]
    private ?UserAccount $userAccount = null;

    #[ORM\Column]
    private ?int $phone_number = null;

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

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(string $surname): static
    {
        $this->surname = $surname;

        return $this;
    }

    public function getBirthdate(): ?\DateTimeInterface
    {
        return $this->birthdate;
    }

    public function setBirthdate(\DateTimeInterface $birthdate): static
    {
        $this->birthdate = $birthdate;

        return $this;
    }

    public function getDni(): ?string
    {
        return $this->dni;
    }

    public function setDni(string $dni): static
    {
        $this->dni = $dni;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getPhoneNumber(): ?int
    {
        return $this->phone_number;
    }

    public function setPhoneNumber(int $phone_number): static
    {
        $this->phone_number = $phone_number;

        return $this;
    }
}
