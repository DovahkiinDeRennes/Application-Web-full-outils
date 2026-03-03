<?php

namespace App\Entity;

use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints\Uuid;

#[ORM\Entity]
class AllPassword
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $url = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $identifier = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $password = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $site = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'allPasswords')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(length: 255)]
    private string $nonce; // base64

    #[ORM\Column(length: 255)]
    private string $tag; // base64

    #[ORM\Column(type: 'guid', unique: true)]
    private ?string $uuid = null;



    public function __construct()
    {
        $this->uuid = $this->generateUuidV4();
    }
    
    /**
     * Génère un UUID version 4 (aléatoire)
     */
    private function generateUuidV4(): string
    {
        $data = random_bytes(16);
    
        // version 4
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        // variant RFC 4122
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
    
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;
        return $this;
    }

    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }

    public function setIdentifier(string $identifier): self
    {
        $this->identifier = $identifier;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    public function getSite(): ?string
    {
        return $this->site;
    }

    public function setSite(string $site): self
    {
        $this->site = $site;
        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getNonce(): string
    {
        return $this->nonce;
    }

    public function setNonce(string $nonce): self
    {
        $this->nonce = $nonce;
        return $this;
    }

    public function getTag(): string
    {
        return $this->tag;
    }

    public function setTag(string $tag): self
    {
        $this->tag = $tag;
        return $this;
    }

    public function getUuid(): ?string
    {
        return $this->uuid;
    }
    
    public function setUuid(string $uuid): self
    {
        $this->uuid = $uuid;
    
        return $this;
    }
}
