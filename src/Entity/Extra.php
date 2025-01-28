<?php

namespace App\Entity;

use App\Repository\ExtraRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ExtraRepository::class)]
#[ORM\Table(name: 'extra', schema: 'oceanlux')]

class Extra
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(name:"name",length: 200)]
    private ?string $name = null;

    #[ORM\Column(name:"description",length: 1000)]
    private ?string $description = null;

    #[ORM\Column(name:"price",type: 'float')]
    private ?float $price = null;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }
}
