<?php

namespace App\Entity;

use App\Repository\CommandeRelationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommandeRelationRepository::class)]
class CommandeRelation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $Qte = null;

    #[ORM\ManyToOne(inversedBy: 'commandeRelations')]
    private ?Commande $Commande = null;

    #[ORM\ManyToOne(inversedBy: 'commandeRelations')]
    private ?Livres $Livre = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQte(): ?int
    {
        return $this->Qte;
    }

    public function setQte(int $Qte): static
    {
        $this->Qte = $Qte;

        return $this;
    }

    public function getCommande(): ?Commande
    {
        return $this->Commande;
    }

    public function setCommande(?Commande $Commande): static
    {
        $this->Commande = $Commande;

        return $this;
    }

    public function getLivre(): ?Livres
    {
        return $this->Livre;
    }

    public function setLivre(?Livres $Livre): static
    {
        $this->Livre = $Livre;

        return $this;
    }
}
