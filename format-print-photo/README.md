# Formato de Fotos para imprimi  (formatphoto)

Esta libreria formatea las imagenes para que se ajuste a la resolucion de impresion de los formatos mas comunes 4x6 ó 5x6
Manteniedo la misma relacion de aspecto cortando la imagen por los extremos o reduciendola si se desea mantener completa añadiendo espacio en blanco

## Instalar

```
composer require antoniosam/formatphoto
``` 

## Uso
La libreria esta compuesta por 3 clases

#### FormatPhotoToPrint

Esta clase es la base de los formateos, realiza los calculos y genera las imagenes para impresion

```
FormatPhotoToPrint::createFolderOutput($ouputfolder);

$format = '4x6' || '5x6'; '4x6' default
$magin = true || false;   true default 
$type = 'adjust' || 'expand'; 'adjust' default

FormatPhotoToPrint::formatPhoto($source,$ouput,$format,$margin,$type);
```

#### CollagePhotoToPrint

Esta clase es un acceso para poder unis dos fotos en una sola, para imprimir collages 
```
$format = '4x6' || '5x6'; '4x6' default
$type = 'adjust' || 'expand'; 'adjust' default

CollagePhotoToPrint::collageTwo($source1,$source2,$ouput,$format,$type);
```

#### CollageFolderToPrint

Esta clase es un acceso para poder crear collages sobre una carpeta completa de fotos

```
$format = '4x6' || '5x6'; '4x6' default
$type = 'adjust' || 'expand'; 'adjust' default
$name = $nombre de la imagen de salida;  'collage' default

CollageFolderToPrint::formatFolderCollageTwo($sourcefolder,$ouputfolder,$format,$type,$name);
```