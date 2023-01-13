<?php

/**
 * User: marcosamano
 * Date: 21/02/20
 */

namespace Ast\FormatPhoto;

use \Ast\FormatPhoto\CollagePhotoToPrint;

class CollageFolderToPrint
{

    function __construct($format = '4x6', $cut = 'adjust',$name = 'collage')
    {
        $this->format = $format;
        $this->cutPhoto = $cut;
        $this->name = $name;
    }
    

    public function collageTwo($sourceFolder,$output,$format,$cut,$nameOutput){
        $files = scandir($sourceFolder);
        if($files!==false && count($files) > 2){
            $names = $this->cleanList($files);
            if((count($names) % 2) == 1){
                $names[] =  $names[0];
            }
            $limit = count($names);
            for($i = 0 ; $i < $limit;$i += 2 ){
                $name1 = $sourceFolder.DIRECTORY_SEPARATOR.$names[$i];
                $name2 = $sourceFolder.DIRECTORY_SEPARATOR.$names[($i+1)];
                $filename =  $output.DIRECTORY_SEPARATOR.$nameOutput.($i+1).'.jpg';
                $filename = CollagePhotoToPrint::collageTwo($name1,$name2,$filename,$format,$cut);
            }
        }
        return null;
    }
    
    private function cleanList($files){
        $filenames = [];
        foreach($files as $name){
            if($name != '.' && $name != '..'){
                $filenames[] = $name;
            }
        }
        return $filenames;
    }

    public static function formatFolderCollageTwo ($source,$output,$format = '4x6',$cut='adjust', $outputName = 'collage_'){
        $obj = new self($format,false,$cut);
        $obj->collageTwo($source,$output,$format,$cut,$outputName);
        return $output;

    }
}