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
        }

        ksort($installedPowerByDay);
        ksort($installedUnitsByDay);

        return $this->render('default/dashboard.html.twig', [
            'sum' => [
                'units' => count($importData->getSnapshot()),
                'gross_power' => round($sumGrossPower, 1),
                'net_power' => round($sumNetPower, 1),
            ],
            'areaChart' => [
                'labels' => array_keys($installedPowerByDay),
                'values' => [
                    'power' => array_values($installedPowerByDay),
                    'units' => array_values($installedUnitsByDay),
                ],
            ]
        ]);
    }

    #[Route('/table', name: 'app_table')]
    public function table(): Response 
    {
        return new Response('TODO');
    }

    #[Route('/monitoring', name: 'app_monitoring')]
    public function monitoring(): Response 
    {
        return new Response('TODO');
    }

    #[Route('/documentation', name: 'app_documentation')]
    public function documentation(): Response 
    {
        return new Response('TODO');
    }
}
