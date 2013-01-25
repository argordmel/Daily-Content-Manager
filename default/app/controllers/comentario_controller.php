<?php
/**
 * Dailyscript - app | web | media
 *
 *
 *
 * @category    Blog
 * @package     Controllers
 * @author      Iván D. Meléndez
 * @copyright   Copyright (c) 2010 Dailyscript Team (http://www.dailyscript.com.co)
 * @version     1.0
 */

Load::model('post');
//Cargo la librería para el manejo de fechas
Load::lib('ext_date');
//Incluyo la libreria de paginación
Load::lib('paginacion/Paginated');

class ComentarioController extends AppController {

    public function before_filter() {
        if(Input::isAjax()) {
            View::template(null);
        }
    }

    public function index() {
    	View::template(null);
    }

    public function comentar() {
    	View::template(null);

        $return = array(
            'status' => 'error',
            'msg' => 'Error no se han recibido parametros'
            );

        if( Input::hasPost('challenge') ) {
        	Load::model('recaptcha');
            $nombre = Input::post('nombre');
            $email = Input::post('email');
            $web = Input::post('web');
            $challenge = Input::post('challenge');
            $response = Input::post('response');
        	$recaptcha = new Recaptcha();
            if ( $recaptcha->test($challenge, $response) ) {
                $return['status'] = 'ok';
                $return['msg'] = 'Captcha Correcto';
            } else {
                $return['msg'] = $recaptcha->getError();
            }
        }

    	View::response('json');
        print json_encode($return);
    }
}