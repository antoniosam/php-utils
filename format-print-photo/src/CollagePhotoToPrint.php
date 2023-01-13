<?php

/**
 * User: marcosamano
 * Date: 21/02/20
 */

namespace Ast\FormatPhoto;

use \Ast\FormatPhoto\FormatPhotoToPrint;

class CollagePhotoToPrint
{

    private $format ;
    private $margin ;
    private $cutPhoto = 'adjust'; // 'expand';

    function __construct($format = '4x6', $margin = false, $cut = 'adjust')
    {
        
        $this->format = $format;
        $this->margin = $margin;
        $this->cutPhoto = $cut;
    }
    

    public function mergeTwo($source,$source2,$output){
        if(file_exists($source) && file_exists($source2)){

            $format = new FormatPhotoToPrint($this->format,$this->margin,$this->cutPhoto);
            $size = $format->getSize();
            $size_x = $size['x'] /2 ;
            $size_y = $size['y'] ;
            $format->setSize($size_x,$size_y);
            $format->format($source);
            $photo1 = $format->getNewPhoto();
            $format->format($source2);
            $photo2 = $format->getNewPhoto();

            $destiny = $format->createEmptyResource($size['x'], $size['y']);
            imagecopy ( $destiny , $photo1 , 0 , 0 , 0 , 0 , $size_x ,  $size_y );
            imagecopy ( $destiny , $photo2 , $size_x , 0 , 0 , 0 , $size_x ,  $size_y );
            $format->setNewPhoto($destiny);
            $format->save($output);
        }
        
        return null;
    }

    public static function collageTwo ($source,$source2,$output,$format = '4x6',$cut='adjust'){
        $obj = new self($format,false,$cut);
        $obj->mergeTwo($source,$source2,$output);
        return $output;

    }
}