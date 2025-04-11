<?php

namespace App\Entity;

use App\Repository\InterventionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InterventionRepository::class)]
#[ORM\Table(name: "intervention")]
class Intervention
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Produit::class)]
    #[ORM\JoinColumn(name: "produit_id", referencedColumnName: "id_produit", nullable: false)]
    private Produit $produit;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "intervenant_id", referencedColumnName: "id_User", nullable: false)]
    private User $intervenant;

    #[ORM\Column(type: "string", length: 50, nullable: true)]
    private ?string $systemeExploitation = null;

    #[ORM\Column(type: "string", length: 50, nullable: true)]
    private ?string $versionSe = null;

    #[ORM\Column(type: "date")]
    private \DateTimeInterface $dateIntervention;

    #[ORM\Column(type: "datetime", nullable: true, options: ["default" => "CURRENT_TIMESTAMP"])]
    private ?\DateTimeInterface $dateUpdate = null;

    #[ORM\Column(type: "string", length: 250, nullable: true)]
    private ?string $commentaire = null;

    #[ORM\Column(type: "boolean", nullable: true)]
    private ?bool $miseAJourWindows = null;

    #[ORM\Column(type: "boolean", nullable: true)]
    private ?bool $miseAJourPilotes = null;

    #[ORM\Column(type: "boolean", nullable: true)]
    private ?bool $autresLogiciels = null;

    #[ORM\Column(type: "string", length: 50, nullable: true)]
    private ?string $codeBarre = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $categorie = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $marque = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $modele = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $numeroSerie = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $cpu = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $frequenceCpu = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $ram = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $typeRam = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $stockage = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $typeStockage = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $carteGraphique = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $memoireVideo = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $intervenantNom = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $codeEtagere;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $statut;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $pdfFilePath = null;

    public function __construct()
    {
        $this->dateIntervention = new \DateTime();
    }

    public function getId(): ?int { return $this->id; }

    public function getProduit(): Produit { return $this->produit; }
    public function setProduit(Produit $produit): self { $this->produit = $produit; return $this; }

    public function getIntervenant(): ?User { return $this->intervenant; }
    public function setIntervenant(User $intervenant): self { $this->intervenant = $intervenant; return $this; }

    public function getSystemeExploitation(): ?string { return $this->systemeExploitation; }
    public function setSystemeExploitation(?string $systemeExploitation): self { $this->systemeExploitation = $systemeExploitation; return $this; }

    public function getVersionSe(): ?string { return $this->versionSe; }
    public function setVersionSe(?string $versionSe): self { $this->versionSe = $versionSe; return $this; }

    public function getDateIntervention(): \DateTimeInterface { return $this->dateIntervention; }
    public function setDateIntervention(\DateTimeInterface $dateIntervention): self { $this->dateIntervention = $dateIntervention; return $this; }

    public function getDateUpdate(): ?\DateTimeInterface { return $this->dateUpdate; }
    public function setDateUpdate(?\DateTimeInterface $dateUpdate): self { $this->dateUpdate = $dateUpdate; return $this; }

    public function getCommentaire(): ?string { return $this->commentaire; }
    public function setCommentaire(?string $commentaire): self { $this->commentaire = $commentaire; return $this; }

    public function getMiseAJourWindows(): ?bool { return $this->miseAJourWindows; }
    public function setMiseAJourWindows(?bool $miseAJourWindows): self { $this->miseAJourWindows = $miseAJourWindows; return $this; }

    public function getMiseAJourPilotes(): ?bool { return $this->miseAJourPilotes; }
    public function setMiseAJourPilotes(?bool $miseAJourPilotes): self { $this->miseAJourPilotes = $miseAJourPilotes; return $this; }

    public function getAutresLogiciels(): ?bool { return $this->autresLogiciels; }
    public function setAutresLogiciels(?bool $autresLogiciels): self { $this->autresLogiciels = $autresLogiciels; return $this; }

    public function getCodeBarre(): ?string { return $this->codeBarre; }
    public function setCodeBarre(?string $codeBarre): self { $this->codeBarre = $codeBarre; return $this; }

    public function getCategorie(): ?string { return $this->categorie; }
    public function setCategorie(?string $categorie): self { $this->categorie = $categorie; return $this; }

    public function getMarque(): ?string { return $this->marque; }
    public function setMarque(?string $marque): self { $this->marque = $marque; return $this; }

    public function getModele(): ?string { return $this->modele; }
    public function setModele(?string $modele): self { $this->modele = $modele; return $this; }

    public function getNumeroSerie(): ?string { return $this->numeroSerie; }
    public function setNumeroSerie(?string $numeroSerie): self { $this->numeroSerie = $numeroSerie; return $this; }

    public function getCpu(): ?string { return $this->cpu; }
    public function setCpu(?string $cpu): self { $this->cpu = $cpu; return $this; }

    public function getFrequenceCpu(): ?string { return $this->frequenceCpu; }
    public function setFrequenceCpu(?string $frequenceCpu): self { $this->frequenceCpu = $frequenceCpu; return $this; }

    public function getRam(): ?string { return $this->ram; }
    public function setRam(?string $ram): self { $this->ram = $ram; return $this; }

    public function getTypeRam(): ?string { return $this->typeRam; }
    public function setTypeRam(?string $typeRam): self { $this->typeRam = $typeRam; return $this; }

    public function getStockage(): ?string { return $this->stockage; }
    public function setStockage(?string $stockage): self { $this->stockage = $stockage; return $this; }

    public function getTypeStockage(): ?string { return $this->typeStockage; }
    public function setTypeStockage(?string $typeStockage): self { $this->typeStockage = $typeStockage; return $this; }

    public function getCarteGraphique(): ?string { return $this->carteGraphique; }
    public function setCarteGraphique(?string $carteGraphique): self { $this->carteGraphique = $carteGraphique; return $this; }

    public function getMemoireVideo(): ?string { return $this->memoireVideo; }
    public function setMemoireVideo(?string $memoireVideo): self { $this->memoireVideo = $memoireVideo; return $this; }

    public function getIntervenantNom(): ?string { return $this->intervenantNom; }
    public function setIntervenantNom(?string $intervenantNom): self { $this->intervenantNom = $intervenantNom; return $this; }

    public function getPdfFilePath(): ?string { return $this->pdfFilePath; }
    public function setPdfFilePath(?string $pdfFilePath): self { $this->pdfFilePath = $pdfFilePath; return $this; }

    public function getCodeEtagere(): ?string { return $this->codeEtagere;}
    public function setCodeEtagere(?string $codeEtagere): self { $this->codeEtagere = $codeEtagere; return $this;}

    public function getStatut(): ?string { return $this->statut;}
    public function setStatut(?string $statut): self { $this->statut = $statut; return $this;}

}
