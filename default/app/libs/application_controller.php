<?php
/**
 * @category Kumbia
 * @package ControllerDeprecated
 * @deprecated Ahora se usa AppController.
 * Se eliminará despues de la beta2
 *
 * Antiguo ApplicationController desaconsejado, ahora se usa el AppController.
 *
 * Todos los controladores heredan de esta clase en un nivel superior
 * por lo tanto los métodos aquí definidos estan disponibles para
 * cualquier controlador.
 */
/**
 * @see Tags
 */
require_once CORE_PATH . 'extensions/helpers/tags.php';

/**
 * @see ControllerDeprecated Antiguo controlador por compatibilidad
 */
require_once CORE_PATH . 'kumbia/controller_deprecated.php';

/**
 * (Obsoleto) Clase controladora que extienden los demás controllers
 *
 * @deprecated Ahora se usa AppController.
 * Se eliminará despues de la beta2.
 * Se mantiene para portar apps fácilmente de 0.5 y beta1.
 *
 * @category Kumbia
 * @package ControllerDeprecated
 */
class ApplicationController extends ControllerDeprecated
{
    /**
     * Titulo de la pagina web
     */
    public $title;
    /**
     * Fecha de vencimiento de la llave en la url, para prevenir que se manipule la url
     */
    public $expKey;
    /**
     * Direccion ip de la llave en la url, para prevenir que se manipule la url
     */
    public $ipKey;
    /**
     * Llave encriptada
     */
    public $validKey;


    final protected function initialize()
    {
        // Verifico si esta en el módulo de administración
        if (Router::get('module') == 'dc-admin') {
            // Verifico si esta logueado el usuario
            if (Auth::is_valid()) {
                //Si esta logueado no debe ingresar nuevamente a iniciar sesión
                if(Router::get('controller') == 'usuario' && Router::get('action') == 'entrar') {
                    Router::redirect('dc-admin/');
                }
                View::template('admin');
            } else {
                View::template('login');
                //Verifico que no este en el controlador usuario y en los métodos entrar o salir para no generar una redirección infinita
                if(Router::get('controller') != 'usuario' && ( Router::get('action') != 'entrar' && Router::get('action') != 'salir') ) {
                    Flash::warning('No haz iniciado sesión.');
                    Router::redirect('dc-admin/usuario/entrar/');
                    return false;
                }
            }

        }
        $this->expKey = date("Y-m-d");
        $this->ipKey = Utils::getIp();
    }

    final protected function finalize()
    {
        /*
         *  Asigno el título a las páginas para que se vean asi: <br>
         *  Escritorio ‹ Nombre de la aplicacion
         *  Noticias ‹ Nombre del blog
         *  Titulo post ‹ Nombre del blog
         */
        if(Router::get('module') == 'dc-admin') {
            if( (Router::get('action') != 'entrar') && (Router::get('action') != 'salir') ) {
                $this->title = trim($this->title).' ‹ '.APP_NAME;
            } else {
                $this->title = NOMBRE_DEL_BLOG .' › Entrar';
            }
        } else if (Router::get('controller') == 'blog') {
            $this->title = trim($this->title).' ‹ '.NOMBRE_DEL_BLOG;
        } else {
            $this->title = ucfirst(Router::get('controller')).' ‹ '.NOMBRE_DEL_BLOG;
        }

        parent::finalize(); // No tocar
    }

}
