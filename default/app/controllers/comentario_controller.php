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
}