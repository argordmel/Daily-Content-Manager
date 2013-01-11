<?php
/**
 * Dailyscript - app | web | media
 *
 *
 *
 * @category    Contacto
 * @package     Controllers
 * @author      Iván D. Meléndez
 * @copyright   Copyright (c) 2010 Dailyscript Team (http://www.dailyscript.com.co)
 * @version     1.0
 */

Load::model('empresa');

class ContactoController extends AppController {

    public function before_filter() {

    }

    public function index() {

        $empresa = new Empresa();
        $this->empresa = $empresa;

        if(Input::hasPost('contacto')) {
            Flash::valid('Los datos se han registrado correctamente. <br />El número del radicado es: '.date("Y-m-d")."-4596");
        }

    }
}
?>
