<?php

namespace App\Entity;

use App\Repository\ProposerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProposerRepository::class)]
class Proposer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?float $tarifNuite = null;

    #[ORM\ManyToOne(inversedBy: 'proposers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?hotel $hotel = null;

    #[ORM\ManyToOne(inversedBy: 'proposers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?chambre $chambre = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTarifNuite(): ?float
    {
        return $this->tarifNuite;
    }

    public function setTarifNuite(float $tarifNuite): static
    {
        $this->tarifNuite = $tarifNuite;

        return $this;
    }

    public function getHotel(): ?hotel
    {
        return $this->hotel;
    }

    public function setHotel(?hotel $hotel): static
    {
        $this->hotel = $hotel;

        return $this;
    }

    public function getChambre(): ?chambre
    {
        return $this->chambre;
    }

    public function setChambre(?chambre $chambre): static
    {
        $this->chambre = $chambre;

        return $this;
    }





}
