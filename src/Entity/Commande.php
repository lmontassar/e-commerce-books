<?php

namespace App\Entity;

use App\Repository\CommandeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommandeRepository::class)]
class Commande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $DateCommandeAt = null;

    #[ORM\Column]
    private ?int $etat = null;

    #[ORM\ManyToOne(inversedBy: 'commandes')]
    private ?User $Client = null;

    #[ORM\OneToMany(targetEntity: CommandeRelation::class, mappedBy: 'Commande')]
    private Collection $commandeRelations;

    #[ORM\Column(nullable: true)]
    private ?bool $IsPayed = null;

    public function __construct()
    {
        $this->commandeRelations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateCommandeAt(): ?\DateTimeImmutable
    {
        return $this->DateCommandeAt;
    }

    public function setDateCommandeAt(\DateTimeImmutable $DateCommandeAt): static
    {
        $this->DateCommandeAt = $DateCommandeAt;

        return $this;
    }

    public function getEtat(): ?int
    {
        return $this->etat;
    }

    public function setEtat(int $etat): static
    {
        $this->etat = $etat;

        return $this;
    }

    public function getClient(): ?User
    {
        return $this->Client;
    }

    public function setClient(?User $Client): static
    {
        $this->Client = $Client;

        return $this;
    }

    /**
     * @return Collection<int, CommandeRelation>
     */
    public function getCommandeRelations(): Collection
    {
        return $this->commandeRelations;
    }

    public function removeAllCommandeRelation(): static
    {
        $this->commandeRelations = new ArrayCollection();
        return $this;
    }

    public function addCommandeRelation(CommandeRelation $commandeRelation): static
    {
        if (!$this->commandeRelations->contains($commandeRelation)) {
            $this->commandeRelations->add($commandeRelation);
            $commandeRelation->setCommande($this);
        }

        return $this;
    }

    public function removeCommandeRelation(CommandeRelation $commandeRelation): static
    {
        if ($this->commandeRelations->removeElement($commandeRelation)) {
            // set the owning side to null (unless already changed)
            if ($commandeRelation->getCommande() === $this) {
                $commandeRelation->setCommande(null);
            }
        }

        return $this;
    }

    public function isIsPayed(): ?bool
    {
        return $this->IsPayed;
    }

    public function setIsPayed(?bool $IsPayed): static
    {
        $this->IsPayed = $IsPayed;

        return $this;
    }
}
