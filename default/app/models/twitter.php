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
 * @copyright   Copyright (c) 2013 Icterus Team (http://www.icter.us)
 * @version     2.0
 */

// Cargamos las librerias necesarias para trabajar con Twitter
Load::lib('redes_sociales/twitter/tmhOAuth');
Load::lib('redes_sociales/twitter/tmhUtilities');

Load::model('usuario');

class Twitter {

    public $twitterdata;
    protected $consumer_key;
    protected $consumer_secret;

    /**
    * Modelo para manejar cuentas twitter
    */
    public function __construct(){
        $config = Api::twitter();
        $this->here = tmhUtilities::php_self();
        $this->consumer_key = $config['consumer_key'];
        $this->consumer_secret = $config['consumer_secret'];
        $this->usuario = new Usuario();
    }

    public function cuenta() {
	    // Verificamos que tengamos una sesión guardada de usuario Twitter
	    $this->twitterdata = $this->usuario->getTwitter(Session::get('id'));
		$tmhOAuth = new tmhOAuth(array(
		  'consumer_key'    => $this->consumer_key,
		  'consumer_secret' => $this->consumer_secret
		));
		if ( !$this->twitterdata ) {

			// Ya solicitado el access token
			if ( isset($_SESSION['access_token']) ) {
				// Almacenamos en el la base de datos
				$this->usuario->setTwitter(Session::get('id'), $_SESSION['access_token']['oauth_token'], $_SESSION['access_token']['oauth_token_secret']);
				// Destruimos la variable de sesión
                unset($_SESSION['access_token']);
                // Redireccionamos al página de donde originalmente veniamos
                header("Location: {$here}");
			} elseif (isset($_REQUEST['oauth_verifier'])) {
				$tmhOAuth->config['user_token']  = $_SESSION['oauth']['oauth_token'];
				$tmhOAuth->config['user_secret'] = $_SESSION['oauth']['oauth_token_secret'];

				$code = $tmhOAuth->request('POST', $tmhOAuth->url('oauth/access_token', ''), array('oauth_verifier' => $_REQUEST['oauth_verifier']));

				if ($code == 200) {
					$_SESSION['access_token'] = $tmhOAuth->extract_params($tmhOAuth->response['response']);
					unset($_SESSION['oauth']);
					header("Location: {$here}");
				} else {
					$this->outputError($tmhOAuth);
				}
			} else {
				$code = $tmhOAuth->request('POST', $tmhOAuth->url('oauth/request_token', ''), array('oauth_callback' => $this->here));
				if ($code == 200) {
	    			$_SESSION['oauth'] = $tmhOAuth->extract_params($tmhOAuth->response['response']);
					$this->Twitter =$tmhOAuth->url("oauth/authorize", '') .  "?oauth_token={$_SESSION['oauth']['oauth_token']}";
				} else {
					$this->outputError($tmhOAuth);
					$this->Twitter = '#';
				}
			}

		} else {
	        // Parametrizamos con la identidad que tenemos guardada
	        $user = $this->twitterdata;
	        $this->twitterdata['error'] = "Pepito";
	        $tmhOAuth = new tmhOAuth(array(
	                    'consumer_key' => $this->consumer_key,
	                    'consumer_secret' => $this->consumer_secret,
	                    'user_token' => $user['token'],
	                    'user_secret' => $user['secret'],
	                ));

	        // Auteticamos la identidad
	        $code = $tmhOAuth->request('GET', $tmhOAuth->url('1/account/verify_credentials'));
			if ($code == 200) {
    			$response = json_decode($tmhOAuth->response['response']);
                $this->twitterdata['image'] = $response->profile_image_url;
                $this->twitterdata['profile'] = 'http://www.twitter.com/' . $response->screen_name;
            } else {
            	$this->outputError($tmhOAuth);
            }
		}

    }


    public function outputError($tmhOAuth) {
		if ( isset($tmhOAuth->response['error']) && $tmhOAuth->response['error'] == '') {
			$error = "Llaves inválidas";
		} else {
			$error = $tmhOAuth->response['error'];
		}
		Flash::error($error);
    }

    public function getData() {
        return $this->twitterdata;
    }

    public function getLink() {
        return $this->Twitter;
    }
}