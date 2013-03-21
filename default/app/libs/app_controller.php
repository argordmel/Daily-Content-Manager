<?php
/**
 * @see Controller nuevo controller
 */
require_once CORE_PATH . 'kumbia/controller.php';

/**
 * Controlador principal que heredan los controladores
 *
 * Todas las controladores heredan de esta clase en un nivel superior
 * por lo tanto los metodos aqui definidos estan disponibles para
 * cualquier controlador.
 *
 * @category Kumbia
 * @package Controller
 */
class AppController extends Controller
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

        if (FAVICON == 'on') Html::headlink(PUBLIC_PATH.'img/icon.png','rel="shortcut icon"');

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
                if(!( Router::get('controller') == 'usuario' && Router::get('action') == 'entrar' ) && !( Router::get('controller') == 'usuario' && Router::get('action') == 'salir') ) {
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

    }

}
