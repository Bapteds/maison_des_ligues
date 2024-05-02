<?php

namespace App\Entity;

use App\Repository\VacationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VacationRepository::class)]
class Vacation
{
    /**
     * Id Vacation
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * Les ateliers de Vacation
     */
    #[ORM\ManyToOne(inversedBy: 'vacations', targetEntity: Atelier::class)]
    private ?Atelier $ateliers = null;

    /**
     * Date heure début Vacation
     */
    #[ORM\Column(length: 255)]
    private ?string $dateHeureDebut = null;

    /**
     * Date heure fin Vacation
     */
    #[ORM\Column(length: 255)]
    private ?string $dateHeureFin = null;

    /**
     * Créer une instance Vacation
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Retourne la date en heure de début de Vacation
     */
    public function getDateHeureDebut(): ?string
    {
        return $this->dateHeureDebut;
    }

    /**
     * Retourne la date
     *
     * @return string|null
     */
    public function getDateDebut(): ?string
    {
        return strtok($this->dateHeureDebut, ' ');
    }

    /**
     * Retourne l'heure du début
     *
     * @return string|null
     */
    public function getHeureDebut(): ?string
    {
        return substr($this->dateHeureDebut, 11, 5);
    }
    /**
     * Retourne la date
     *
     * @return string|null
     */
    public function getDateFin(): ?string
    {
        return strtok($this->dateHeureFin, ' ');
    }

    /**
     * Retourne l'heure du fin
     *
     * @return string|null
     */
    public function getHeureFin(): ?string
    {
        return substr($this->dateHeureFin, 11, 5);
    }

    /**
     * Définit la date en heure de début de Vacation
     */
    public function setDateHeureDebut(string $dateHeureDebut): static
    {
        $this->dateHeureDebut = $dateHeureDebut;

        return $this;
    }

    /**
     * Retourne la date en heure de fin de Vacation
     */
    public function getDateHeureFin(): ?string
    {
        return $this->dateHeureFin;
    }

    /**
     * Définit la date en heure de fin de Vacation
     */
    public function setDateHeureFin(string $dateHeureFin): static
    {
        $this->dateHeureFin = $dateHeureFin;

        return $this;
    }

    /**
     * Retourne les ateliers
     */
    public function getAtelier()
    {
        return $this->ateliers;
    }

    /**
     * Assigne l'Atelier à cette Vacation.
     */
    public function setAtelier(Atelier $atelier): self
    {
        $this->ateliers = $atelier;
        $atelier->setVacation($this);
        return $this;
    }

    public function getPeriodeVacation()
    {
        $periode = (substr($this->dateHeureDebut, 5, -3)) . ' -> ' . (substr($this->dateHeureFin, 5, -3));
        return $periode;
    }
}
