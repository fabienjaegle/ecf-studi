<?php

namespace App\Entity;

use App\Entity\User;
use App\Repository\FranchiseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FranchiseRepository::class)]
class Franchise extends User
{
    #[ORM\OneToMany(mappedBy: 'franchise', targetEntity: Structure::class)]
    private Collection $structures;

    #[ORM\ManyToOne(inversedBy: 'franchises')]
    private ?Admin $domain = null;

    public function __construct()
    {
        $this->structures = new ArrayCollection();
    }

    /**
     * @return Collection<int, Structure>
     */
    public function getStructures(): Collection
    {
        return $this->structures;
    }

    public function addStructure(Structure $structure): self
    {
        if (!$this->structures->contains($structure)) {
            $this->structures->add($structure);
            $structure->setFranchise($this);
        }

        return $this;
    }

    public function removeStructure(Structure $structure): self
    {
        if ($this->structures->removeElement($structure)) {
            // set the owning side to null (unless already changed)
            if ($structure->getFranchise() === $this) {
                $structure->setFranchise(null);
            }
        }

        return $this;
    }

    public function getDomain(): ?Admin
    {
        return $this->domain;
    }

    public function setDomain(?Admin $domain): self
    {
        $this->domain = $domain;

        return $this;
    }
}
