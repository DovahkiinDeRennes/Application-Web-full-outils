<?php

namespace App\Entity;

use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity]
#[UniqueEntity(fields: ['email'], message: 'Cet email est déjà utilisé')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column(type: 'string')]
    private ?string $password = null;

    // Optionnel : rôle pour le système de sécurité
    #[ORM\Column(type: 'json')]
    private array $roles = [];

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: AllPassword::class, cascade: ['persist', 'remove'])]
    private Collection $allPasswords;

    // User entity
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $masterKeyHash = null;

    #[ORM\Column(length: 64)]
    private string $masterSalt;

    public function __construct()
    {
        $this->allPasswords = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        // garantie que tous les utilisateurs ont au moins ROLE_USER
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }

    // Méthodes nécessaires pour UserInterface
    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function eraseCredentials(): void
    {
        // Si tu stockes des données sensibles temporairement, tu peux les effacer ici
    }

    public function getAllPasswords(): Collection
    {
        return $this->allPasswords;
    }

    public function addAllPassword(AllPassword $allPassword): self
    {
        if (!$this->allPasswords->contains($allPassword)) {
            $this->allPasswords[] = $allPassword;
            $allPassword->setUser($this);
        }
        return $this;
    }

    public function removeAllPassword(AllPassword $allPassword): self
    {
        if ($this->allPasswords->removeElement($allPassword)) {
            if ($allPassword->getUser() === $this) {
                $allPassword->setUser(null);
            }
        }
        return $this;
    }

    public function getMasterKeyHash(): ?string
    {
        return $this->masterKeyHash;
    }
    
    public function setMasterKeyHash(?string $masterKeyHash): self
    {
        $this->masterKeyHash = $masterKeyHash;
    
        return $this;
    }
    
    public function getMasterSalt(): string
    {
        return $this->masterSalt;
    }
    
    public function setMasterSalt(string $masterSalt): self
    {
        $this->masterSalt = $masterSalt;
    
        return $this;
    }
}
