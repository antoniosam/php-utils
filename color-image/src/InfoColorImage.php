<?php
/**
 * Created by PhpStorm.
 * User: marcosamano
 * Date: 17/09/18
 * Time: 11:29 AM
 */

namespace Ast\ColorImage;


class InfoColorImage
{
    private $min_pixel;
    private $max_pixel;
    private $avg;
    private $dominatecolor;
    private $colorPercentage;
    private $colorListMap;
    function __construct($min_pixel,$max_pixel,$avg, $dominatecolor,$colorPercentage,$colorListMap)
    {
        $this->min_pixel= $min_pixel;
        $this->max_pixel = $max_pixel;
        $this->avg = $avg;
        $this->dominatecolor = $dominatecolor;
        $this->colorPercentage = $colorPercentage;
        $this->colorListMap = $colorListMap;
    }

    function __toString()
    {
        return 'MinPixel: '.$this->min_pixel.', MaxPixel: '.$this->max_pixel.', AvgPixel: '.$this->avg.', Dominated: '.$this->dominatecolor.'.';
    }

    /**
     * @return string
     */
    public function getMinPixel()
    {
        return $this->min_pixel;
    }

    /**
     * @return string
     */
    public function getMaxPixel()
    {
        return $this->max_pixel;
    }

    /**
     * @return string
     */
    public function getAvgPixel()
    {
        return $this->avg;
    }

    /**
     * @return string
     */
    public function getDominatePixel()
    {
        return $this->dominatecolor;
    }

    /**
     * @return array
     */
    public function getPercentagePixels()
    {
        return $this->colorPercentage;
    }

    /**
     * @return array
     */
    public function getPixels()
    {
        return $this->colorListMap;
    }

}