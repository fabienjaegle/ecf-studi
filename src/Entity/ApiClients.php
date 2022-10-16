<?php

namespace App\Entity;

use App\Repository\ApiClientsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: "api_clients")]
#[ORM\Entity(repositoryClass: ApiClientsRepository::class)]
class ApiClients
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $client_id = null;

    #[ORM\Column(length: 255)]
    private ?string $client_secret = null;

    #[ORM\Column(length: 100)]
    private ?string $client_name = null;

    #[ORM\Column(length: 1)]
    private ?string $active = null;

    #[ORM\Column(length: 50)]
    private ?string $short_description = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $full_description = null;

    #[ORM\Column(length: 200)]
    private ?string $logo_url = null;

    #[ORM\Column(length: 100)]
    private ?string $url = null;

    #[ORM\Column(length: 100)]
    private ?string $dpo = null;

    #[ORM\Column(length: 200)]
    private ?string $technical_contact = null;

    #[ORM\Column(length: 200)]
    private ?string $commercial_contact = null;

    #[ORM\OneToOne(targetEntity: ApiClientsGrants::class, mappedBy: 'client')]
    private $grants;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGrants(): ?ApiClientsGrants
    {
        return $this->grants;
    }

    public function setGrants(ApiClientsGrants $grants): self
    {
        $this->grants = $grants;

        return $this;
    }

    public function getClientSecret(): ?string
    {
        return $this->client_secret;
    }

    public function setClientSecret(string $client_secret): self
    {
        $this->client_secret = $client_secret;

        return $this;
    }

    public function getClientName(): ?string
    {
        return $this->client_name;
    }

    public function setClientName(string $client_name): self
    {
        $this->client_name = $client_name;

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

    public function getShortDescription(): ?string
    {
        return $this->short_description;
    }

    public function setShortDescription(string $short_description): self
    {
        $this->short_description = $short_description;

        return $this;
    }

    public function getFullDescription(): ?string
    {
        return $this->full_description;
    }

    public function setFullDescription(string $full_description): self
    {
        $this->full_description = $full_description;

        return $this;
    }

    public function getLogoUrl(): ?string
    {
        return $this->logo_url;
    }

    public function setLogoUrl(string $logo_url): self
    {
        $this->logo_url = $logo_url;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getDpo(): ?string
    {
        return $this->dpo;
    }

    public function setDpo(string $dpo): self
    {
        $this->dpo = $dpo;

        return $this;
    }

    public function getTechnicalContact(): ?string
    {
        return $this->technical_contact;
    }

    public function setTechnicalContact(string $technical_contact): self
    {
        $this->technical_contact = $technical_contact;

        return $this;
    }

    public function getCommercialContact(): ?string
    {
        return $this->commercial_contact;
    }

    public function setCommercialContact(string $commercial_contact): self
    {
        $this->commercial_contact = $commercial_contact;

        return $this;
    }
}
