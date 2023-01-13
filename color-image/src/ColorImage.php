<?php

/**
 * Created by PhpStorm.
 * User: marcosamano
 * Date: 17/09/18
 * Time: 11:27 AM
 */

namespace Ast\ColorImage;

class ColorImage
{
    /**
     * @param $resource
     * @return InfoColorImage
     */
    public static function getInfo($resource,$grayfilter = false)
    {
        $size_x = imagesx($resource);
        $size_y = imagesy($resource);

        if($grayfilter){
            imagefilter($resource, IMG_FILTER_GRAYSCALE);
        }

        /*returns the mean value of the colors and the list of all pixel's colors*/
        $colorListMap = array();
        $colorPercentage = [];
        $pixeles = ($size_x * $size_y);
        $unidad = 100/$pixeles;
        $colorSum = 0;
        $min_pixel = 255;
        $max_pixel = 0;
        for ($y = 0; $y < $size_y; $y++) {
            for ($x = 0; $x < $size_x; $x++) {
                $rgb = imagecolorat($resource, $x, $y);
                $pixel = $rgb & 0xFF;
                $colorSum  += $pixel;
                $colorListMap[$y][] = $pixel;
                if(!isset($colorPercentage[$pixel])){
                    $colorPercentage[$pixel] = 0;
                }
                $colorPercentage[$pixel] += $unidad;
                if($pixel < $min_pixel){
                    $min_pixel = $pixel;
                }
                if($pixel > $max_pixel){
                    $max_pixel = $pixel;
                }
            }
        }
        $domina = 0;
        $roundpercetage = [];
        $dominatecolor = '';
        foreach ($colorPercentage as $pixel => $porcentaje) {
            $round = ($porcentaje < 1)? ceil($porcentaje) : floor($porcentaje);
            if ($round > $domina) {
                $domina = $round;
                $dominatecolor = $pixel;
            }
            $roundpercetage[$pixel] = $round;
        }
        $avg = round(($colorSum / $pixeles));
        return new InfoColorImage($min_pixel,$max_pixel,$avg, $dominatecolor,$roundpercetage,$colorListMap);
    }
}