<?php

namespace App\Entity;

use App\Repository\UserAccountRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\Ignore;
use JsonSerializable;

#[ORM\Entity(repositoryClass: UserAccountRepository::class)]
#[ORM\Table(name: 'user_account', schema: 'oceanlux')]
class UserAccount implements UserInterface, PasswordAuthenticatedUserInterface, JsonSerializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['user:read', 'user:write'])]
    private ?int $id = null;

    #[ORM\Column(name: "username", length: 300)]
    #[Groups(['user:read', 'user:write'])]
    private ?string $username = null;

    #[ORM\Column(name: "email", length: 100)]
    #[Groups(['user:read', 'user:write'])]
    private ?string $email = null;

    #[ORM\Column(name: "rol", type: 'string', length: 255, nullable: false)]
    #[Groups(['user:read', 'user:write'])]
    private string $rol;

    #[ORM\Column(name: "password", length: 250)]
    #[Ignore] // No se serializa
    private ?string $password = null;

    #[ORM\OneToOne(targetEntity: Client::class, inversedBy: 'userAccount', cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['user:read'])] // Solo incluir si es necesario
    private Client $client;

    #[ORM\Column(name: "validation_token", type: 'string', length: 255, nullable: true)]
    private ?string $validationToken = null;

    #[ORM\Column(name: "expires_at", type: "datetime", nullable: true)]
    private ?\DateTime $expiresAt = null;

    #[ORM\Column(name:"verified",type: 'boolean', nullable: false)]
    private bool $isVerified = false;

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;
        return $this;
    }

    public function getExpiresAt(): ?\DateTime
    {
        return $this->expiresAt;
    }

    public function setExpiresAt(?\DateTime $expiresAt): void
    {
        $this->expiresAt = $expiresAt;
    }

    public function getValidationToken(): ?string
    {
        return $this->validationToken;
    }

    public function setValidationToken(?string $validationToken): void
    {
        $this->validationToken = $validationToken;
    }

    public function getUserRole(): ?string
    {
        return $this->rol;
    }

    public function setRol(string $rol): static
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

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): self
    {
        $this->client = $client;
        return $this;
    }

    public function eraseCredentials(): void
    {
        // No implementado
    }

    public function getUserIdentifier(): string
    {
        return $this->username;
    }

    public function getRoles(): array
    {
        return [$this->rol];
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'email' => $this->email,
            'rol' => $this->rol,
            'client' => [
                'id' => $this->client->getId(),
                'name' => $this->client->getName(),
                'surname' => $this->client->getSurname(),
                'birthdate' => $this->client->getBirthdate()?->format('Y-m-d'),
                'dni' => $this->client->getDni(),
                'address' => $this->client->getAddress(),
                'phone_number' => $this->client->getPhoneNumber(),
            ],
        ];
    }
}