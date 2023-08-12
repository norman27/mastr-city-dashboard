<?php

namespace App\Controller;

use App\Importer\SolarUnits;
use App\Importer\Types\Einheit;
use stdClass;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UiController extends AbstractController
{
    #[Route('/', name: 'app_ui')]
    public function index(SolarUnits $solarUnits): Response 
    {
        $city = 'Herne';

        $units = $solarUnits->getAll($city);

        //@TODO create solarburst chart
        $root = new stdClass();
        $root->id = '0.0';
        $root->name = $city;
        $root->parent = '';

        $gt100 = new stdClass();
        $gt100->id = '1.1';
        $gt100->name = '>= 100 kW';
        $gt100->parent = '0.0';

        $gt10 = new stdClass();
        $gt10->id = '1.2';
        $gt10->name = '10-99 kW';
        $gt10->parent = '0.0';

        $gt1 = new stdClass();
        $gt1->id = '1.3';
        $gt1->name = '1-9 kW';
        $gt1->parent = '0.0';

        $balcony = new stdClass();
        $balcony->id = '1.4';
        $balcony->name = 'Balkonkraftwerk';
        $balcony->parent = '0.0';

        $data = [$root, $gt100, $gt10, $gt1, $balcony];

        $id = 0;

        /** @var Einheit $unit */
        foreach ($units as $unit) {
            $obj = new stdClass();
            $obj->name = $unit->Name;
            $obj->id = '2.' . ++$id;
            $obj->value = (float) $unit->Bruttoleistung;

            if ($id > 995) {
                continue;
            }

            if ($unit->Bruttoleistung >= 100) {
                $obj->parent = '1.1';
            } elseif ($unit->Bruttoleistung >= 10 && $unit->Bruttoleistung < 100) {
                $obj->parent = '1.2';
            } elseif ($unit->Bruttoleistung >= 1 && $unit->Bruttoleistung < 10) {
                $obj->parent = '1.3';
            } elseif ($unit->Bruttoleistung < 1) {
                $obj->parent = '1.4';
            }

            $data[] = $obj;

        }

        return $this->render('default/index.html.twig', [
            'data' => json_encode($data),
        ]);
    }
}
