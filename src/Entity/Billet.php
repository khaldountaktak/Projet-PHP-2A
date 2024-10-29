<?php

namespace App\Entity;

use App\Repository\BilletRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BilletRepository::class)]
class Billet
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $pays = null;

    #[ORM\ManyToOne(inversedBy: 'billets', cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Album $album = null;

    #[ORM\Column(length: 255)]
    private ?string $valeur = null;

    #[ORM\Column(length: 255)]
    private ?string $DateApparition = null;

    /**
     * @var Collection<int, Exposition>
     */
    #[ORM\ManyToMany(targetEntity: Exposition::class, mappedBy: 'billets')]
    private Collection $expositions;

    public function __construct()
    {
        $this->expositions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPays(): ?string
    {
        return $this->pays;
    }

    public function setPays(string $pays): static
    {
        $this->pays = $pays;

        return $this;
    }

    public function getAlbum(): ?Album
    {
        return $this->album;
    }

    public function setAlbum(?Album $album): static
    {
        $this->album = $album;

        return $this;
    }

    public function getValeur(): ?string
    {
        return $this->valeur;
    }

    public function setValeur(string $valeur): static
    {
        $this->valeur = $valeur;

        return $this;
    }

    public function getDateApparition(): ?string
    {
        return $this->DateApparition;
    }

    public function setDateApparition(string $DateApparition): static
    {
        $this->DateApparition = $DateApparition;

        return $this;
    }

    /**
     * @return Collection<int, Exposition>
     */
    public function getExpositions(): Collection
    {
        return $this->expositions;
    }

    public function addExposition(Exposition $exposition): static
    {
        if (!$this->expositions->contains($exposition)) {
            $this->expositions->add($exposition);
            $exposition->addBillet($this);
        }

        return $this;
    }

    public function removeExposition(Exposition $exposition): static
    {
        if ($this->expositions->removeElement($exposition)) {
            $exposition->removeBillet($this);
        }

        return $this;
    }
}
