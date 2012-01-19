<?php
/**
 * Dailyscript - app | web | media
 *
 * Clase para truncar palabras, texto o digidos
 *
 * @package     Libs
 * @author      Iván D. Meléndez
 * @copyright   Copyright (c) 2010 Dailyscript Team (http://www.dailyscript.com.co)
 * @version     1.0r1
 */

class Truncar {

    public static function palabra($texto, $limite, $fin = '&nbsp;...') {
        $texto= strip_tags($texto);
        if (strlen($texto) > $limite) {
            $palabras	=	str_word_count($texto, 2);
            $pos        =	array_keys($palabras);
            $texto_aux	=	@substr($texto, 0, $pos[$limite]).$fin;
            if($texto_aux != $fin) {
                $texto = $texto_aux;
            }
	}
	return $texto;
    }

    public static function digito($texto, $limite, $fin = '...') {
        $texto= strip_tags($texto);
        if (strlen($texto) > $limite) {
            $texto_aux	=	@substr($texto, 0, $limite).$fin;           
             if($texto_aux != $fin) {
                $texto = $texto_aux;
             }
        }
        return $texto;        
    }
}
?>
