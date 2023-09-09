<?php

namespace App\Controller\Api;

use App\Repository\ImportDataRepository;
use stdClass;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class QualityController extends AbstractController
{
    /**
     * Return a list of unplausible entries
     */
    #[Route('/api/quality/{city}', name: 'app_api_quality')]
    public function netzList(string $city, ImportDataRepository $importDataRepository): JsonResponse
    {
        return new JsonResponse();
    }

    /**
     * Return a list of unplausible entries
     */
    #[Route('/api/quality/invalid/{city}', name: 'app_api_quality_invalid')]
    public function invalidList(string $city, ImportDataRepository $importDataRepository): JsonResponse
    {
        $data = $importDataRepository->findOneBy(
            ['city' => $city],
            ['ymd' => 'DESC'] // this gets us the most recent
        );

        $unplausible = [];

        //@TODO find a design pattern for this and put rules into constants
        foreach ($data->snapshot as $unit) {
            $record = new stdClass();
            $record->unit = $unit['EinheitMastrNummer'];
            $record->gross_power = $unit['Bruttoleistung'];

            if ($unit['Nettonennleistung'] > $unit['Bruttoleistung']) {
                $record->rule = 1;
                $record->details = 'Nettonennleistung (' . $unit['Nettonennleistung'] . ') > Bruttoleistung (' . $unit['Bruttoleistung'] . ')';
                $unplausible[] = $record;
                continue; //@TODO instead of skipping we can sum up the violations
            }

            if (isset($unit['AnzahlModule']) && $unit['Bruttoleistung'] * 1000 / $unit['AnzahlModule'] > 600) {
                $record->rule = 2;
                $record->details = 'Errechnete Leistung je Modul unplausibel (' . round($unit['Bruttoleistung'] * 1000 / $unit['AnzahlModule'], 2) . ' Wp)';
                $unplausible[] = $record;
                continue;
            }

            if (isset($unit['AnzahlModule']) && $unit['Bruttoleistung'] * 1000 / $unit['AnzahlModule'] < 30) {
                $record->rule = 3;
                $record->details = 'Errechnete Leistung je Modul unplausibel (' . round($unit['Bruttoleistung'] * 1000 / $unit['AnzahlModule'], 2) . ' Wp)';
                $unplausible[] = $record;
                continue;
            }

            if (
                $unit['Nettonennleistung'] < ($unit['Bruttoleistung'] * 0.5)
                && $unit['Nettonennleistung'] > 0.8
            ) {
                $record->rule = 4;
                $record->details = 'Relative Nettoleistung entspricht ' . round($unit['Nettonennleistung'] * 100 / $unit['Bruttoleistung'], 2) . '%)';
                $unplausible[] = $record;
                continue;
            }
        }

        return new JsonResponse([
            'meta' => [
                'rules' => [
                    1 => 'Nettoleistung > Bruttoleistung',
                    2 => 'Leistung je Modul > 600 Wp',
                    3 => 'Leistung je Modul < 30 Wp',
                    4 => 'Nettoleistung < 50% Bruttoleistung'
                ],
                'ymd' => $data->ymd,
                'city' => $data->city,
            ],
            'units' => $unplausible
        ]);
    }
}
