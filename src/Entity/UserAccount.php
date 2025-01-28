<?php

namespace App\Entity;

use App\Enum\Role;
use App\Repository\UserAccountRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserAccountRepository::class)]
#[ORM\Table(name: 'user_account', schema: 'oceanlux')]
class UserAccount
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(name:"username",length: 300)]
    private ?string $username = null;

    #[ORM\Column(name:"email" ,length: 100)]
    private ?string $email = null;


    #[ORM\Column(name:"rol",type: 'string', enumType: Role::class)]
    private Role $rol;


    #[ORM\Column(name:"password",length: 250)]
    private ?string $password = null;



    #[ORM\OneToOne(targetEntity: Client::class, cascade: ['persist', 'remove'], mappedBy: 'userAccount')]
    private ?Client $client_id = null;




    public function getRol(): Role
    {
        return $this->rol;
    }

    public function setRol(Role $rol): self
    {
        $this->rol = $rol;
        return $this;
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return Collection<int, Trip>
     */
    public function getUserTrips(): Collection
    {
        return $this->user_trips;
    }

    public function addUserTrip(Trip $userTrip): static
    {
        if (!$this->user_trips->contains($userTrip)) {
            $this->user_trips->add($userTrip);
            $userTrip->setYachtId($this);
        }

        return $this;
    }

    public function removeUserTrip(Trip $userTrip): static
    {
        if ($this->user_trips->removeElement($userTrip)) {
            // set the owning side to null (unless already changed)
            if ($userTrip->getYachtId() === $this) {
                $userTrip->setYachtId(null);
            }
        }

        return $this;
    }

    public function getClientId(): ?Client
    {
        return $this->client_id;
    }

    public function setClientId(?Client $client_id): static
    {
        $this->client_id = $client_id;

        return $this;
    }
}
