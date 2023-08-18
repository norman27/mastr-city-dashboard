<?php

namespace App\Entity;

use App\Repository\ImportDataRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ImportDataRepository::class)]
class ImportData
{
    #[ORM\Id]
    #[ORM\Column]
    public ?int $ymd = null;

    #[ORM\Id]
    #[ORM\Column(length: 255)]
    public ?string $city = null;

    #[ORM\Column]
    public array $snapshot = [];
}
