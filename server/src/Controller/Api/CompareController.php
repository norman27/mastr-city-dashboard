<?php

namespace App\Controller\Api;

use App\Repository\ImportDataRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class CompareController extends AbstractController
{
    /**
     * Find the difference between two days. First version only tells added or removed units
     */
    #[Route('/api/compare/{city}/{ymd1}/{ymd2}', name: 'app_api_compare')]
    public function compare(string $city, string $ymd1, string $ymd2, ImportDataRepository $importDataRepository): JsonResponse
    {
        $data1 = $importDataRepository->findOneBy(
            ['ymd' => $ymd1, 'city' => $city],
        );

        $data2 = $importDataRepository->findOneBy(
            ['ymd' => $ymd2, 'city' => $city],
        );

        $units1 = [];
        $units2 = [];
        foreach ($data1->snapshot as $value) {
            $units1[] = $value['EinheitMastrNummer'];
        }
        foreach ($data2->snapshot as $value) {
            $units2[] = $value['EinheitMastrNummer'];
        }

        $diff = [
            'added' => array_diff($units1, $units2),
            'removed' => array_diff($units2, $units1),
            'changed' => [], //@TODO feature missing
        ];

        return new JsonResponse($diff);
    }
}
