<?php

namespace App\Entity;

use App\Entity\User;
use App\Repository\AdminRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AdminRepository::class)]
class Admin extends User
{
    #[ORM\OneToMany(mappedBy: 'domain', targetEntity: Franchise::class)]
    private Collection $franchises;

    public function __construct()
    {
        $this->franchises = new ArrayCollection();
    }

    /**
     * @return Collection<int, Franchise>
     */
    public function getFranchises(): Collection
    {
        return $this->franchises;
    }

    public function addFranchise(Franchise $franchise): self
    {
        if (!$this->franchises->contains($franchise)) {
            $this->franchises->add($franchise);
            $franchise->setDomain($this);
        }

        return $this;
    }

    public function removeFranchise(Franchise $franchise): self
    {
        if ($this->franchises->removeElement($franchise)) {
            // set the owning side to null (unless already changed)
            if ($franchise->getDomain() === $this) {
                $franchise->setDomain(null);
            }
        }

        return $this;
    }
}