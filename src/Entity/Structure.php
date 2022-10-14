<?php

namespace App\Entity;

use App\Entity\User;
use App\Repository\StructureRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StructureRepository::class)]
class Structure extends User
{
    #[ORM\ManyToOne(inversedBy: 'structures')]
    #[ORM\JoinColumn(onDelete: 'cascade')]
    private ?Franchise $franchise = null;

    public function getFranchise(): ?Franchise
    {
        return $this->franchise;
    }

    public function setFranchise(?Franchise $franchise): self
    {
        $this->franchise = $franchise;

        return $this;
    }
}
