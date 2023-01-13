<?php

/**
 * User: marcosamano
 * Date: 21/02/20
 */

namespace Ast\FormatPhoto;

class FormatPhotoToPrint
{

    const FORMAT_5x6 = [1250, 1500];
    const FORMAT_4x6 = [1000, 1500];
    const MARGIN = 15;

    private $size_x = 0;
    private $size_y = 0;

    private $hasMargin = false;
    private $margin = 0;
    private $newPhotoResource = null;
    private $cutPhoto = 'adjust'; // 'expand';
    private $dimensions;

    function __construct($format = '4x6', $margin = true, $cut = 'adjust')
    {
        switch ($format) {
            case '4x6':
                $this->size_x = self::FORMAT_4x6[1];
                $this->size_y = self::FORMAT_4x6[0];
                break;
            case '5x6':
                $this->size_x = self::FORMAT_5x6[1];
                $this->size_y = self::FORMAT_5x6[0];
                break;
            default:
                $this->size_x = self::FORMAT_4x6[1];
                $this->size_y = self::FORMAT_4x6[0];
                break;
        }
        $this->hasMargin = $margin;
        $this->margin = self::MARGIN;
        $this->cutPhoto = $cut;
    }

    public function getSize(){
        return ['x'=>$this->size_x,'y'=>$this->size_y];
    }

    public function setSize($x, $y)
    {
        $this->size_x = $x;
        $this->size_y = $y;
    }

    public function format($filename)
    {
        if (file_exists($filename)) {
            $imageResource = $this->getResource($filename);
            $this->calculateNewSize($imageResource);
            $this->newPhotoResource = $this->createResourceFormated($imageResource);
        }
    }

    public function save($filename, $overwrite = false)
    {
        if (file_exists($filename)) {
            if ($overwrite) {
                unlink($filename);
                imagejpeg($this->newPhotoResource, $filename, 95);
                return $filename;
            } else {
                return null;
            }
        } else {
            imagejpeg($this->newPhotoResource, $filename);
            return $filename;
        }
    }

    public function setNewPhoto($resource)
    {
        return $this->newPhotoResource = $resource;
    }

    public function getNewPhoto()
    {
        return $this->newPhotoResource;
    }

    /**
     * @param $source
     * @param $output
     * @param string $type
     * @param string $format
     * @return null|string
     */
    static function formatPhoto($source, $output, $format = '4x6', $margin = false, $cut = 'adjust')
    {
        $obj = new self($format, $margin, $cut);
        $obj->format($source);
        return $obj->save($output, true);
    }

    /**
     * @return resource
     */
    public function createEmptyResource($size_x, $size_y)
    {
        $thumb = imagecreatetruecolor($size_x, $size_y);
        $fondo = imagecolorallocate($thumb, 255, 255, 255);
        imagefilledrectangle($thumb, 0, 0, $size_x, $size_y, $fondo);
        return $thumb;
    }

    /**
     * @param $resource
     * @return resource
     */
    private function createResourceFormated($resource)
    {
        $thumb = $this->createEmptyResource($this->dimensions['photo_w'], $this->dimensions['photo_h']);
        imagecopyresized(
            $thumb,
            $resource,
            $this->dimensions['offset_x'],
            $this->dimensions['offset_y'],
            $this->dimensions['startCrop_x'],
            $this->dimensions['startCrop_y'],
            $this->dimensions['resize_w'],
            $this->dimensions['resize_h'],
            $this->dimensions['cropImage_w'],
            $this->dimensions['cropImage_h']
        );
        return $thumb;
    }

