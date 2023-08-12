<?php

namespace App\Importer;

use App\Importer\SoapClient;
use App\Importer\Types\Einheit;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressBar;

class SolarFacade
{
    private SoapClient $client;
    private ?OutputInterface $output = null;

    public function __construct(SoapClient $client)
    {
        $this->client = $client;    
    }

    public function getUnitsForCity(string $city): array
    {
        $units = $this->client->GetGefilterteListeStromErzeuger($city);

        $filteredUnits = array_filter($units, function($v, $k) {
            /** @var Einheit $v */
            return $v->Einheittyp === Einheit::EINHEITTYP_SOLAREINHEIT;
        }, ARRAY_FILTER_USE_BOTH);

        if ($this->output !== null) {
            $unitCount = count($filteredUnits);
            $this->output->writeln(sprintf('Found %d units', $unitCount));

            $progressBar = new ProgressBar($this->output, 100);
            $progressBar->start();
            $i = 0;
        }

        $solarUnits = [];
        
        foreach ($filteredUnits as $unit) {
            if ($this->output !== null) {
                $i++;
                if ($i % round($unitCount / 100) === 0) {
                    $progressBar->advance();
                };
            }
            $solarUnits[] = $this->client->GetEinheitSolar($unit->EinheitMastrNummer);
        }

        if ($this->output !== null) {
            $progressBar->finish();
            $this->output->writeln('');
        }

        return $solarUnits;
    }

    public function setOutput(OutputInterface $output): void
    {
        $this->output = $output;
    }
}