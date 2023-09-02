<?php

namespace App\Controller\Api;

use App\Repository\ImportDataRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class DiffController extends AbstractController
{
    /**
     * Find the difference between two days. First version only tells added or removed units
     */
    #[Route('/api/diff/{city}/{ymdNew}/{ymdOld}', name: 'app_api_diff')]
    public function diff(string $city, string $ymdNew, string $ymdOld, ImportDataRepository $importDataRepository): JsonResponse
    {
        $foundDiffs = [];

        $dataNew = $importDataRepository->findOneBy(
            ['ymd' => $ymdNew, 'city' => $city],
        );

        $dataOld = $importDataRepository->findOneBy(
            ['ymd' => $ymdOld, 'city' => $city],
        );

        $unitsNew = [];
        $unitsOld = [];
        foreach ($dataNew->snapshot as $value) {
            $unitsNew[$value['EinheitMastrNummer']] = $value;
        }
        foreach ($dataOld->snapshot as $value) {
            $unitsOld[$value['EinheitMastrNummer']] = $value;
        }

        foreach ($unitsNew as $unit) {
            if (! isset($unitsOld[$unit['EinheitMastrNummer']])) {
                $foundDiffs[] = [
                    'type' => 'added',
                    'unit' => $unit['EinheitMastrNummer'],
                    'gross' => $unit['Bruttoleistung'],
                    'net' => $unit['Nettonennleistung'],
                    'additional' => [],
                ];
            }
        }

        foreach ($unitsOld as $unit) {
            if (! isset($unitsNew[$unit['EinheitMastrNummer']])) {
                $foundDiffs[] = [
                    'type' => 'removed',
                    'unit' => $unit['EinheitMastrNummer'],
                    'gross' => $unit['Bruttoleistung'],
                    'net' => $unit['Nettonennleistung'],
                    'additional' => [],
                ];
            } else {
                $relevantChange = $this->getRelevantChange(
                    $unitsNew[$unit['EinheitMastrNummer']],
                    $unitsOld[$unit['EinheitMastrNummer']]
                );

                if (count($relevantChange) > 0) {
                    $foundDiffs[] = [
                        'type' => 'changed',
                        'unit' => $unit['EinheitMastrNummer'],
                        'gross' => $unit['Bruttoleistung'],
                        'net' => $unit['Nettonennleistung'],
                        'additional' => $relevantChange,
                    ];
                }
            }
        }

        return new JsonResponse($foundDiffs);
    }

    private function getRelevantChange($unitNew, $unitOld): array {
        $changes = [];
        $relevantAttributes = ['Bruttoleistung', 'Nettonennleistung', 'NetzbetreiberpruefungStatus'];

        foreach ($relevantAttributes as $attr) {
            if ($unitNew[$attr] !== $unitOld[$attr]) {
                $changes[] = $attr . '=>' . $unitNew[$attr];
            }
        }

        return $changes;
    }
}
