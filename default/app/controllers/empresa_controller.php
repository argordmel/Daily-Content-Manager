<?php
/**
 * Dailyscript - app | web | media
 *
 *
 *
 * @category    La empresa
 * @package     Controllers
 * @author      Iván D. Meléndez
 * @copyright   Copyright (c) 2010 Dailyscript Team (http://www.dailyscript.com.co)
 * @version     1.0
 */

Load::models('empresa');

class EmpresaController extends AppController {

    public function before_filter() {

    }

    public function index() {
        $empresa = new Empresa();
        $this->empresa = $empresa;
    }
}
?>
