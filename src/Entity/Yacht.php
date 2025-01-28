<?php

namespace App\Entity;

use App\Repository\YachtRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: YachtRepository::class)]
#[ORM\Table(name: 'yacht', schema: 'oceanlux')]
class Yacht
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(name: "yacht_name",length: 200)]

    private ?string $yacht_name = null;

    #[ORM\Column(name: "yacht_model",length: 200)]
    private ?string $yacht_model = null;

    #[ORM\Column(name: "image",length: 1000)]
    private ?string $image = null;

    #[ORM\Column(name: "description",length: 1000)]
    private ?string $description = null;

    #[ORM\Column]
    private ?int $capacity = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getYachtName(): ?string
    {
        return $this->yacht_name;
    }

    public function setYachtName(string $yacht_name): static
    {
        $this->yacht_name = $yacht_name;

        return $this;
    }

    public function getYachtModel(): ?string
    {
        return $this->yacht_model;
    }

    public function setYachtModel(string $yacht_model): static
    {
        $this->yacht_model = $yacht_model;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;

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

    public function getCapacity(): ?int
    {
        return $this->capacity;
    }

    public function setCapacity(int $capacity): static
    {
        $this->capacity = $capacity;

        return $this;
    }
}
