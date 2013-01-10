<?php
/**
 * Dailyscript - app | web | media
 *
 *
 *
 * @category    Extensions
 * @author      Iván D. Meléndez
 * @package     Filters
 * @copyright   Copyright (c) 2010 Dailyscript Team (http://www.dailyscript.com.co)
 * @version     1.0
 */

class AcentoFilter implements FilterInterface {

    /**
     * Ejecuta el filtro para limpiar acentos
     *
     * @param string $s
     * @param array $options
     * @return string
     */

    public static function execute($s, $options=array()) {

        $find       =   array('á','é','í','ó','ú','ü','Á','É','Í','Ó','Ú','Ü');
        $replace    =   array('a','e','i','o','u','u','A','E','I','O','U','U');

        $string     =   str_replace($find, $replace, $s);
        
        return $string;

   }

}
?>
