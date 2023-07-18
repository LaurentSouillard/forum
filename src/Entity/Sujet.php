<?php

namespace App\Entity;

use App\Repository\SujetRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SujetRepository::class)]
class Sujet
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'text')]
    private $contenu;

    #[ORM\Column(type: 'datetime')]
    private $dateDeCreation;

    #[ORM\ManyToOne(targetEntity: Categorie::class, inversedBy: 'sujets')]
    private $categorie;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContenu(): ?string
    {
        return $this->contenu;
    }

    public function setContenu(string $contenu): self
    {
        $this->contenu = $contenu;

        return $this;
    }

    public function getDateDeCreation(): ?\DateTimeInterface
    {
        return $this->dateDeCreation;
    }

    public function setDateDeCreation(\DateTimeInterface $dateDeCreation): self
    {
        $this->dateDeCreation = $dateDeCreation;

        return $this;
    }

    public function getCategorie(): ?Categorie
    {
        return $this->categorie;
    }

    public function setCategorie(?Categorie $categorie): self
    {
        $this->categorie = $categorie;

        return $this;
    }
}
