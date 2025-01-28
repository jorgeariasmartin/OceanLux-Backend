<?php

namespace App\Entity;

use App\Repository\BookingExtrasRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BookingExtrasRepository::class)]
#[ORM\Table(name: 'booking_extras', schema: 'oceanlux')]

class BookingExtras
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name:"booking_id",nullable: false)]
    private ?Booking $booking_id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name:"extra_id",nullable: false)]
    private ?extra $extra_id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBookingId(): ?Booking
    {
        return $this->booking_id;
    }

    public function setBookingId(?Booking $booking_id): static
    {
        $this->booking_id = $booking_id;

        return $this;
    }

    public function getExtraId(): ?extra
    {
        return $this->extra_id;
    }

    public function setExtraId(?extra $extra_id): static
    {
        $this->extra_id = $extra_id;

        return $this;
    }
}
