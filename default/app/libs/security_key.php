<?php
/**
 * Dailyscript - app | web | media
 *
 * Clase que permite crear llave de seguridad en los formularios
 *
 * @package     Libs
 * @author      Iván D. Meléndez
 * @copyright   Copyright (c) 2010 Dailyscript Team (http://www.dailyscript.com.co)
 * @version     1.0
 */

class SecurityKey {

    /**
     * Genera un input tipo hidden con el valor de la llave
     *     
     * @return string
     */
    public static function generateKey(){

        $h = date("G")>12 ? 1 : 0;
        $time = uniqid().mktime($h, 0, 0, date("m"), date("d"), date("Y"));
        $key = sha1($time);
        $_SESSION['rsa32_key'] = $key;
                
        return "<input type='hidden' id='rsa32_key' name='rsa32_key' value='$key' />\r\n";
    }

    /**
     * Devuelve el resultado de la llave almacenada en sesion
     * con la enviada en el form
     *
     * @return boolean
     */
    public static function isValid () {

        $key = isset($_SESSION['rsa32_key']) ? $_SESSION['rsa32_key'] : null;

        if( (!is_null($key) ) && ($key === Input::post('rsa32_key')) ) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Devuelve la ultima llave almacenada en sesion
     *
     * @return string
     */
    public static function getKey() {

        $key = isset($_SESSION['rsa32_key']) ? $_SESSION['rsa32_key'] : null;

        return $key;

    }
    
}
?>