<?php

// can be used to import old data snapshots 
$city = 'herne';
$ymd = '20230712';

function getFormattedDate($old, $format = 'Y-m-d\TH:i:s.u') {
    $old = str_replace(['/Date(', ')/'], ['', ''], $old);
    $sec = intval($old / 1000);
    return date($format, $sec);
}

// read json from file
$json = json_decode(file_get_contents('data/herne.json'));

$newJson = [];

foreach ($json->Data as $item) {
    if (
        $item->BetriebsStatusId !== 35
        || $item->EnergietraegerName !== "Solare Strahlungsenergie"
    ) {
        continue;
    }

    // setting wsdl object as good as possible
    $newItem = new stdClass();
    $newItem->Ergebniscode = "OK";
    $newItem->AufrufVeraltet = false;
    $newItem->AufrufLebenszeitEnde = "9999-12-31T23:59:59.9999999";
    $newItem->AufrufVersion = 1;
    $newItem->EinheitMastrNummer = $item->MaStRNummer;
    $newItem->DatumLetzteAktualisierung = getFormattedDate($item->DatumLetzteAktualisierung);
    $newItem->LokationMastrNummer = $item->LokationId;
    $newItem->NetzbetreiberpruefungStatus = "Unbekannt";
    $newItem->Netzbetreiberzuordnungen = new stdClass();
    $newItem->NetzbetreiberpruefungDatum = "1970-01-01";
    $newItem->AnlagenbetreiberMastrNummer = $item->AnlagenbetreiberMaStRNummer;
    $newItem->NetzbetreiberMastrNummer = "SNB963671951227";
    $newItem->Land = "Deutschland";
    $newItem->Bundesland = "NordrheinWestfalen";
    $newItem->Landkreis = "Herne";
    $newItem->Gemeinde = "Herne";
    $newItem->Gemeindeschluessel = "05916000";
    $newItem->Postleitzahl = $item->Plz;
    $newItem->StrasseNichGefunden = false;
    $newItem->Hausnummer = new stdClass();
    $newItem->HausnummerNichtGefunden = false;
    $newItem->Ort = "Herne";
    $newItem->Registrierungsdatum = getFormattedDate($item->DatumLetzteAktualisierung, 'Y-m-d');
    $newItem->Inbetriebnahmedatum = getFormattedDate($item->InbetriebnahmeDatum, 'Y-m-d');
    $newItem->EinheitSystemstatus = "Aktiv";
    $newItem->EinheitBetriebsstatus = "InBetrieb";
    $newItem->NameStromerzeugungseinheit = $item->EinheitName;
    $newItem->Weic = new stdClass();
    $newItem->Kraftwerksnummer = new stdClass();
    $newItem->Energietraeger = "SolareStrahlungsenergie";
    $newItem->Bruttoleistung = $item->Bruttoleistung;
    $newItem->Nettonennleistung = $item->Nettonennleistung;
    $newItem->FernsteuerbarkeitNb = false;
    $newItem->Einspeisungsart = "Unbekannt";
    $newItem->zugeordneteWirkleistungWechselrichter = $item->Nettonennleistung;
    $newItem->GemeinsamerWechselrichterMitSpeicher = "Unbekannt";
    $newItem->AnzahlModule = $item->AnzahlSolarModule;
    $newItem->Lage = "Unbekannt";
    $newItem->Leistungsbegrenzung = "Unbekannt";
    $newItem->EinheitlicheAusrichtungUndNeigungswinkel = false;
    $newItem->Hauptausrichtung = "Unbekannt";
    $newItem->HauptausrichtungNeigungswinkel = "Unbekannt";
    $newItem->Nebenausrichtung = "None";
    $newItem->NebenausrichtungNeigungswinkel = "None";
    $newItem->Nutzungsbereich = "Unbekannt";
    $newItem->EegMastrNummer = "Unbekannt";
    
    $newJson[] = $newItem;
}

$mysqli = new mysqli("w009acfc.kasserver.com", "d03dd154", "QmM6KwquCrSnvJF8fsFv", "d03dd154");

$stmt = $mysqli->prepare('INSERT INTO import_data SET ymd=?, city=?, snapshot=?');
$snapshot = json_encode($newJson);
$stmt->bind_param("sss", $ymd, $city, $snapshot);
$stmt->execute();
$stmt->close();
