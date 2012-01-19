<?php
/**
 * Dailyscript - app | web | media
 *
 *
 *
 * @category    Principal
 * @package     Controllers
 * @author      Iván D. Meléndez
 * @copyright   Copyright (c) 2010 Dailyscript Team (http://www.dailyscript.com.co)
 * @version     1.0
 */

class PrincipalController extends ApplicationController {

    public function before_filter() {
        View::template('principal');
    }

    public function index() {


    }
}
?>
