# Marktstammdatenregister Dashboard

![Preview](https://raw.githubusercontent.com/norman27/marktstammdatenregister/main/docs/preview.png)

## TODOs
- [x] add github action daily running the importer
- [x] create proof of concept with dashboard and initial visualizations
- [x] add monitoring data of imports
- [x] add powercluster widget in dashboard
- [ ] data validation between WSDL and JSON API (seems to work for `herne`, but not `neuss`, `hamm` etc)
- [ ] add a local mysql container?
- [ ] add documentation
- [ ] add validation, linting and basic test github action
- [ ] move data creation to ApiController to prepare separate frontend
- [ ] experiment with rust router (`par_iter()`) 

## Requirements
### API Key
You have to register a user and apply for an API Key https://www.marktstammdatenregister.de/MaStRHilfe/subpages/webdienst.html

## Usage
In order to start the application it is recommended to open it in VSCode DevContainer. Then simply run it using:

```shell
apachectl start
```