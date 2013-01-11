<?php
/**
 * Dailyscript - app | web | media
 *
 * Clase para el manejo de texto y otras cosas
 *
 * @package     Libs
 * @author      Iván D. Meléndez
 * @copyright   Copyright (c) 2010 Dailyscript Team (http://www.dailyscript.com.co)
 * @version     1.0
 */

class Utils {

    /*
     * Metodo para obtener la ip real del cliente
     */
    public static function getIp() {

        if( isset($_SERVER['HTTP_X_FORWARDED_FOR']) ) {
            $client_ip = ( !empty($_SERVER['REMOTE_ADDR']) ) ? $_SERVER['REMOTE_ADDR'] : ( ( !empty($_ENV['REMOTE_ADDR']) ) ? $_ENV['REMOTE_ADDR'] : "unknown" );
            $entries = split('[, ]', $_SERVER['HTTP_X_FORWARDED_FOR']);
            reset($entries);
            while (list(, $entry) = each($entries)) {
                $entry = trim($entry);
                if ( preg_match("/^([0-9]+\\.[0-9]+\\.[0-9]+\\.[0-9]+)/", $entry, $ip_list) ) {
                    $private_ip = array(
                                        '/^0\\./',
                                        '/^127\\.0\\.0\\.1/',
                                        '/^192\\.168\\..*/',
                                        '/^172\\.((1[6-9])|(2[0-9])|(3[0-1]))\\..*/',
                                        '/^10\\..*/');
                    $found_ip = preg_replace($private_ip, $client_ip, $ip_list[1]);
                    if ($client_ip != $found_ip) {
                        $client_ip = $found_ip;
                        break;
                    }
                }
            }
        }
        else {
            $client_ip = ( !empty($_SERVER['REMOTE_ADDR']) ) ? $_SERVER['REMOTE_ADDR'] : ( ( !empty($_ENV['REMOTE_ADDR']) ) ? $_ENV['REMOTE_ADDR'] : "unknown" );
        }

        return $client_ip;
    }

    /*
     * Metodo para resaltar palabras de una cadena de texto
     */
    public static function resaltar($palabra, $texto) {
        $reemp  =   str_ireplace($palabra,'%s',$texto);
        $aux    =   $reemp;
        $veces  =   substr_count($reemp,'%s');
        if($veces == 0) {
            return $texto;
        }
        $palabras_originales    =   array();
        for($i = 0 ; $i < $veces ; $i ++) {
            $palabras_originales[]  =   '<strong style="color: red;background-color: #ffffff;">'.substr($texto,strpos($aux,'%s'),strlen($palabra)).'</strong>';
            $aux    =   substr($aux,0,strpos($aux,'%s')).$palabra.substr($aux,strlen(substr($aux,0,strpos($aux,'%s')))+2);
        }
        return vsprintf($reemp,$palabras_originales);
    }

    /**
     * Metodo para crear el slug de los titulos, categorias y etiquetas
     */
    public static function slug ($string, $separator = '-', $length = 100) {
        $search = explode(',', 'ç,Ç,ñ,Ñ,æ,Æ,œ,á,Á,é,É,í,Í,ó,Ó,ú,Ú,à,À,è,È,ì,Ì,ò,Ò,ù,Ù,ä,ë,ï,Ï,ö,ü,ÿ,â,ê,î,ô,û,å,e,i,ø,u,Š,Œ,Ž,š,¥');
        $replace = explode(',', 'c,C,n,N,ae,AE,oe,a,A,e,E,i,I,o,O,u,U,a,A,e,E,i,I,o,O,u,U,ae,e,i,I,oe,ue,y,a,e,i,o,u,a,e,i,o,u,s,o,z,s,Y');
        $string = str_replace($search, $replace, $string);
        $string = strtolower($string);
        $string = preg_replace('/[^a-z0-9_]/i', $separator, $string);
        $string = preg_replace('/\\' . $separator . '[\\' . $separator . ']*/', $separator, $string);
        if (strlen($string) > $length) {
            $string = substr($string, 0, $length);
        }
        $string = preg_replace('/\\' . $separator . '$/', '', $string);
        $string = preg_replace('/^\\' . $separator . '/', '', $string);
        return $string;
    }

