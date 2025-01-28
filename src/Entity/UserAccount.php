<?php

namespace App\Entity;

use App\Enum\Role;
use App\Repository\UserAccountRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: UserAccountRepository::class)]
#[ORM\Table(name: 'user_account', schema: 'oceanlux')]
class UserAccount implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(name:"username", length: 300)]
    private ?string $username = null;

    #[ORM\Column(name:"email", length: 100)]
    private ?string $email = null;

    #[ORM\Column(name:"rol", type: 'string', length: 255, nullable: false)]
    private string $rol;

    #[ORM\Column(name:"password", length: 250)]
    private ?string $password = null;

    #[ORM\OneToOne(inversedBy: 'userAccount', targetEntity: Client::class, cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private Client $client;

    public function __construct()
    {
        $this->rol = Role::USER->value; // Default role
    }

    public function getRol(): Role
    {
        return Role::from($this->rol);
    }

    public function setRol(Role $rol): static
    {
        $this->rol = $rol->value;
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

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): self
    {
        $this->client = $client;
        return $this;
    }

    public function getRoles(): array
    {
        return ['ROLE_USER'];
    }

    public function eraseCredentials(): void
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getUserIdentifier(): string
    {
        return $this->username;
    }
}