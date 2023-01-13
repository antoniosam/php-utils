<?php

namespace Ast\UtilString;


class Slugg
{
    static public function slugify($url)
    {

// Tranformamos  a minusculas
        $url = mb_strtolower($url, "UTF-8");
//Rememplazamos caracteres especiales latinos
        $find = array('á', 'é', 'í', 'ó', 'ú', 'ñ');
        $repl = array('a', 'e', 'i', 'o', 'u', 'n');
        $url = str_replace($find, $repl, $url);
// Añaadimos los guiones
        $find = array(' ', '&', '\r\n', '\n', '+');
        $url = str_replace($find, '-', $url);

// Eliminamos y Reemplazamos demás caracteres especiales
        $find = array('/[^a-z0-9\-<>]/', '/[\-]+/', '/<[^>]*>/');
        $repl = array('', '-', '');
        $url = preg_replace($find, $repl, $url);

        return $url;
    }

    /**
     * @param $cadena
     * @return mixed
     */
    static function removeDobleSlash($cadena){
        return str_replace("//","/",$cadena);
    }

}