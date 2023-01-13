# mwbexporterextra
Libreria basada en https://github.com/mysql-workbench-schema-exporter/doctrine2-exporter con metodos personalizados de salida en doctrine 2 anotaciones

## Instalacion
```
composer require --dev antoniosam/mwbexporterextra
```
## Uso
```
Exporter::symfony4($filemwb, $outDir, $namespace = 'App');
Exporter::symfony4dev($filemwb, $outDir, $namespace = 'App');
Exporter::symfony3($filemwb, $outDir, $namespace = 'AppBundle');
```
**symfony4dev()**
No crea respaldo de los archivos, los sobreescribe  .
