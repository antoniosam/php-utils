<?php

require_once(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'src'.DIRECTORY_SEPARATOR.'FormatPhotoToPrint.php');
require_once(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'src'.DIRECTORY_SEPARATOR.'CollagePhotoToPrint.php');
require_once(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'src'.DIRECTORY_SEPARATOR.'CollageFolderToPrint.php');

use Ast\FormatPhoto\CollageFolderToPrint;
use Ast\FormatPhoto\FormatPhotoToPrint;
use Ast\FormatPhoto\CollagePhotoToPrint;

$source_L  = __DIR__.DIRECTORY_SEPARATOR.'imgs'.DIRECTORY_SEPARATOR.'demo_landscape.jpg';
$source_P  = __DIR__.DIRECTORY_SEPARATOR.'imgs'.DIRECTORY_SEPARATOR.'demo_portrait.jpg';
$ouput_L_A  = __DIR__.DIRECTORY_SEPARATOR.'demo'.DIRECTORY_SEPARATOR.'output_landscape_adjust.jpg';
$ouput_L_E  = __DIR__.DIRECTORY_SEPARATOR.'demo'.DIRECTORY_SEPARATOR.'output_landscape_expand.jpg';
$ouput_P_A  = __DIR__.DIRECTORY_SEPARATOR.'demo'.DIRECTORY_SEPARATOR.'output_portrait_adjust.jpg';
$ouput_P_E  = __DIR__.DIRECTORY_SEPARATOR.'demo'.DIRECTORY_SEPARATOR.'output_portrait_expand.jpg';
$ouput_C2_A  = __DIR__.DIRECTORY_SEPARATOR.'demo'.DIRECTORY_SEPARATOR.'output_collage2_adjust.jpg';
$ouput_C2_E  = __DIR__.DIRECTORY_SEPARATOR.'demo'.DIRECTORY_SEPARATOR.'output_collage2_expand.jpg';

$sourcefolder  = __DIR__.DIRECTORY_SEPARATOR.'imgs';
$ouputfolder  = __DIR__.DIRECTORY_SEPARATOR.'demo';
$margin = false;

FormatPhotoToPrint::createFolderOutput($ouputfolder);

FormatPhotoToPrint::formatPhoto($source_L,$ouput_L_A,'4x6',$margin,'adjust');
FormatPhotoToPrint::formatPhoto($source_L,$ouput_L_E,'4x6',$margin,'expand');

FormatPhotoToPrint::formatPhoto($source_P,$ouput_P_A,'4x6',$margin,'adjust');
FormatPhotoToPrint::formatPhoto($source_P,$ouput_P_E,'4x6',$margin,'expand');

CollagePhotoToPrint::collageTwo($source_L,$source_P,$ouput_C2_A,'4x6','adjust');
CollagePhotoToPrint::collageTwo($source_L,$source_P,$ouput_C2_E,'4x6','expand');

CollageFolderToPrint::formatFolderCollageTwo($sourcefolder,$ouputfolder,'4x6','expand','demofolder_');
