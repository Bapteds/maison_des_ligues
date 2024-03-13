<?php

namespace App\Entity;

use App\Repository\ChambreRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ChambreRepository::class)]
class Chambre
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $libelleCategorie = null;

    #[ORM\OneToMany(mappedBy: 'chambre', targetEntity: Nuite::class, orphanRemoval: true)]
    private Collection $nuite;

    #[ORM\OneToMany(mappedBy: 'chambre', targetEntity: Proposer::class)]
    private Collection $proposers;




    public function __construct()
    {
        $this->nuite = new ArrayCollection();
        $this->proposers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelleCategorie(): ?string
    {
        return $this->libelleCategorie;
    }

    public function setLibelleCategorie(string $libelleCategorie): static
    {
        $this->libelleCategorie = $libelleCategorie;

        return $this;
    }

    /**
     * @return Collection<int, nuite>
     */
    public function getNuite(): Collection
    {
        return $this->nuite;
    }

    public function addNuite(Nuite $nuite): static
    {
        if (!$this->nuite->contains($nuite)) {
            $this->nuite->add($nuite);
            $nuite->setChambre($this);
        }

        return $this;
    }

    public function removeNuite(Nuite $nuite): static
    {
        if ($this->nuite->removeElement($nuite)) {
            // set the owning side to null (unless already changed)
            if ($nuite->getChambre() === $this) {
                $nuite->setChambre(null);
            }
        }

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
            $proposer->setChambre($this);
        }

        return $this;
    }

    public function removeProposer(Proposer $proposer): static
    {
        if ($this->proposers->removeElement($proposer)) {
            // set the owning side to null (unless already changed)
            if ($proposer->getChambre() === $this) {
                $proposer->setChambre(null);
            }
        }

        return $this;
    }

}
