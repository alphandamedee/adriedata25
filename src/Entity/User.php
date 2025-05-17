<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use App\Entity\Role;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: "User")]
#[ORM\HasLifecycleCallbacks]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "id_user", type: "integer")]
    private ?int $id = null;

    #[ORM\Column(name: "nom", type: "string", length: 50)]
    private string $nomUser;

    #[ORM\Column(name: "prenom", type: "string", length: 50)]
    private string $prenom;

    #[ORM\Column(name: "email", type: "string", length: 100, unique: true)]
    private string $mailUser;

    #[ORM\Column(name: "password", type: "string", length: 255)]
    private string $password;

    #[ORM\Column(name: "attribut", type: "string", length: 50, nullable: true)]
    private ?string $attrUser = null;

    #[ORM\Column(name: "date_Creation", type: "datetime")]
    private \DateTimeInterface $dateCreation;

    #[ORM\Column(name: "date_Update", type: "datetime")]
    private \DateTimeInterface $dateUpdate;

    #[ORM\Column(type: "json")]
    private array $roles = []; // 🔹 Ajout de la propriété roles pour éviter l'erreur

    #[ORM\ManyToOne(targetEntity: Role::class)]
    #[ORM\JoinColumn(name: "role_id", referencedColumnName: "role_id", nullable: false)]
    private Role $role; // 🔹 Ajout de la relation ManyToOne avec l'entité Role

    #[ORM\Column(name: "photo", type: "string", length: 255, nullable: true)]
    #[Assert\File(maxSize: "2M", mimeTypes: ["image/jpeg", "image/png"])]
    private ?string $photo = null;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $resetToken = null;

    public function getResetToken(): ?string
    {
        return $this->resetToken;
    }

    public function setResetToken(?string $resetToken): self
    {
        $this->resetToken = $resetToken;
        return $this;
    }


    // Implémentation de UserInterface
    public function getUserIdentifier(): string
    {
        return $this->mailUser;
    }

    public function eraseCredentials(): void
    {
        // Supprime les données sensibles après authentification
    }

    // Implémentation de PasswordAuthenticatedUserInterface
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): self
    {
        $this->photo = $photo;
        return $this;
    }

    // Gestion des rôles
   
    public function getRoles(): array
    {
        $roleName = $this->role->getRoleName(); // Ex: "Administrateur"

        return match ($roleName) {
            'Administrateur' => ['ROLE_ADMIN'],
            'Technicien'     => ['ROLE_TECHNICIEN'],
            'Bénévole'       => ['ROLE_BENEVOLE'],
            default          => ['ROLE_USER'],
        };

        // 🔹 Assure toujours qu'un utilisateur a "ROLE_USER"
        if (!in_array('ROLE_USER', $roles)) {
            $roles[] = 'ROLE_USER';
        }

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }

    // Getters et Setters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomUser(): string
    {
        return $this->nomUser;
    }

    public function setNomUser(string $nomUser): self
    {
        $this->nomUser = $nomUser;
        return $this;
    }

    public function getPrenom(): string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;
        return $this;
    }

    public function getMailUser(): string
    {
        return $this->mailUser;
    }
    
    public function getEmail(): string
    {
        return $this->mailUser;
    }

    public function setMailUser(string $mailUser): self
    {
        $this->mailUser = $mailUser;
        return $this;
    }

    public function getAttrUser(): ?string
    {
        return $this->attrUser;
    }

    public function setAttrUser(?string $attrUser): self
    {
        $this->attrUser = $attrUser;
        return $this;
    }

    public function getRole(): ?Role
    {
        return $this->role;
    }
    
    public function setRole(Role $role): self
    {
        $this->role = $role;
        return $this;
    }

    public function getDateCreation(): \DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function setDateCreation(\DateTimeInterface $dateCreation): self
    {
        $this->dateCreation = $dateCreation;
        return $this;
    }

    public function getDateUpdate(): \DateTimeInterface
    {
        return $this->dateUpdate;
    }

    public function setDateUpdate(\DateTimeInterface $dateUpdate): self
    {
        $this->dateUpdate = $dateUpdate;
        return $this;
    }

    
    public function __toString(): string
    {
        return $this->prenom . ' ' . $this->nomUser;
    }

    // Gestion automatique des dates de création et de mise à jour
    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        $this->dateCreation = new \DateTime();
        $this->dateUpdate = new \DateTime();
    }

    #[ORM\PreUpdate]
    public function onPreUpdate(): void
    {
        $this->dateUpdate = new \DateTime();
    }
}
