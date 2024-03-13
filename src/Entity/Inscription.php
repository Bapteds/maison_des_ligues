<?php

namespace App\Entity;

use App\Entity\User;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\InscriptionRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: InscriptionRepository::class)]
class Inscription
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateInscription = null;

    #[ORM\OneToOne(targetEntity: User::class, cascade: ['persist', 'remove'], inversedBy: "inscriptions")]
    private ?User $licencie = null;

    #[Assert\Count(
        min: 1,
        max: 5,
    )]
    #[ORM\ManyToMany(targetEntity: Atelier::class, inversedBy: 'inscriptions')]
    private Collection $atelierInscrit;

    #[ORM\ManyToMany(targetEntity: Restauration::class, inversedBy: 'inscriptions')]
    private Collection $restaurer;


    #[ORM\ManyToOne(inversedBy: 'inscriptions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Etat $etat = null;

    public function __construct()
    {
        $this->atelierInscrit = new ArrayCollection();
        $this->restaurer = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateInscription(): ?\DateTimeInterface
    {
        return $this->dateInscription;
    }

    public function setDateInscription(\DateTimeInterface $dateInscription): self
    {
        $this->dateInscription = $dateInscription;

        return $this;
    }

    public function getLicencie(): ?User
    {
        return $this->licencie;
    }

    public function setLicencie(?User $licencie): self
    {
        $this->licencie = $licencie;

        return $this;
    }

    /**
     * @return Collection<int, Atelier>
     */
    public function getAtelierInscrit(): Collection
    {
        return $this->atelierInscrit;
    }

    public function addAtelierInscrit(Atelier $atelierInscrit): self
    {
        if (!$this->atelierInscrit->contains($atelierInscrit)) {
            $this->atelierInscrit->add($atelierInscrit);
        }

        return $this;
    }

    public function removeAtelierInscrit(Atelier $atelierInscrit): self
    {
        $this->atelierInscrit->removeElement($atelierInscrit);

        return $this;
    }

    /**
     * @return Collection<int, Restauration>
     */
    public function getRestaurer(): Collection
    {
        return $this->restaurer;
    }

    public function addRestaurer(Restauration $restaurer): self
    {
        if (!$this->restaurer->contains($restaurer)) {
            $this->restaurer->add($restaurer);
        }

        return $this;
    }

    public function removeRestaurer(Restauration $restaurer): self
    {
        $this->restaurer->removeElement($restaurer);

        return $this;
    }

    public function getEtat(): ?Etat
    {
        return $this->etat;
    }

    public function setEtat(?Etat $etat): self
    {
        $this->etat = $etat;

        return $this;
    }
}
