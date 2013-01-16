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

Load::model('usuario');

class Twitter {

    public $logger = true;

    public $twitterdata;
    protected $consumer_key;
    protected $consumer_secret;

    public function __construct(){
        $config = Api::twitter();
        $this->consumer_key = $config['consumer_key'];
        $this->consumer_secret = $config['consumer_secret'];
        $this->usuario = new Usuario();
    }

    /**
     * Metodo para registrar publicaciones
     *
     * @return array
     */
    public function cuenta() {
        // Verificamos que tengamos una sesión guardada de usuario Twitter
        $this->twitterdata = $this->usuario->getTwitter(Session::get('id'));

        // Verificamos que no existe ninguna sesión guardada
        if (!$this->twitterdata) {

            // Cargamos las librerias necesarias para nuestra aplicación
            Load::lib('twitter/tmhOAuth');
            Load::lib('twitter/tmhUtilities');

            // Inicializamos la libreria de Twitter
            $tmhOAuth = new tmhOAuth(array(
                        'consumer_key' => $this->consumer_key,
                        'consumer_secret' => $this->consumer_secret,
                    ));

            // Definimos el sitio donde estamos usando nuestro código para luego
            // volver después de que iniciemos sesión de nuestro usuario
            $here = tmhUtilities::php_self();

            // Función para manejar los errores devueltos durante la autenticación
            // en Twitter
            function outputError($tmhOAuth) {
                //echo 'Error: ' . $tmhOAuth->response['response'] . PHP_EOL;
                //tmhUtilities::pr($tmhOAuth);
                if ( isset($tmhOAuth->response['error']) && $tmhOAuth->response['error'] == '') {
                    $error = "Llaves inválidas";
                } else {
                    $error = $tmhOAuth->response['error'];
                }
                Flash::error($error);
            }

            // Verificamos que las variables de acceso existan
            if (isset($_SESSION['access_token'])) {

                // Recogemos las sessión de Twitter y almacenamos en BBDD el
                // token y el token_secret del usuario autenticado
                $this->usuario->setTwitter(Session::get('id'), $_SESSION['access_token']['oauth_token'], $_SESSION['access_token']['oauth_token_secret']);

                // Destruimos la variable de sesión
                unset($_SESSION['access_token']);

                // Redireccionamos al página de donde originalmente veniamos
                header("Location: {$here}");

            // Verficamos que la autenticación se haya llevado a cabo
            } elseif (isset($_REQUEST['oauth_verifier'])) {

                // Parametrizamos el user_token y el user_secret desde la sesión
                $tmhOAuth->config['user_token'] = $_SESSION['oauth']['oauth_token'];
                $tmhOAuth->config['user_secret'] = $_SESSION['oauth']['oauth_token_secret'];

                // Verificamos que el user_token y el user_secret sean correctos mediante oauth_verifier
                $code = $tmhOAuth->request('POST', $tmhOAuth->url('oauth/access_token', ''), array(
                    'oauth_verifier' => $_REQUEST['oauth_verifier']
                        ));

                // Si el user_token y user_secret son correctos y refrescamos la página
                if ($code == 200) {
                    $_SESSION['access_token'] = $tmhOAuth->extract_params($tmhOAuth->response['response']);
                    unset($_SESSION['oauth']);
                    header("Location: {$here}");

                // Sino mostramos el error que no retorna
                } else {
                    outputError($tmhOAuth);
                }

            // Si no existe ninguna identidad guardada
            } else {

                // Parametrizamos el tipo de acceso de nuestra aplicacion
                $params = array(
                    'oauth_callback' => $here,
                    'x_auth_access_type' => 'write'
                );

                // Solicitamos a los servidores de Twitter un link valido para
                // que nuestra aplicación tenga permiso sobre la identidad del
                // usuario
                $code = $tmhOAuth->request('POST', $tmhOAuth->url('oauth/request_token', ''), $params);

                // Si la solicitud es exitosa nos devuelve los parametros
                // para generar nuestro link
                if ($code == 200) {
                    $_SESSION['oauth'] = $tmhOAuth->extract_params($tmhOAuth->response['response']);
                    $this->Twitter = $tmhOAuth->url("oauth/authorize", '') . "?oauth_token={$_SESSION['oauth']['oauth_token']}";

                // Sino mostramos el error
                } else {
                    outputError($tmhOAuth);
                    $this->Twitter = "#";
                }
            }

        // Si poseemos poseemos una identidad Twitter asociada
        } else {

            // Cargamos las librerias necesarias para trabajar con Twitter
            Load::lib('twitter/tmhOAuth');
            Load::lib('twitter/tmhUtilities');

            // Parametrizamos con la identidad que tenemos guardada
            $user = $this->twitterdata;
            $tmhOAuth = new tmhOAuth(array(
                        'consumer_key' => $this->consumer_key,
                        'consumer_secret' => $this->consumer_secret,
                        'user_token' => $user['token'],
                        'user_secret' => $user['secret'],
                    ));

            // Auteticamos la identidad
            $code = $tmhOAuth->request('GET', $tmhOAuth->url('1/account/verify_credentials'));

            // Si la autenticacion es correcta extraemos el avatar y el link de
            // identidad guardada
            if ($code == 200) {
                $response = json_decode($tmhOAuth->response['response']);
                $this->twitterdata['image'] = $response->profile_image_url;
                $this->twitterdata['profile'] = 'http://www.twitter.com/' . $response->screen_name;

            // Sino es correcta mostramos el error de autenticacion
            } else {
                tmhUtilities::pr(htmlentities($tmhOAuth->response['response']));
            }
        }
    }

    public function getData() {
        return $this->twitterdata;
    }

    public function getLink() {
        return $this->Twitter;
    }
}
?>