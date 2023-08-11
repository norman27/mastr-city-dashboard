<?php

namespace App\Controller;

use App\Marktstammdatenregister\Soap\Client as SoapClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{
    #[Route('/api', name: 'app_api')]
    public function index(): JsonResponse
    {
        $apiKey = $this->getParameter('app.mastr_api_key');
        $apiUser = $this->getParameter('app.mastr_api_user');

        $soap = new SoapClient($apiKey, $apiUser);

        var_dump(
            $soap->GetAktuellerStandTageskontingent(
                [
                    'apiKey' => $apiKey,
                    'marktakteurMastrNummer' => $apiUser,
                ]
            )
        );
        
        exit;

        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/ApiController.php',
        ]);
    }
}
