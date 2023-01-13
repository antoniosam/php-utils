<?php
/**
 * User: marcosamano
 * Date: 26/11/18
 *
 */

namespace Ast\UtilString;


class ParsetoHtml
{
    public static  function  breakline($cadena){
        $cadena = str_replace("\r",'<br/>',$cadena);
        return str_replace("\n",'<br/>',$cadena);
    }

}