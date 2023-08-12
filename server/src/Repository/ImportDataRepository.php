<?php

namespace App\Repository;

use App\Entity\ImportData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ImportData>
 *
 * @method ImportData|null find($id, $lockMode = null, $lockVersion = null)
 * @method ImportData|null findOneBy(array $criteria, array $orderBy = null)
 * @method ImportData[]    findAll()
 * @method ImportData[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImportDataRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ImportData::class);
    }
}
