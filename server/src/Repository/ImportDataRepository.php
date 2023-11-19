<?php

namespace App\Repository;

use App\Entity\ImportData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ImportData>
 *
 * @method ImportData|null find($id, $lockMode = null, $lockVersion = null)
 * @method ImportData|null findOneBy(array $criteria, array $orderBy = null)
 * @method ImportData[]    findAll()
 * @method ImportData[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImportDataRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ImportData::class);
    }

    public function getImportOverview(?string $city = null): array {
        $wheres = ['1=1'];

        if ($city !== null) {
            $wheres[] = 'city = :city';
        }

        $whereCondition = implode(' AND ', $wheres);

        $sql = <<<SQL
SELECT
    ymd,
    city,
    SUM(1) AS units,
    ROUND(SUM(CAST(JSON_EXTRACT(value, "$.Bruttoleistung") AS FLOAT))) AS gross,
    ROUND(SUM(CAST(JSON_EXTRACT(value, "$.Nettonennleistung") AS FLOAT))) AS net,
    SUM(
        IF(JSON_EXTRACT(value, "$.NetzbetreiberpruefungStatus") = 'Geprueft', 1, 0)
    ) AS geprueft
FROM
    import_data AS id
    CROSS JOIN JSON_TABLE(id.snapshot, '$[*]' COLUMNS (value JSON PATH '$')) jsontable
WHERE
    {$whereCondition}
GROUP BY
    ymd, city
ORDER BY
    ymd DESC
LIMIT 90
SQL;

        $em = $this->getEntityManager();
        $stmt = $em->getConnection()->prepare($sql);

        if ($city !== null) {
            $stmt->bindValue('city', $city);
        }

        return $stmt->executeQuery()->fetchAllAssociative();
    }

    public function getUnit(string $ymd, string $city, string $mastr): array {
/**
  'EinheitMastrNummer' => string 'SEE991914339624' (length=15)
  'DatumLetzteAktualisierung' => string '2019-12-07T05:10:56.6989221' (length=27)
  'LokationMastrNummer' => string 'SEL954855708805' (length=15)
  'NetzbetreiberpruefungStatus' => string 'Geprueft' (length=8)
  'Netzbetreiberzuordnungen' => 
    array (size=3)
      'NetzbetreiberMastrNummer' => string 'SNB963671951227' (length=15)
      'NetzbetreiberpruefungsDatum' => string '2019-12-12' (length=10)
      'NetzbetreiberpruefungsStatus' => string 'Geprueft' (length=8)
  'NetzbetreiberpruefungDatum' => string '2019-12-12' (length=10)
  'AnlagenbetreiberMastrNummer' => string 'ABR991604346222' (length=15)
  'NetzbetreiberMastrNummer' => string 'SNB963671951227' (length=15)
  'Land' => string 'Deutschland' (length=11)
  'Bundesland' => string 'NordrheinWestfalen' (length=18)
  'Landkreis' => string 'Herne' (length=5)
  'Gemeinde' => string 'Herne' (length=5)
  'Gemeindeschluessel' => string '05916000' (length=8)
  'Postleitzahl' => string '44649' (length=5)
  'StrasseNichtGefunden' => boolean false
  'Hausnummer' => 
    array (size=2)
      'Wert' => null
      'NichtVorhanden' => boolean false
  'HausnummerNichtGefunden' => boolean false
  'Ort' => string 'Herne' (length=5)
  'Registrierungsdatum' => string '2019-12-07' (length=10)
  'Inbetriebnahmedatum' => string '2014-08-20' (length=10)
  'EinheitSystemstatus' => string 'Aktiv' (length=5)
  'EinheitBetriebsstatus' => string 'InBetrieb' (length=9)
  'NameStromerzeugungseinheit' => string 'Hausdach' (length=8)
  'Weic' => 
    array (size=2)
      'Wert' => null
      'NichtVorhanden' => boolean false
  'Kraftwerksnummer' => 
    array (size=2)
      'Wert' => null
      'NichtVorhanden' => boolean false
  'Energietraeger' => string 'SolareStrahlungsenergie' (length=23)
  'FernsteuerbarkeitNb' => boolean false
  'Einspeisungsart' => string 'TeileinspeisungEigenverbrauch' (length=29)
  'zugeordneteWirkleistungWechselrichter' => string '3.600' (length=5)
  'GemeinsamerWechselrichterMitSpeicher' => string 'KeinStromspeicherVorhanden' (length=26)
  'AnzahlModule' => int 15
  'Lage' => string 'BaulicheAnlagen' (length=15)
  'Leistungsbegrenzung' => string 'Ja70Prozent' (length=11)
  'EinheitlicheAusrichtungUndNeigungswinkel' => boolean true
  'Hauptausrichtung' => string 'Sued' (length=4)
  'HauptausrichtungNeigungswinkel' => string 'Grad20Bis40' (length=11)
  'Nebenausrichtung' => string 'None' (length=4)
  'NebenausrichtungNeigungswinkel' => string 'None' (length=4)
  'Nutzungsbereich' => string 'Haushalt' (length=8)
  'EegMastrNummer' => string 'EEG918742932698' (length=15)
 */

        $sql = <<<SQL
SELECT
	JSON_EXTRACT(value, "$.EinheitMastrNummer") AS EinheitMastrNummer,
	JSON_EXTRACT(value, "$.NameStromerzeugungseinheit") AS NameStromerzeugungseinheit,
	CAST(JSON_EXTRACT(value, "$.Bruttoleistung") AS FLOAT) AS Bruttoleistung,
    CAST(JSON_EXTRACT(value, "$.Nettonennleistung") AS FLOAT) AS Nettonennleistung,
    CAST(JSON_EXTRACT(value, "$.AnzahlModule") AS UNSIGNED) AS AnzahlModule,
    JSON_EXTRACT(value, "$.Registrierungsdatum") AS Registrierungsdatum,
    JSON_EXTRACT(value, "$.Inbetriebnahmedatum") AS Inbetriebnahmedatum,
    JSON_EXTRACT(value, "$.Postleitzahl") AS Postleitzahl,
    JSON_EXTRACT(value, "$.GemeinsamerWechselrichterMitSpeicher") AS GemeinsamerWechselrichterMitSpeicher,
    JSON_EXTRACT(value, "$.Einspeisungsart") AS Einspeisungsart,
    JSON_EXTRACT(value, "$.Hauptausrichtung") AS Hauptausrichtung,
    JSON_EXTRACT(value, "$.HauptausrichtungNeigungswinkel") AS HauptausrichtungNeigungswinkel,
    JSON_EXTRACT(value, "$.Nebenausrichtung") AS Nebenausrichtung,
    JSON_EXTRACT(value, "$.NebenausrichtungNeigungswinkel") AS NebenausrichtungNeigungswinkel,
    JSON_EXTRACT(value, "$.Leistungsbegrenzung") AS Leistungsbegrenzung,
    JSON_EXTRACT(value, "$.NetzbetreiberpruefungStatus") AS NetzbetreiberpruefungStatus,
    JSON_EXTRACT(value, "$.NetzbetreiberpruefungDatum") AS NetzbetreiberpruefungDatum,
    JSON_EXTRACT(value, "$.Nutzungsbereich") AS Nutzungsbereich
FROM
	import_data AS id
    CROSS JOIN JSON_TABLE(id.snapshot, '$[*]' COLUMNS (value JSON PATH '$')) jsontable
WHERE
	id.ymd = :ymd
    AND city = :city
    AND JSON_EXTRACT(value, "$.EinheitMastrNummer") = :mastr
SQL;

        $em = $this->getEntityManager();
        $stmt = $em->getConnection()->prepare($sql);

        $stmt->bindValue('ymd', $ymd);
        $stmt->bindValue('city', $city);
        $stmt->bindValue('mastr', $mastr);

        $result = $stmt->executeQuery()->fetchAssociative();

        return $result ? $result : [];
    }
}