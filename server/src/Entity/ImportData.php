<?php

namespace App\Entity;

use App\Repository\ImportDataRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ImportDataRepository::class)]
class ImportData
{
    #[ORM\Id]
    #[ORM\Column]
    private ?int $ymd = null;

    #[ORM\Id]
    #[ORM\Column(length: 255)]
    private ?string $city = null;

    #[ORM\Column]
    private array $snapshot = [];

    public function getYmd(): ?int
    {
        return $this->ymd;
    }

    public function setYmd(int $ymd): static
    {
        $this->ymd = $ymd;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getSnapshot(): array
    {
        return $this->snapshot;
    }

    public function setSnapshot(array $snapshot): static
    {
        $this->snapshot = $snapshot;

        return $this;
    }
}
