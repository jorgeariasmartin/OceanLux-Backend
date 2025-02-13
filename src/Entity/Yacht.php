<?php

namespace App\Entity;

use App\Repository\YachtRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Attribute\MaxDepth;

#[ORM\Entity(repositoryClass: YachtRepository::class)]
#[ORM\Table(name: 'yacht', schema: 'oceanlux')]
class Yacht
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['yacht:read', 'trip:read'])]
    private ?int $id = null;

    #[ORM\Column(name: "yacht_name", length: 100)]
    #[Groups(['yacht:read', 'trip:read'])]
    private ?string $name = null;

    #[ORM\Column(name: "yacht_model", length: 100)]
    #[Groups(['yacht:read', 'trip:read'])]
    private ?string $model = null;

    #[ORM\Column(name: "image", type: 'string', length: 255)]
    #[Groups(['yacht:read', 'trip:read'])]
    private ?string $image = null;

    #[ORM\Column(name: "description", type: 'string')]
    #[Groups(['yacht:read', 'trip:read'])]
    private ?string $description = null;

    #[ORM\Column(name: "capacity", type: 'integer')]
    #[Groups(['yacht:read', 'trip:read'])]
    private ?int $capacity = null;

    public function getCapacity(): ?int
    {
        return $this->capacity;
    }

    public function setCapacity(?int $capacity): void
    {
        $this->capacity = $capacity;
    }

    #[ORM\OneToMany(targetEntity: Trip::class, mappedBy: 'yacht')]
    #[Groups(['yacht:trips'])]
    #[MaxDepth(1)]
    private Collection $trips;

    public function __construct()
    {
        $this->trips = new ArrayCollection();
    }

    #[Groups(['yacht:read', 'trip:read'])]
    public function getId(): ?int
    {
        return $this->id;
    }

    #[Groups(['yacht:read', 'trip:read'])]
    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    #[Groups(['yacht:read'])]
    public function getModel(): ?string
    {
        return $this->model;
    }

    public function setModel(string $model): static
    {
        $this->model = $model;
        return $this;
    }

    #[Groups(['yacht:read'])]
    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;
        return $this;
    }

    #[Groups(['yacht:read'])]
    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;
        return $this;
    }

    #[Groups(['yacht:trips'])]
    public function getTrips(): Collection
    {
        return $this->trips;
    }
}