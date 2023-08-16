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

    public function getImportOverview(?string $city = null): array {
        $wheres = ['1=1'];

        if ($city !== null) {
            $wheres[] = 'city = :city';
        }

        $whereCondition = implode(' AND ', $wheres);

        $sql = <<<SQL
    SELECT
        ymd,
        city,
        SUM(1) AS units,
        SUM(CAST(JSON_EXTRACT(value, "$.Bruttoleistung") AS FLOAT)) AS gross,
        SUM(CAST(JSON_EXTRACT(value, "$.Nettonennleistung") AS FLOAT)) AS net
    FROM
        import_data AS id
        CROSS JOIN JSON_TABLE(id.snapshot, '$[*]' COLUMNS (value JSON PATH '$')) jsontable
    WHERE
        {$whereCondition}
    GROUP BY
        ymd, city
    ORDER BY
        ymd DESC
    ;
SQL;

        $em = $this->getEntityManager();
        $stmt = $em->getConnection()->prepare($sql);

        if ($city !== null) {
            $stmt->bindValue('city', $city);
        }

        return $stmt->executeQuery()->fetchAllAssociative();
    }
}
