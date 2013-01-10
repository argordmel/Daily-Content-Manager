<?php
/**
 * KumbiaPHP web & app Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://wiki.kumbiaphp.com/Licencia
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@kumbiaphp.com so we can send you a copy immediately.
 *
 * Validate es una Clase que realiza validaciones Lógicas
 * 
 * @category   KumbiaPHP
 * @package    validate 
 * @copyright  Copyright (c) 2005-2009 Kumbia Team (http://www.kumbiaphp.com)
 * @license    http://wiki.kumbiaphp.com/Licencia     New BSD License
 */
class Validate
{
    /**
     * Almacena el mensaje de error
     *
     * @var String
     */
    public static $error = NULL;
    /**
     * Almacena la Expresion Regular
     *
     * @var String
     */
    public static $regex = NULL;
    /**
     * Valida que int
     *
     * @param int $check
     * @return bool
     */
    public static function int ($check)
    {
        return filter_var($check, FILTER_VALIDATE_INT);
    }
    /**
     * Valida que una cadena este entre un rango.
     * Los espacios son contados
     * Retorna true si el string $value se encuentra entre min and max
     *
     * @param string $value
     * @param int $min
     * @param int $max
     * @return bool
     */
    public static function between ($value, $min = 0, $max = NULL)
    {
        $length = strlen($value);
        return ($length >= $min && $length <= $max);
    }
    /**
     * Valida que es un número se encuentre 
     * en un rango minímo y máximo
     * 
     * @param int $value
     * @param int $min
     * @param int $max
     */
    public static function intBetween($value, $min=0, $max=NULL)
    {
        $int_options = array('options'=> array('min_range'=>$min, 'max_range'=>$max));
        return filter_var($value, FILTER_VALIDATE_INT, $int_options);
    }
    /**
     * Valida que un string contenga una longitud mínima
     * retorna true si la longitud del $value es menor que el $min
     *
     * @param string $value
     * @param int $min
     * @return bool
     */
    public static function minLength($value, $min)
    {
        return (strlen($value) < $min);
    }
    /**
     * Valida que un string contenga una longitud máxima
     * retorna true si la longitud del $value es mayor que el $max 
     *
     * @param string $value
     * @param int $max
     * @return bool
     */
    public static function maxLength ($value, $max) 
    {
        return (strlen($value) > $max);
    }
    /**
     * Valida que un valor se encuentre en una lista
     * Retorna tru si el string $value se encuentra en la lista $list
     *
     * @param string $value
     * @param array $list
     * @return bool
     */
    public static function inList($value, $list)
    {
        return in_array($check, $list);
    }
    
    /**
     * Valida que una cadena sea un mail
     *
     * @param string $mail
     * @return bool
     */
    public static function mail ($mail)
    {
        return filter_var($mail, FILTER_VALIDATE_EMAIL);
    }
    /**
     * Valida URL
     *
     * @param string $url
     * @return bool
     */
    public static function url ($url)
    {
        return filter_var($url, FILTER_VALIDATE_URL, FILTER_FLAG_HOST_REQUIRED);
    }
    /**
     * Valida que sea IPv4
     *
     * @param String $ip
     * @return bool
     */
    public static function ip ($ip)
    {
        return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);
    }
    /**
     * Valida que un string no sea null o contenga solo espacios
     *
     * @param string $check
     * @return bool
     */
    public static function isNull ($check)
    {
        return !self::custom($check, '/[^\\s]/');
    }
    /**
     * Valida que un String sea alpha-num (incluye caracteres acentuados)
     *
     * @param string $string
     * @return bool
     */
    public static function alNum ($string)
    {
        return self::custom($string, '/^[\p{Ll}\p{Lm}\p{Lo}\p{Lt}\p{Lu}\p{Nd}]+$/mu');
    }
    /**
     * Valida una fecha
     *
     * @param string $value fecha a validar acorde al formato indicado
     * @param string $format formato de fecha. acepta: d-m-y, y-m-d, m-d-y, donde el "-" puede ser cualquier caracter 
     *                       de separacion incluso un espacio en blanco o ".", exceptuando (d,m,y o números).
     * @return boolean
     */
    public static function date ($value, $format = 'd-m-y')
    {
        // busca el separador removiendo los caracteres de formato
        $separator = str_replace(array('d' , 'm' , 'y'), '', $format);
        $separator = $separator[0]; // el separador es el primer caracter
        if ($separator && substr_count($value, $separator) == 2) {
            switch (str_replace($separator, '', $format)) {
                case 'dmy':
                    list ($day, $month, $year) = explode($separator, $value);
                    break;
                case 'mdy':
                    list ($month, $day, $year) = explode($separator, $value);
                    break;
                case 'ymd':
                    list ($year, $month, $day) = explode($separator, $value);
                    break;
                default:
                    return false;
            }
            if (ctype_digit($month) && ctype_digit($day) && ctype_digit($year)) {
                return checkdate($month, $day, $year);
            }
        }
        return false;
    }
    /**
     * Valida un string dada una Expresion Regular
     *
     * @param string $check
     * @param string $regex
     * @return bool
     */
    public static function custom ($check, $regex)
    {
        return filter_var($check, FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => $regex)));
    }
    
    /**
     * Valida si es un número decimal
     * 
     * @param string $value
     * @param string $decimal
     * @return boolean
     */
    public static function decimal($value, $decimal = ',')
    {
		return filter_var($value, FILTER_VALIDATE_FLOAT, array('options' => array('decimal' => $decimal)));
	}
}
