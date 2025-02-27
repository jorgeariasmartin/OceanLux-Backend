<?php

namespace App\Entity;

use App\Enum\BookingStatus;
use App\Enum\Role;
use App\Repository\BookingRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BookingRepository::class)]
#[ORM\Table(name: 'booking  ', schema: 'oceanlux')]
class Booking
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(name:"booking_date", type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $booking_date = null;

    #[ORM\Column(name:"number_of_guest",type: Types::INTEGER)]
    private ?int $number_of_guest = null;

    #[ORM\Column(name:"total_price",type: Types::FLOAT)]
    private ?float $total_price = null;

    #[ORM\Column(name:"status",type: 'string', enumType: BookingStatus::class)]
    private BookingStatus $status;

    #[ORM\Column(name:"rate", type: Types::FLOAT, nullable: true)]
    private ?float $rate = 0;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(name:"user_id",nullable: false)]
    private ?UserAccount $user_id = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(name: "trip_id",nullable: false)]
    private ?Trip $trip_id = null;


    public function getStatus(): BookingStatus
    {
        return $this->status;
    }

    public function setStatus(BookingStatus $status): static
    {
        $this->status = $status;
        return $this;
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBookingDate(): ?\DateTimeInterface
    {
        return $this->booking_date;
    }

    public function setBookingDate(\DateTimeInterface $booking_date): static
    {
        $this->booking_date = $booking_date;

        return $this;
    }

    public function getNumberOfGuest(): ?int
    {
        return $this->number_of_guest;
    }

    public function setNumberOfGuest(int $number_of_guest): static
    {
        $this->number_of_guest = $number_of_guest;

        return $this;
    }

    public function getTotalPrice(): ?float
    {
        return $this->total_price;
    }

    public function setTotalPrice(float $total_price): static
    {
        $this->total_price = $total_price;

        return $this;
    }

    public function getRate(): ?float
    {
        return $this->rate;
    }

    public function setRate(float $rate): static
    {
        $this->rate = $rate;

        return $this;
    }

    public function getUserId(): ?UserAccount
    {
        return $this->user_id;
    }

    public function setUserId(UserAccount $user_id): static
    {
        $this->user_id = $user_id;

        return $this;
    }

    public function getTripId(): ?Trip
    {
        return $this->trip_id;
    }

    public function setTripId(Trip $trip_id): static
    {
        $this->trip_id = $trip_id;

        return $this;
    }
}
