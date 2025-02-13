<?php

namespace App\Entity;

use App\Repository\TripRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Attribute\Ignore;

#[ORM\Entity(repositoryClass: TripRepository::class)]
#[ORM\Table(name: 'trip', schema: 'oceanlux')]
class Trip
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['trip:read', 'yacht:trips'])]
    private ?int $id = null;

    #[ORM\Column(name:"name", length: 255)]
    #[Groups(['trip:read', 'yacht:trips'])]
    private ?string $name = null;

    #[ORM\Column(name: "departure", type: 'string')]
    #[Groups(['trip:read'])]
    private ?string $departure = null;

    #[ORM\Column(name:"price", type: 'float')]
    #[Groups(['trip:read'])]
    private ?float $price = null;

    #[ORM\Column(name:"duration_hours", type: 'integer')]
    #[Groups(['trip:read'])]
    private ?int $duration_hours = null;

    #[ORM\Column(name:"description", length: 1000)]
    #[Groups(['trip:read'])]
    private ?string $description = null;

    #[ORM\Column(name:"startdate", type: Types::DATE_MUTABLE)]
    #[Groups(['trip:read'])]
    private ?\DateTimeInterface $startdate = null;

    #[ORM\Column(name:"enddate", type: Types::DATE_MUTABLE)]
    #[Groups(['trip:read'])]
    private ?\DateTimeInterface $enddate = null;

    #[ORM\ManyToOne(targetEntity: Yacht::class, inversedBy: 'trips')]
    #[ORM\JoinColumn(name:"yacht_id", referencedColumnName: "id", nullable: false)]
    #[Groups(['trip:read'])]
    private ?Yacht $yacht = null;

    #[Groups(['trip:read', 'yacht:trips'])]
    public function getId(): ?int
    {
        return $this->id;
    }

    #[Groups(['trip:read', 'yacht:trips'])]
    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    #[Groups(['trip:read'])]
    public function getDeparture(): ?string
    {
        return $this->departure;
    }

    public function setDeparture(?string $departure): void
    {
        $this->departure = $departure;
    }

    #[Groups(['trip:read'])]
    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;
        return $this;
    }

    #[Groups(['trip:read'])]
    public function getDurationHours(): ?int
    {
        return $this->duration_hours;
    }

    public function setDurationHours(int $duration_hours): static
    {
        $this->duration_hours = $duration_hours;
        return $this;
    }

    #[Groups(['trip:read'])]
    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;
        return $this;
    }

    #[Groups(['trip:read'])]
    public function getStartdate(): ?\DateTimeInterface
    {
        return $this->startdate;
    }

    public function setStartdate(\DateTimeInterface $startdate): static
    {
        $this->startdate = $startdate;
        return $this;
    }

    #[Groups(['trip:read'])]
    public function getEnddate(): ?\DateTimeInterface
    {
        return $this->enddate;
    }

    public function setEnddate(\DateTimeInterface $enddate): static
    {
        $this->enddate = $enddate;
        return $this;
    }

    #[Groups(['trip:read'])]
    public function getYacht(): ?Yacht
    {
        return $this->yacht;
    }

    public function setYacht(?Yacht $yacht): static
    {
        $this->yacht = $yacht;
        return $this;
    }
}