# Manual SQL Data Analysis

```sql
SELECT
	JSON_EXTRACT(value, "$.NameStromerzeugungseinheit") AS name,
	CAST(JSON_EXTRACT(value, "$.Bruttoleistung") AS FLOAT) AS brutto,
    CAST(JSON_EXTRACT(value, "$.Nettonennleistung") AS FLOAT) AS netto
FROM
	import_data AS id
    CROSS JOIN JSON_TABLE(id.snapshot, '$[*]' COLUMNS (value JSON PATH '$')) jsontable
WHERE
	id.ymd = (SELECT MAX(ymd) FROM import_data)
    AND city = 'herne'
ORDER BY
	brutto DESC
;
```