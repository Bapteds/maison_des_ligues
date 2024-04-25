<?php

namespace App\Entity;

use App\Entity\Inscription;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['numlicence'], message: 'Il existe déjà un compte ayant ce numéro de licence.')]
class User implements UserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name:'id')]
    private ?int $id = null;

    #[ORM\Column(name:'email', length: 180)]
    private ?string $email = null;

    #[ORM\Column(name:'roles')]
    private string $roles;

    /**
     * @var string The hashed password
     */
    #[ORM\Column(name:'password')]
    private ?string $password = null;

    #[ORM\Column(name:'numlicence', length: 255, unique: true)]
    private ?string $numlicence = null;

    #[ORM\Column(name:'isVerified', type: 'boolean')]
    private $isVerified = false;

    #[ORM\OneToOne(targetEntity: Inscription::class, cascade: ['persist', 'remove'], mappedBy: "licencie")]
    private ?Inscription $inscriptions = null;

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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): string
    {
        //$roles = $this->roles;
        $roles = 'ROLE_INSCRIT';
        return $roles;
    }

    public function setRoles(string $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getNumlicence(): ?string
    {
        return $this->numlicence;
    }

    public function setNumlicence(string $numlicence): self
    {
        $this->numlicence = $numlicence;

        return $this;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function __toString()
    {
        return $this->email;
    }

    public function getInscription(): ?Inscription
    {
        return $this->inscriptions;
    }

    public function setInscription(?Inscription $inscriptions): self
    {
        $this->inscriptions = $inscriptions;

        return $this;
    }
}