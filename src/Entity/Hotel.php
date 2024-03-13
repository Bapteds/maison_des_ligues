<?php

namespace App\Entity;

use App\Repository\HotelRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HotelRepository::class)]
class Hotel
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $adresse = null;

    #[ORM\Column(length: 255)]
    private ?string $adresse2 = null;

    #[ORM\Column(length: 255)]
    private ?string $cp = null;

    #[ORM\Column(length: 255)]
    private ?string $ville = null;

    #[ORM\Column(length: 255)]
    private ?string $tel = null;

    #[ORM\Column(length: 255)]
    private ?string $mail = null;

    #[ORM\OneToMany(mappedBy: 'hotel', targetEntity: nuite::class, orphanRemoval: true)]
    private Collection $nuites;

    #[ORM\OneToOne(mappedBy: 'hotel', cascade: ['persist', 'remove'])]
    private ?Proposer $proposer = null;

    #[ORM\OneToMany(mappedBy: 'hotel', targetEntity: Proposer::class)]
    private Collection $proposers;

    public function __construct()
    {
        $this->nuites = new ArrayCollection();
        $this->proposers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getAdresse2(): ?string
    {
        return $this->adresse2;
    }

    public function setAdresse2(string $adresse2): self
    {
        $this->adresse2 = $adresse2;

        return $this;
    }


    public function getCp(): ?string
    {
        return $this->cp;
    }

    public function setCp(string $cp): self
    {
        $this->cp = $cp;

        return $this;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(string $ville): self
    {
        $this->ville = $ville;

        return $this;
    }

    public function getTel(): ?string
    {
        return $this->tel;
    }

    public function setTel(string $tel): self
    {
        $this->tel = $tel;

        return $this;
    }

    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(string $mail): self
    {
        $this->mail = $mail;

        return $this;
    }


    public function __toString() {
        return $this->nom;
    }

    /**
     * @return Collection<int, nuite>
     */
    public function getNuites(): Collection
    {
        return $this->nuites;
    }

    public function addNuite(nuite $nuite): static
    {
        if (!$this->nuites->contains($nuite)) {
            $this->nuites->add($nuite);
            $nuite->setHotel($this);
        }

        return $this;
    }

    public function removeNuite(nuite $nuite): static
    {
        if ($this->nuites->removeElement($nuite)) {
            // set the owning side to null (unless already changed)
            if ($nuite->getHotel() === $this) {
                $nuite->setHotel(null);
            }
        }

        return $this;
    }

    public function getProposer(): ?Proposer
    {
        return $this->proposer;
    }

    public function setProposer(Proposer $proposer): static
    {
        // set the owning side of the relation if necessary
        if ($proposer->getHotel() !== $this) {
            $proposer->setHotel($this);
        }

        $this->proposer = $proposer;

        return $this;
    }

    /**
     * @return Collection<int, Proposer>
     */
    public function getProposers(): Collection
    {
        return $this->proposers;
    }

    public function addProposer(Proposer $proposer): static
    {
        if (!$this->proposers->contains($proposer)) {
            $this->proposers->add($proposer);
            $proposer->setHotel($this);
        }

        return $this;
    }

    public function removeProposer(Proposer $proposer): static
    {
        if ($this->proposers->removeElement($proposer)) {
            // set the owning side to null (unless already changed)
            if ($proposer->getHotel() === $this) {
                $proposer->setHotel(null);
            }
        }

        return $this;
    }
}
