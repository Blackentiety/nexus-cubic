<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $role = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $pseudoMinecraft = null;

    #[ORM\Column(length: 255)]
    private ?string $uuidMinecraft = null;

    #[ORM\Column]
    private ?float $credits = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $dateInscription = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $token = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): static
    {
        $this->role = $role;

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

    public function getPseudoMinecraft(): ?string
    {
        return $this->pseudoMinecraft;
    }

    public function setPseudoMinecraft(string $pseudoMinecraft): static
    {
        $this->pseudoMinecraft = $pseudoMinecraft;

        return $this;
    }

    public function getUuidMinecraft(): ?string
    {
        return $this->uuidMinecraft;
    }

    public function setUuidMinecraft(string $uuidMinecraft): static
    {
        $this->uuidMinecraft = $uuidMinecraft;

        return $this;
    }

    public function getCredits(): ?float
    {
        return $this->credits;
    }

    public function setCredits(float $credits): static
    {
        $this->credits = $credits;

        return $this;
    }

    public function getDateInscription(): ?\DateTime
    {
        return $this->dateInscription;
    }

    public function setDateInscription(\DateTime $dateInscription): static
    {
        $this->dateInscription = $dateInscription;

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): static
    {
        $this->token = $token;

        return $this;
    }
}
