<?php

namespace App\Entity;

use App\Repository\InscriptionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InscriptionRepository::class)]
class Inscription
{
    /**
     * Id Inscription
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * Date inscription Inscription
     */
    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateInscription = null;

    /**
     * Les ateliers d'Inscription
     */
    #[ORM\ManyToMany(inversedBy: 'inscriptions', targetEntity: Atelier::class)]
    private ?Collection $ateliers;

    /**
     * Les restaurations d'Inscription
     */
    #[ORM\ManyToMany(inversedBy: 'inscriptions', targetEntity: Restauration::class)]
    private ?Collection $restaurations;

    /**
     * Les nuités d'Inscription
     */
    #[ORM\ManyToMany(mappedBy: 'inscriptions', targetEntity: Nuite::class)]
    private ?Collection $nuites;

    /**
     * Le compte d'Inscription
     */
    #[ORM\ManyToOne(inversedBy: 'inscription')]
    private ?User $user = null;

    #[ORM\Column]
    private ?bool $estPaye = null;


    public function __construct()
    {
        $this->restaurations = new ArrayCollection();
        $this->ateliers = new ArrayCollection();
        $this->nuites = new ArrayCollection();
    }
    /**
     * Retourne l'id d'Inscription
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Retourne la date d'Inscription
     */
    public function getDateInscription(): ?\DateTimeInterface
    {
        return $this->dateInscription;
    }

    /**
     * Définit la date d'Inscription
     */
    public function setDateInscription(?\DateTimeInterface $dateInscription): static
    {
        $this->dateInscription = $dateInscription;

        return $this;
    }

    public function setAtelier(Atelier $atelier)
    {
        if (!$this->ateliers->contains($atelier)) {
            $this->ateliers->add($atelier);
        }

        return $this;
    }

    public function setRestauration(Restauration $restauration)
    {
        if (!$this->restaurations->contains($restauration)) {
            $this->restaurations[] = $restauration;
            $restauration->setInscription($this);
        }
        return $this;
    }

    public function setUser(User $user): static
    {
        // set the owning side of the relation if necessary
        if ($user->getInscription() !== $this) {
            $user->setInscription($this);
        }

        $this->user = $user;

        return $this;
    }

    public function setNuite(Nuite $nuite)
    {
        if (!$this->nuites->contains($nuite)) {
            $this->nuites->add($nuite);
            $nuite->setInscription($this);
        }
        return $this;
    }

    public function isEstPaye(): ?bool
    {
        return $this->estPaye;
    }

    public function setEstPaye(bool $estPaye): static
    {
        $this->estPaye = $estPaye;

        return $this;
    }

    public function getAteliers(){
        return $this->ateliers;
    }

    public function getNuites(){
        return $this->nuites;
    }

    public function getRestaurations(){
        return $this->restaurations;
    }
}
