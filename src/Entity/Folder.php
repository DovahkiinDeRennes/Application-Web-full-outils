<?php

namespace App\Entity;


use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints\Uuid;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity]
class Folder
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $name = null;

   #[ORM\OneToMany(mappedBy: 'folder', targetEntity: AllPassword::class, cascade: ['persist', 'remove'])]
    private Collection $allPasswords;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $ref = null; 

    #[ORM\ManyToOne(inversedBy: 'folders')]
#[ORM\JoinColumn(nullable: false)]
private ?User $user = null;

        public function __construct()
    {
        $this->allPasswords = new ArrayCollection();
    }


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

/**
 * @return Collection<int, AllPassword>
 */
public function getAllPasswords(): Collection
{
    return $this->allPasswords;
}

public function addAllPassword(AllPassword $allPassword): static
{
    if (!$this->allPasswords->contains($allPassword)) {
        $this->allPasswords->add($allPassword);
        $allPassword->setFolder($this);
    }

    return $this;
}

public function removeAllPassword(AllPassword $allPassword): static
{
    if ($this->allPasswords->removeElement($allPassword)) {
        // set the owning side to null (unless already changed)
        if ($allPassword->getFolder() === $this) {
            $allPassword->setFolder(null);
        }
    }

    return $this;
}

public function getRef(): ?string
{
    return $this->ref;
}

public function setRef(string $ref): static
{
    $this->ref = $ref;

    return $this;
}

public function getUser(): ?User
{
    return $this->user;
}

public function setUser(?User $user): static
{
    $this->user = $user;

    return $this;
}
}