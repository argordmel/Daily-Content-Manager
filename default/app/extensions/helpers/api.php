<?php
/**
 * Dailyscript - app | web | media
 *
 * Extension para acceso a los api externos
 *
 * @category    Extensions
 * @author      Jaro Marval
 * @package     Helpers
 * @copyright   Copyright (c) 2010 Dailyscript Team (http://www.dailyscript.com.co)
 * @version     1.0
 */

class Api {

	protected static $_file = 'api';

	public static function _openAPI($api){
        $file = Config::read(self::$_file, TRUE);
        $keys = array();
        foreach ($file[$api] as $key => $value) {
        	$keys[$key] = $value;
        }
		return $keys;
	}

	public static function twitter() {
		return self::_openAPI('twitter');
	}

	public static function facebook() {
		return self::_openAPI('facebook');
	}

	public static function bitly() {
		return self::_openAPI('bitly');
	}

	public static function recaptcha() {
		return self::_openAPI('recaptcha');
	}

}