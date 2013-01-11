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

class StringFilter implements FilterInterface {

    /**
     * Ejecuta el filtro para los string
     *
     * @param string $s
     * @param array $options
     * @return string
     */

    public static function execute($s, $options=array()) {

        $string = filter_var($s, FILTER_SANITIZE_STRING);
        return $string;

   }

}
?>
