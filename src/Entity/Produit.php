<?php

namespace App\Entity;

use App\Repository\ProduitRepository;
use App\Entity\CategorieProduit;
use Doctrine\ORM\Mapping as ORM;
use DateTimeImmutable;

#[ORM\Entity(repositoryClass: ProduitRepository::class)]
#[ORM\Table(name: "produit")]
#[ORM\HasLifecycleCallbacks]
class Produit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "id_produit", type: "integer")]
    private ?int $idProduit = null;

    #[ORM\Column(name: "code_barre", type: "string", length: 100, nullable: true)]
    private ?string $codeBarre = null;

    #[ORM\ManyToOne(targetEntity: CategorieProduit::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?CategorieProduit $categorie = null;

    #[ORM\Column(name: "marque", type: "string", length: 100, nullable: true)]
    private ?string $marque = null;

    #[ORM\Column(name: "modele", type: "string", length: 100, nullable: true)]
    private ?string $modele = null;

    #[ORM\Column(name: "stockage", type: "integer", nullable: true)]
    private ?int $stockage = null;

    #[ORM\Column(name: "type_stockage", type: "string", length: 50, nullable: true)]
    private ?string $typeStockage = null;

    #[ORM\Column(name: "ram", type: "string", length: 50, nullable: true)]
    private ?string $ram = null;

    #[ORM\ManyToOne(targetEntity: TypeRam::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?TypeRam $typeRam = null;

    #[ORM\Column(name: "num_serie", type: "string", length: 50, nullable: true)]
    private ?string $numeroSerie = null;

    #[ORM\Column(name: "statut", type: "string", length: 50, nullable: true)]
    private ?string $statut = null;

    #[ORM\Column(name: "code_etagere" , type: 'string', length: 10, nullable: true)]
    private ?string $codeEtagere = null; // Nouvelle colonne pour code étagère

    #[ORM\Column(name: "cpu", type: "string", length: 50, nullable: true)]
    private ?string $cpu = null;

    #[ORM\Column(name: "frequence_cpu", type: "string", length: 50, nullable: true)]
    private ?string $frequenceCpu = null;

    #[ORM\Column(name: "carte_graphique", type: "string", length: 50, nullable: true)]
    private ?string $carteGraphique = null;

    #[ORM\Column(name: "memoire_video", type: "string", length: 50, nullable: true)]
    private ?string $memoireVideo = null;

    #[ORM\Column(name: "date_ajout", type: "datetime", nullable: true)]
    private ?\DateTimeInterface $dateAjout = null;

    #[ORM\Column(name: "mise_a_jour_windows", type: "datetime", nullable: true)]
    private ?\DateTimeInterface $miseAJourWindows = null;

    #[ORM\Column(name: "mise_a_jour_pilotes", type: "datetime", nullable: true)]
    private ?\DateTimeInterface $miseAJourPilotes = null;

    #[ORM\Column(name: "autres_logiciels", type: "datetime", nullable: true)]
    private ?\DateTimeInterface $autresLogiciels = null;

    #[ORM\Column(name: "taille", type: "string", length: 50, nullable: true)]
    private ?string $taille = null;

    
    
    

    // Getters et setters

    public function getDateAjout(): ?\DateTimeInterface
    {
        return $this->dateAjout;
    }

    public function setDateAjout(\DateTimeInterface $dateAjout): self
    {
        $this->dateAjout = $dateAjout;

        return $this;
    }

    public function getIdProduit(): ?int
    {
        return $this->idProduit;
    }

    public function getCodeBarre(): ?string
    {
        return $this->codeBarre;
    }

    public function setCodeBarre(?string $codeBarre): self
    {
        $this->codeBarre = $codeBarre;
        return $this;
    }

    public function getCategorie(): ?CategorieProduit
    {
        return $this->categorie;
    }

    public function setCategorie(?CategorieProduit $categorie): self
    {
        $this->categorie = $categorie;
        return $this;
    }

    public function getMarque(): ?string
    {
        return $this->marque;
    }

    public function setMarque(?string $marque): self
    {
        $this->marque = $marque;
        return $this;
    }

    public function getModele(): ?string
    {
        return $this->modele;
    }

    public function setModele(?string $modele): self
    {
        $this->modele = $modele;
        return $this;
    }

    public function getTaille(): ?string
    {
        return $this->taille;
    }

    public function setTaille(?string $taille): self
    {
        $this->taille = $taille;
        return $this;
    }

    public function getStockage(): ?int
    {
        return $this->stockage;
    }

    public function setStockage(?int $stockage): self
    {
        $this->stockage = $stockage;
        return $this;
    }

    public function getTypeStockage(): ?string
    {
        return $this->typeStockage;
    }

    public function setTypeStockage(?string $typeStockage): self
    {
        $this->typeStockage = $typeStockage;
        return $this;
    }

    public function getRam(): ?string
    {
        return $this->ram;
    }

    public function setRam(?string $ram): self
    {
        $this->ram = $ram;
        return $this;
    }

    public function getTypeRam(): ?TypeRam
    {
        return $this->typeRam;
    }

    public function setTypeRam(?TypeRam $typeRam): self
    {
        $this->typeRam = $typeRam;
        return $this;
    }

    public function getNumeroSerie(): ?string
    {
        return $this->numeroSerie;
    }

    public function setNumeroSerie(?string $numeroSerie): self
    {
        $this->numeroSerie = $numeroSerie;
        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(?string $statut): self
    {
        $this->statut = $statut;
        return $this;
    }

    public function getCodeEtagere(): ?string
    {
        return $this->codeEtagere;
    }

    public function setCodeEtagere(?string $codeEtagere): self
    {
        $this->codeEtagere = $codeEtagere;
        return $this;
    }

    public function getCpu(): ?string
    {
        return $this->cpu;
    }

    public function setCpu(?string $cpu): self
    {
        $this->cpu = $cpu;
        return $this;
    }

    public function getFrequenceCpu(): ?string
    {
        return $this->frequenceCpu;
    }

    public function setFrequenceCpu(?string $frequenceCpu): self
    {
        $this->frequenceCpu = $frequenceCpu;
        return $this;
    }

    public function getCarteGraphique(): ?string
    {
        return $this->carteGraphique;
    }

    public function setCarteGraphique(?string $carteGraphique): self
    {
        $this->carteGraphique = $carteGraphique;
        return $this;
    }

    public function getMemoireVideo(): ?string
    {
        return $this->memoireVideo;
    }

    public function setMemoireVideo(?string $memoireVideo): self
    {
        $this->memoireVideo = $memoireVideo;
        return $this;
    }

    public function getMiseAJourWindows(): ?\DateTimeInterface
    {
        return $this->miseAJourWindows;
    }

    public function setMiseAJourWindows(?\DateTimeInterface $miseAJourWindows): self
    {
        $this->miseAJourWindows = $miseAJourWindows;
        return $this;
    }

    public function getMiseAJourPilotes(): ?\DateTimeInterface
    {
        return $this->miseAJourPilotes;
    }

    public function setMiseAJourPilotes(?\DateTimeInterface $miseAJourPilotes): self
    {
        $this->miseAJourPilotes = $miseAJourPilotes;
        return $this;
    }

    public function getAutresLogiciels(): ?\DateTimeInterface
    {
        return $this->autresLogiciels;
    }

    public function setAutresLogiciels(?\DateTimeInterface $autresLogiciels): self
    {
        $this->autresLogiciels = $autresLogiciels;
        return $this;
    }

    #[ORM\PrePersist]
    public function setDateAjoutAutomatically(): void
    {
        if ($this->dateAjout === null) {
            $this->dateAjout = new DateTimeImmutable();
        }
    }
}
