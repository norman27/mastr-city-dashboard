# Marktstammdatenregister Dashboard

![Preview](https://raw.githubusercontent.com/norman27/marktstammdatenregister/main/docs/preview.png)

## Components

| component    | responsibility                           |
|--------------|------------------------------------------|
| importer     | Scheduled import of MaStR data           |
| server       | Apache Webserver with UI                 |

## Requirements
### API Key
You have to register a user and apply for an API Key https://www.marktstammdatenregister.de/MaStRHilfe/subpages/webdienst.html

## Usage
In order to start the application it is recommended to open it in VSCode DevContainer. Then simply run it using:

```shell
apachectl start
```