    private function calculateNewSize($resource)
    {
        $ancho = imagesx($resource);
        $alto = imagesy($resource);

        if ($this->hasMargin) {
            $max_x = $this->size_x - ($this->margin * 2);
            $max_y = $this->size_y - ($this->margin * 2);
        } else {
            $max_x = $this->size_x;
            $max_y = $this->size_y;
        }

        $ratioy = $alto / $ancho;
        $ratiox = $ancho / $alto;
        $ratioFormat_y = $max_y / $max_x;
        $ratioFormat_x = $max_x / $max_y;
        $offsetX = 0;
        $offsetY = 0;
        $startCrop_x = 0;
        $startCrop_y = 0;
        $cropImage_w = 0;
        $cropImage_h = 0;
        if ($this->cutPhoto == 'expand') {
            if($this->hasMargin){
                $offsetX = $this->margin;
                $offsetY = $this->margin;
            }
            if ($ancho > $alto) {
                $x = $max_x;
                $y = $max_y;
                $expand_Y = floor($ancho * $ratioFormat_y);
                if ($expand_Y > $alto) {
                    $expand_X = floor($alto * $ratioFormat_x);
                    $startCrop_x = floor(($ancho - $expand_X) / 2);
                    $startCrop_y = 0;
                    $cropImage_w = $ancho - ($startCrop_x * 2) ;
                    $cropImage_h = $alto;
                } else {
                    $startCrop_x = 0;
                    $startCrop_y = floor(($alto - $expand_Y) / 2);
                    $cropImage_w = $ancho;
                    $cropImage_h = $alto - ($startCrop_y * 2);
                }
            } else {
                $x = $max_x;
                $y = $max_y;
                $expand_Y = floor($ancho * $ratioFormat_y);
                if ($expand_Y > $alto) {
                    $expand_X = floor($alto * $ratioFormat_x);
                    $startCrop_x = floor(($ancho - $expand_X) / 2);
                    $startCrop_y = 0;
                    $cropImage_w = $ancho - ($startCrop_x * 2) ;
                    $cropImage_h = $alto;
                } else {
                    $startCrop_x = 0;
                    $startCrop_y = floor(($alto - $expand_Y) / 2);
                    $cropImage_w = $ancho ;
                    $cropImage_h = $alto - ($startCrop_y * 2);
                }
            }
        } else {
            if ($ancho > $alto) {
                $x = $max_x;
                $y = floor($x * $ratioy);
                if ($y > $max_y) {
                    $y = $max_y;
                    $x = floor($y * $ratiox);
                }
            } else {
                $y = $max_y;
                $x = floor($y * $ratiox);
                if ($x > $max_x) {
                    $x = $max_x;
                    $y = floor($x * $ratioy);
                }
            }
            $cropImage_w = $ancho;
            $cropImage_h = $alto;
            if($this->hasMargin){
                $offsetX = floor(($max_x - $x) / 2) + $this->margin ;
                $offsetY = floor(($max_y - $y) / 2) + $this->margin;
            }else{
                $offsetX = floor(($max_x - $x) / 2);
                $offsetY = floor(($max_y - $y) / 2);
            }
            
        }
        $this->dimensions = [
            'photo_w' => $this->size_x, 'photo_h' => $this->size_y,
            'orgX' => $ancho, 'orgY' => $alto,
            'offset_x' => $offsetX, 'offset_y' => $offsetY,
            'resize_w' => $x, 'resize_h' => $y,
            'startCrop_x' => $startCrop_x, 'startCrop_y' => $startCrop_y,
            'cropImage_w' => $cropImage_w, 'cropImage_h' => $cropImage_h
        ];
        //print_r($this->dimensions);
    }

    /**
     * @param $filename
     * @return null|resource
     */
    private function getResource($filename)
    {
        $ext = pathinfo($filename)['extension'];
        if ($ext == 'jpeg' || $ext == 'jpg' || $ext == 'png') {
            if ($ext == 'jpeg' || $ext == 'jpg') {
                $resource = imagecreatefromjpeg($filename);
            } else {
                $resource = imagecreatefrompng($filename);
            }
            if ((imagesx($resource) < imagesy($resource) && $this->size_x > $this->size_y) ||
                (imagesx($resource) > imagesy($resource) && $this->size_x < $this->size_y)
            ) {
                $resource = imagerotate($resource, 90, 0);
            }
            return $resource;
        }
        return null;
    }

    public static function createFolderOutput($folder){
        if(!file_exists($folder)){
            mkdir($folder,077,true);
        }
    }

    
}
