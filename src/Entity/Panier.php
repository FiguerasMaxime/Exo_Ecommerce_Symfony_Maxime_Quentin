<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PanierRepository")
 */
class Panier
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     */
    private $date;

    /**
     * @ORM\Column(type="boolean")
     */
    private $etat = false;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\ContenuPanier", mappedBy="panier", cascade={"persist", "remove"})
     */
    private $contenuPanier;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\User", mappedBy="panier")
     */
    private $utilisateur;

    public function __construct()
    {
        $this->utilisateur = new ArrayCollection();
        $this->date = new \DateTime('now');
    }


    public function getId(): ?int
    {
        return $this->id;
    }


    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getEtat(): ?bool
    {
        return $this->etat;
    }

    public function setEtat(bool $etat): self
    {
        $this->etat = $etat;

        return $this;
    }

    public function getContenuPanier(): ?ContenuPanier
    {
        return $this->contenuPanier;
    }

    // public function setIDPanier(int $contenuPanier): self {
    //     $this->contenuPanier = $contenuPanier;

    //     return $this;
    // }
    public function setContenuPanier(?ContenuPanier $contenuPanier): self
    {
        $this->contenuPanier = $contenuPanier;

        // set (or unset) the owning side of the relation if necessary
        $newPanier = null === $contenuPanier ? null : $this;
        if ($contenuPanier->getPanier() !== $newPanier) {
            $contenuPanier->setPanier($newPanier);
        }

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getUtilisateur(): Collection
    {
        return $this->utilisateur;
    }

    public function addUtilisateur(User $utilisateur): self
    {
        if (!$this->utilisateur->contains($utilisateur)) {
            $this->utilisateur[] = $utilisateur;
            $utilisateur->setPanier($this);
        }

        return $this;
    }

    public function removeUtilisateur(User $utilisateur): self
    {
        if ($this->utilisateur->contains($utilisateur)) {
            $this->utilisateur->removeElement($utilisateur);
            // set the owning side to null (unless already changed)
            if ($utilisateur->getPanier() === $this) {
                $utilisateur->setPanier(null);
            }
        }

        return $this;
    }


}
