<?php

namespace App\Controller;

use App\Importer\SoapClient;
use App\Repository\ImportDataRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api')]
class ApiController extends AbstractController
{
    #[Route('/v1/active', name: 'app_api_active_list')]
    public function activeList(Request $request, ImportDataRepository $importDataRepository): JsonResponse
    {
        $datas = $importDataRepository->getImportOverview($request->get('city', null));
        array_walk($datas, function (&$data) {
            $data['ymd'] = \DateTime::createFromFormat('Ymd', $data['ymd'])->format('Y-m-d');
        });

        return $this->json($datas);
    }

    #[Route('/rate-limit', name: 'app_api_rate_limit')]
    public function rateLimit(): JsonResponse
    {
        $apiKey = $this->getParameter('app.mastr_api_key');
        $apiUser = $this->getParameter('app.mastr_api_user');

        $soap = new SoapClient($apiKey, $apiUser);

        return $this->json(
            $soap->GetAktuellerStandTageskontingent(
                [
                    'apiKey' => $apiKey,
                    'marktakteurMastrNummer' => $apiUser,
                ]
            )
        );
    }
}
