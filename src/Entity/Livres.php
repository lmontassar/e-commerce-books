<?php

namespace App\Entity;

use App\Repository\LivresRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LivresRepository::class)]
class Livres
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\Column(length: 255)]
    private ?string $ISBN = null;

    #[ORM\Column(length: 255)]
    private ?string $Editeur = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $editedAt = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $resume = null;

    #[ORM\Column]
    private ?float $prix = null;

    #[ORM\Column]
    private ?int $qte = 0;

    #[ORM\Column(length: 255)]
    private ?string $auteur = "";

    #[ORM\ManyToOne(inversedBy: 'livres')]
    private ?Categories $categorie = null;

    #[ORM\OneToMany(targetEntity: CommandeRelation::class, mappedBy: 'Livre')]
    private Collection $commandeRelations;


    public function __construct()
    {
        $this->commandeRelations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getISBN(): ?string
    {
        return $this->ISBN;
    }

    public function setISBN(string $ISBN): static
    {
        $this->ISBN = $ISBN;
        return $this;
    }

    public function getEditeur(): ?string
    {
        return $this->Editeur;
    }

    public function setEditeur(string $Editeur): static
    {
        $this->Editeur = $Editeur;
        return $this;
    }

    public function getEditedAt(): ?\DateTimeImmutable
    {
        return $this->editedAt;
    }

    public function setEditedAt(\DateTimeImmutable $editedAt): static
    {
        $this->editedAt = $editedAt;
        return $this;
    }

    public function getResume(): ?string
    {
        return $this->resume;
    }

    public function setResume(?string $resume): static
    {
        $this->resume = $resume;
        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): static
    {
        $this->prix = $prix;
        return $this;
    }

    public function getQte(): ?int
    {
        return $this->qte;
    }

    public function setQte(int $qte): static
    {
        $this->qte = $qte;

        return $this;
    }

    public function getAuteur(): ?string
    {
        return $this->auteur;
    }

    public function setAuteur(string $auteur): static
    {
        $this->auteur = $auteur;

        return $this;
    }

    public function getCategorie(): ?categories
    {
        return $this->categorie;
    }

    public function setCategorie(?categories $categorie): static
    {
        $this->categorie = $categorie;

        return $this;
    }

    /**
     * @return Collection<int, CommandeRelation>
     */
    public function getCommandeRelations(): Collection
    {
        return $this->commandeRelations;
    }

    public function addCommandeRelation(CommandeRelation $commandeRelation): static
    {
        if (!$this->commandeRelations->contains($commandeRelation)) {
            $this->commandeRelations->add($commandeRelation);
            $commandeRelation->setLivre($this);
        }

        return $this;
    }

    public function removeCommandeRelation(CommandeRelation $commandeRelation): static
    {
        if ($this->commandeRelations->removeElement($commandeRelation)) {
            // set the owning side to null (unless already changed)
            if ($commandeRelation->getLivre() === $this) {
                $commandeRelation->setLivre(null);
            }
        }

        return $this;
    }

}
