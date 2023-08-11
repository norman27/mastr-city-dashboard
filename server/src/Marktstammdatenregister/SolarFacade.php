<?php

namespace App\Marktstammdatenregister;

use App\Marktstammdatenregister\Soap\Client;
use App\Marktstammdatenregister\Types\Einheit;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressBar;

class SolarFacade
{
    private Client $client;
    private ?OutputInterface $output = null;

    public function __construct(Client $client)
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