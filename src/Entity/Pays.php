<?php

namespace App\Entity;

use App\Repository\PaysRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PaysRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Pays
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 65)]
    private ?string $nom = null;

    #[ORM\Column(length: 40)]
    private ?string $drapeau = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getDrapeau(): ?string
    {
        return $this->drapeau;
    }

    public function setDrapeau(string $drapeau): self
    {
        $this->drapeau = $drapeau;

        return $this;
    }

    #[ORM\PostRemove]
    public function deleteDrapeau(): void
    {
        if (file_exists(__DIR__ .'/../../public/assets/img/upload/drapeau/' . $this->drapeau)) {
            unlink(__DIR__ .'/../../public/assets/img/upload/drapeau/' . $this->drapeau);
        }
    }
}
