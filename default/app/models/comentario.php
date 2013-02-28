<?php
/**
 * Dailyscript - app | web | media
 *
 *
 *
 * @category    Administracion
 * @package     Models
 * @author      Jaro Marval
 * @copyright   Copyright (c) 2010 Dailyscript Team (http://www.dailyscript.com.co)
 * @version     1.0
 */

Load::models('post','usuario');

class Post extends ActiveRecord {

    public $logger = true;

    const PENDIENTE = 1;
    const APROBADO = 2;
    const SPAM = 3;
    const ELIMINADO = 4;

    function registarComentario() {

    }

    function filtrarComentarios() {

    }

    function procesarComentario(){

    }

}