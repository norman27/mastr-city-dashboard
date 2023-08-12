<?php

namespace App\Importer\Types;

class Einheit 
{
    public const EINHEITTYP_SOLAREINHEIT = 'Solareinheit';

    public float $Bruttoleistung = 0.0;
    public string $Einheitart = '';
    public string $Einheittyp = '';
    public string $EinheitMastrNummer = '';
    public string $Name = '';
}