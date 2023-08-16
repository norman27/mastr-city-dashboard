<?php

namespace App\Controller;

use App\Entity\ImportData;
use App\Repository\ImportDataRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    #[Route('/', name: 'app_dashboard')]
    public function dashboard(ImportDataRepository $importDataRepository): Response 
    {
        /** @var ImportData $importData */
        $importData = $importDataRepository->findOneBy(
            ['city' => 'herne'],
            ['ymd' => 'DESC'] // this should get us the most recent
        );

        $sumGrossPower = 0.0;
        $sumNetPower = 0.0;
        $installedPowerByDay = [];
        $installedUnitsByDay = [];
        $clusters = [
            '0-1 kWp' => 0.0,
            '1-10 kWp' => 0.0,
            '10-30 kWp' => 0.0,
            '30-100 kWp' => 0.0,
            '100-500 kWp' => 0.0,
            '500-1000 kWp' => 0.0,
            '>1000 kWp' => 0.0,
        ];
        foreach ($importData->getSnapshot() as $unit) {
            $day = $unit['Inbetriebnahmedatum'];
            if (! isset($installedPowerByDay[$day])) {
                $installedPowerByDay[$day] = 0.0;
                $installedUnitsByDay[$day] = 0;
            }
            $installedPowerByDay[$day] += (float) $unit['Nettonennleistung'];
            $installedUnitsByDay[$day] += 1;

            $sumGrossPower += (float) $unit['Bruttoleistung'];
            $sumNetPower += (float) $unit['Nettonennleistung'];

            //@TODO not very beutiful
            if ($unit['Nettonennleistung'] < 1) {
                $clusters['0-1 kWp'] += (float) $unit['Nettonennleistung'];
            } elseif ($unit['Nettonennleistung'] < 10) {
                $clusters['1-10 kWp'] += (float) $unit['Nettonennleistung'];
            } elseif ($unit['Nettonennleistung'] < 30) {
                $clusters['10-30 kWp'] += (float) $unit['Nettonennleistung'];
            } elseif ($unit['Nettonennleistung'] < 100) {
                $clusters['30-100 kWp'] += (float) $unit['Nettonennleistung'];
            } elseif ($unit['Nettonennleistung'] < 500) {
                $clusters['100-500 kWp'] += (float) $unit['Nettonennleistung'];
            } elseif ($unit['Nettonennleistung'] < 1000) {
                $clusters['500-1000 kWp'] += (float) $unit['Nettonennleistung'];
            } else {
                $clusters['>1000 kWp'] += (float) $unit['Nettonennleistung'];
            }
        }

        ksort($installedPowerByDay);
        ksort($installedUnitsByDay);
        $installedCumulativeUnits = [];

        foreach ($installedUnitsByDay as $day => $units) {
            if (empty($installedCumulativeUnits)) {
                $installedCumulativeUnits[$day] = $units;
            } else {
                $installedCumulativeUnits[$day] = $units + end($installedCumulativeUnits);
            }
        }

        $activeResult = $this->forward('App\Controller\ApiController::activeList', ['city'  => 'herne']);
        $filteredActive = [];

        foreach (json_decode($activeResult->getContent()) as $active) {
            $filteredActive[$active->ymd] = $active->net;
        }

        return $this->render('default/dashboard.html.twig', [
            'sum' => [
                'units' => count($importData->getSnapshot()),
                'gross_power' => round($sumGrossPower, 1),
                'net_power' => round($sumNetPower, 1),
            ],
            'areaChart' => [ //@TODO rename
                'labels' => array_keys($installedPowerByDay),
                'values' => [
                    'power' => array_values($installedPowerByDay),
                    'units' => array_values($installedCumulativeUnits),
                ],
            ],
            'pieChart' => [ //@TODO rename
                'labels' => array_keys($clusters),
                'values' => array_values($clusters),
            ],
            'netPowerChart' => [
                'ymd' => array_keys($filteredActive),
                'net' => array_values($filteredActive),
            ],
        ]);
    }

    #[Route('/table', name: 'app_table')]
    public function table(ImportDataRepository $importDataRepository): Response 
    {
        /** @var ImportData $importData */
        $importData = $importDataRepository->findOneBy(
            ['city' => 'herne'],
            ['ymd' => 'DESC'] // this should get us the most recent
        );

        return $this->render('default/table.html.twig', [
            'units' => $importData->getSnapshot()
        ]);
    }

    #[Route('/imports', name: 'app_imports')]
    public function monitoring(ImportDataRepository $importDataRepository): Response 
    {
        return $this->render('default/imports.html.twig', [
            'imports' => $importDataRepository->getImportOverview()
        ]);
    }

    #[Route('/documentation', name: 'app_documentation')]
    public function documentation(): Response 
    {
        return new Response('TODO');
    }
}
