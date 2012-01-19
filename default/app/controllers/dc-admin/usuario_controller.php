<?php
/**
 * Dailyscript - app | web | media
 *
 *
 *
 * @category    Administracion
 * @package     Controllers
 * @author      Iván D. Meléndez
 * @copyright   Copyright (c) 2010 Dailyscript Team (http://www.dailyscript.com.co)
 * @version     1.0
 */

Load::models('usuario');

class UsuarioController extends ApplicationController {
    
    public function before_filter() {

    }

    public function index() {
        
    }

    public function entrar() {        
        $usuario = new Usuario();
        $usuario->entrar();
    }

    public function salir() {        
        $usuario = new Usuario();
        $usuario->salir();
    }

    
}
?>
