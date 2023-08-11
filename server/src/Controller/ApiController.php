<?php

namespace App\Controller;

use App\Marktstammdatenregister\Soap\Client;
use SoapClient;
use stdClass;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Marktstammdatenregister\Types\Einheit;

class ApiController extends AbstractController
{
    #[Route('/api', name: 'app_api')]
    public function index(): JsonResponse
    {
        $apiKey = $this->getParameter('app.mastr_api_key');
        $apiUser = $this->getParameter('app.mastr_api_user');
        $wsdl = $this->getParameter('app.mastr_api_wsdl');

        $soap = new SoapClient($wsdl);

        $response = $soap->GetGefilterteListeStromErzeuger(
            [
                'apiKey' => $apiKey,
                'marktakteurMastrNummer' => $apiUser,
                //'postleitzahl' => 44628,
                'ort' => 'Herne',
                'einheitBetriebsstatus' => 'InBetrieb'
            ]
        );

        foreach ($response->Einheiten as $einheit) {
            var_dump($einheit);
        }

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
