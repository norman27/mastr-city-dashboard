<?php

namespace App\Importer;

use App\Importer\Types\Einheit;
use stdClass;

class SoapClient 
{
    private string $apiKey;
    private string $apiUser;
    private $soapClient;

    public function __construct(string $apiKey, string $apiUser) 
    {
        $this->apiKey = $apiKey;
        $this->apiUser = $apiUser;

        // Available endpoints:
        // Test - https://test.marktstammdatenregister.de/MaStRAPI/wsdl/mastr.wsdl
        // Prod - https://www.marktstammdatenregister.de/MaStRApi/wsdl/mastr.wsdl
        $this->soapClient = new \SoapClient('https://www.marktstammdatenregister.de/MaStRApi/wsdl/mastr.wsdl');
    }

    public function GetAktuellerStandTageskontingent(): stdClass
    {
        return $this->soapClient->GetAktuellerStandTageskontingent(
            [
                'apiKey' => $this->apiKey,
                'marktakteurMastrNummer' => $this->apiUser,
            ]
        );
    }

    /**
     * @return Einheit[]
     */
    public function GetGefilterteListeStromErzeuger(string $city): array 
    {
        $response = $this->soapClient->GetGefilterteListeStromErzeuger(
            [
                'apiKey' => $this->apiKey,
                'marktakteurMastrNummer' => $this->apiUser,
                'einheitBetriebsstatus' => 'InBetrieb',
                'ort' => $city,
            ]
        );

        return $response->Einheiten;
    }

    public function GetEinheitSolar(string $mastrNumber): stdClass 
    {
        $response = $this->soapClient->GetEinheitSolar(
            [
                'apiKey' => $this->apiKey,
                'marktakteurMastrNummer' => $this->apiUser,
                'einheitMastrNummer' => $mastrNumber,
            ]
        );

        return $response;
    }
}