    /**
     * Balance Tags
     *
     *
     * @param string $text
     * @return string
     */
    public static function balanceTags ($text) {
        $tagstack = array();
        $stacksize = 0;
        $tagqueue = '';
        $newtext = '';
        $single_tags = array('br' , 'hr' , 'img' , 'input'); //Known single-entity/self-closing tags
        $nestable_tags = array('blockquote' , 'div' , 'span'); //Tags that can be immediately nested within themselves
        # WP bug fix for comments - in case you REALLY meant to type '< !--'
        $text = str_replace('< !--', '<    !--', $text);
        # WP bug fix for LOVE <3 (and other situations with '<' before a number)
        $text = preg_replace('#<([0-9]{1})#', '&lt;$1', $text);
        while (preg_match("/<(\/?\w*)\s*([^>]*)>/", $text, $regex)) {
            $newtext .= $tagqueue;
            $i = strpos($text, $regex[0]);
            $l = strlen($regex[0]);
            // clear the shifter
            $tagqueue = '';
            // Pop or Push
            if ($regex[1][0] == "/") { // End Tag
                $tag = strtolower(substr($regex[1], 1));
                // if too many closing tags
                if ($stacksize <= 0) {
                    $tag = '';
                    //or close to be safe $tag = '/' . $tag;
                } else  // if stacktop value = tag close value then pop
                    if ($tagstack[$stacksize - 1] == $tag) { // found closing tag
                        $tag = '</' . $tag . '>'; // Close Tag
                        // Pop
                        array_pop($tagstack);
                        $stacksize --;
                    } else { // closing tag not at top, search for it
                        for ($j = $stacksize - 1; $j >= 0; $j --) {
                            if ($tagstack[$j] == $tag) {
                                // add tag to tagqueue
                                for ($k = $stacksize - 1; $k >= $j; $k --) {
                                    $tagqueue .= '</' . array_pop($tagstack) . '>';
                                    $stacksize --;
                                }
                                break;
                            }
                        }
                        $tag = '';
                    }
            } else { // Begin Tag
                $tag = strtolower($regex[1]);
                // Tag Cleaning
                // If self-closing or '', don't do anything.
                if ((substr($regex[2], - 1) == '/') || ($tag == '')) {} // ElseIf it's a known single-entity tag but it doesn't close itself, do so
                elseif (in_array($tag, $single_tags)) {
                    $regex[2] .= '/';
                } else { // Push the tag onto the stack
                    // If the top of the stack is the same as the tag we want to push, close previous tag
                    if (($stacksize > 0) && ! in_array($tag, $nestable_tags) && ($tagstack[$stacksize - 1] == $tag)) {
                        $tagqueue = '</' . array_pop($tagstack) . '>';
                        $stacksize --;
                    }
                    $stacksize = array_push($tagstack, $tag);
                }
                // Attributes
                $attributes = $regex[2];
                if ($attributes) {
                    $attributes = ' ' . $attributes;
                }
                $tag = '<' . $tag . $attributes . '>';
                //If already queuing a close tag, then put this tag on, too
                if ($tagqueue) {
                    $tagqueue .= $tag;
                    $tag = '';
                }
            }
            $newtext .= substr($text, 0, $i) . $tag;
            $text = substr($text, $i + $l);
        }
        // Clear Tag Queue
        $newtext .= $tagqueue;
        // Add Remaining text
        $newtext .= $text;
        // Empty Stack
        while ($x = array_pop($tagstack)) {
            $newtext .= '</' . $x . '>'; // Add remaining tags to close
        }
        $newtext = str_replace("< !--", "<!--", $newtext);
        $newtext = str_replace("<    !--", "< !--", $newtext);
        return $newtext;
    }
    
}

?>
