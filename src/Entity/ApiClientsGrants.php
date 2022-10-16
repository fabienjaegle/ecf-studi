<?php

namespace App\Entity;

use App\Repository\ApiClientsGrantsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: "api_clients_grants")]
#[ORM\Entity(repositoryClass: ApiClientsGrantsRepository::class)]
class ApiClientsGrants
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $install_id = null;

    #[ORM\Column(length: 1)]
    private ?string $active = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $perms = null;

    #[ORM\Column]
    private ?int $branch_id = null;

    #[ORM\OneToOne(targetEntity: ApiClients::class, inversedBy: 'grants')]
    #[ORM\JoinColumn(name: 'client_id', referencedColumnName: 'client_id')]
    private $client = null;

    //#[ORM\OneToMany(mappedBy: 'client_id', targetEntity: ApiInstallPerm::class, orphanRemoval: true)]
    //private Collection $apiInstallPerms;

    public function __construct()
    {
        //$this->apiInstallPerms = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getClient(): ?string
    {
        return $this->client;
    }

    public function setClient(string $client_id): self
    {
        $this->client = $client_id;

        return $this;
    }

    public function getInstallId(): ?string
    {
        return $this->install_id;
    }

    public function setInstallId(string $install_id): self
    {
        $this->install_id = $install_id;

        return $this;
    }

    public function getActive(): ?string
    {
        return $this->active;
    }

    public function setActive(string $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function getPerms(): ?string
    {
        return $this->perms;
    }

    public function setPerms(string $perms): self
    {
        $this->perms = $perms;

        return $this;
    }

    public function getBranchId(): ?int
    {
        return $this->branch_id;
    }

    public function setBranchId(int $branch_id): self
    {
        $this->branch_id = $branch_id;

        return $this;
    }

    public function getApiClients(): ?ApiClients
    {
        return $this->apiClients;
    }

    public function setApiClients(ApiClients $apiClients): self
    {
        $this->apiClients = $apiClients;

        return $this;
    }

    /**
     * @return Collection<int, ApiInstallPerm>
     */
    /*public function getApiInstallPerms(): Collection
    {
        return $this->apiInstallPerms;
    }

    public function addApiInstallPerm(ApiInstallPerm $apiInstallPerm): self
    {
        if (!$this->apiInstallPerms->contains($apiInstallPerm)) {
            $this->apiInstallPerms->add($apiInstallPerm);
            $apiInstallPerm->setClientGrants($this);
        }

        return $this;
    }

    public function removeApiInstallPerm(ApiInstallPerm $apiInstallPerm): self
    {
        $this->apiInstallPerms->removeElement($apiInstallPerm);

        return $this;
    }*/
}
