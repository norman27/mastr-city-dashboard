<?php

namespace App\Controller;

use SoapClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{
    #[Route('/api', name: 'app_api')]
    public function index(): JsonResponse
    {
        $soap = new SoapClient('https://test.marktstammdatenregister.de/MaStRAPI/wsdl/mastr.wsdl');
        var_dump($soap->__getFunctions()); exit;


        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/ApiController.php',
        ]);
    }
}
