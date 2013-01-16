<?php
/**
 * Dailyscript - app | web | media
 * Modelo para manejar almacenar y obtener el user_token y el user_secret
 * del usuario de Twitter
 * Fecha 05/02/2012
 *
 * @category    API
 * @package     Models
 * @author      Jaro Marval
 * @copyright   Copyright (c) 2010 Dailyscript Team (http://www.dailyscript.com.co)
 * @version     2.0
 */

class Recaptcha {

	private $_error = "";

	/**
	 *	Validar la respuesta a la prueba de recaptcha
	 *
	 */
	public function test($challenge, $response) {

		Load::lib('recaptcha/recaptchalib');
		$app = Api::recaptcha();
		$ip = Utils::getIp();
		$resp = recaptcha_check_answer($app['private_key'],
		                            $ip,
		                            $challenge,
		                            $response);

		if ($resp->is_valid) {
			return True;
		} else {
			$this->_error = $resp->error;
			return False;
		}
	}

	public function getError() {
		$string = array(
			'incorrect-captcha-sol' => 'Solución incorrecta'
			);
		return $string[$this->_error];
	}

}
?>