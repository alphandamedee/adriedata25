<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "role")] // Renommé pour éviter les conflits SQL
class Role
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "role_id", type: "integer")]
    private ?int $idRole = null;

    #[ORM\Column(name: "role_name", type: "string", length: 50, unique: true)] // Renommé en "role_name"
    private string $roleName;

    // Getters et setters

    public function getIdRole(): ?int
    {
        return $this->idRole;
    }

    public function getRoleName(): string
    {
        return $this->roleName;
    }

    public function setRoleName(string $roleName): self
    {
        $this->roleName = $roleName;
        return $this;
    }
    public function __toString(): string
    {
        return $this->roleName;
    }
}
