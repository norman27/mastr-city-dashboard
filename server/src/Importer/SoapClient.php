<?php

namespace App\Importer;

use App\Importer\Types\Einheit;
use SoapFault;
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
    public function GetGefilterteListeStromErzeuger(string $city, int $page, $limit): array 
    {
        $offset = (($page - 1) * $limit) + 1;

        $response = $this->soapClient->GetGefilterteListeStromErzeuger(
            [
                'apiKey' => $this->apiKey,
                'marktakteurMastrNummer' => $this->apiUser,
                'einheitBetriebsstatus' => 'InBetrieb',
                'ort' => $city,
                'startAb' => $offset,
                'limit' => $limit, //@TODO this is a bug for cities with more than 2000 units
            ]
        );

        return (isset($response->Einheiten)) ? $response->Einheiten : [];
    }

    public function GetEinheitSolar(string $mastrNumber, int $requestTries = 0): stdClass 
    {
        try {
            $response = $this->soapClient->GetEinheitSolar(
                [
                    'apiKey' => $this->apiKey,
                    'marktakteurMastrNummer' => $this->apiUser,
                    'einheitMastrNummer' => $mastrNumber,
                ]
            );
        } catch (SoapFault $exception) {
            if (
                $requestTries < 3
            ) {
                sleep(1);
                return $this->GetEinheitSolar($mastrNumber, $requestTries + 1);
            }
        }

        return $response;
    }
}