<?php

namespace App\Entity;

use App\Repository\TripRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TripRepository::class)]
#[ORM\Table(name: 'trip', schema: 'oceanlux')]

class Trip
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(name:"name",length: 100)]
    private ?string $name = null;

    #[ORM\Column(name:"price",type: 'float')]
    private ?float $price = null;

    #[ORM\Column(name:"duration_hours",type: Types::INTEGER)]
    private ?int $duration_hours = null;

    #[ORM\Column(name:"description",length: 1000)]
    private ?string $description = null;

    #[ORM\Column(name:"startdate",type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $startdate = null;

    #[ORM\Column(name:"enddate",type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $end_date = null;

    #[ORM\ManyToOne(inversedBy: 'user_trips')]
    #[ORM\JoinColumn(name:"yacht_id",nullable: false)]
    private Yacht $yacht_id;

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

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getDurationHours(): ?int
    {
        return $this->duration_hours;
    }

    public function setDurationHours(int $duration_hours): static
    {
        $this->duration_hours = $duration_hours;

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

    public function getStartdate(): ?\DateTimeInterface
    {
        return $this->startdate;
    }

    public function setStartdate(\DateTimeInterface $startdate): static
    {
        $this->startdate = $startdate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->end_date;
    }

    public function setEndDate(\DateTimeInterface $end_date): static
    {
        $this->end_date = $end_date;

        return $this;
    }

    public function getYachtId(): ?UserAccount
    {
        return $this->yacht_id;
    }

    public function setYachtId(?UserAccount $yacht_id): static
    {
        $this->yacht_id = $yacht_id;

        return $this;
    }
}
