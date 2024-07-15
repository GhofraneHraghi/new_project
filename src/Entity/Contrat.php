<?php

namespace App\Entity;

use App\Repository\ContratRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ContratRepository::class)]
class Contrat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $datDebut = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $datFin = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\Column(length: 255)]
    private ?string $nameClient = null;

    #[ORM\ManyToOne(inversedBy: 'contrats')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDatDebut(): ?\DateTimeImmutable
    {
        return $this->datDebut;
    }

    public function setDatDebut(\DateTimeImmutable $datDebut): static
    {
        $this->datDebut = $datDebut;

        return $this;
    }

    public function getDatFin(): ?\DateTimeImmutable
    {
        return $this->datFin;
    }

    public function setDatFin(\DateTimeImmutable $datFin): static
    {
        $this->datFin = $datFin;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getNameClient(): ?string
    {
        return $this->nameClient;
    }

    public function setNameClient(string $nameClient): static
    {
        $this->nameClient = $nameClient;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }
}
