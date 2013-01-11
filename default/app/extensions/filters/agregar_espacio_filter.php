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

class AgregarEspacioFilter implements FilterInterface {

    /**
     * Ejecuta el filtro para agregar espacios
     *
     * @param string $s
     * @param array $options
     * @return string
     */

    public static function execute($s, $options=array()) {

        $find       =   array('+');
        $replace    =   array(' ');

        $string     =   str_replace($find, $replace, $s);
        
        return $string;

   }

}
?>
