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

Load::model('usuario');
Load::model('post');
Load::lib('paginacion/Paginated');

class UsuarioController extends AppController {

    public function before_filter() {
        if(Input::isAjax()) {
            View::template(null);
        }
    }

    public function index() {
        Router::toAction('listar/');
    }

    public function listar($estado=null, $parametro=null, $valor=null, $pag='pag',$num='') {
        //Titulo de la página
        $this->title = 'Usuario';
        //Determino si el estado corresponde al paginador. Ejemplo: post/listar/pag/2/
        if($estado && $estado == 'pag') {
            $pag = $estado;
            $num = $parametro;
        } else if($parametro && $parametro == 'pag') {//Determino si el parametro es el paginador. Ejemplo: post/listar/borradores/pag/2/
            $pag = $parametro;
            $num = $valor;
        } else if($valor && $valor == 'pag') {//Determino si el valor es el paginador. Ejemplo: post/listar/categoria/pag/2/
            Flash::error('Acceso denegado al sistema');
            Router::redirect('dc-admin/');
        }
        $post = new Post();

        //Determino la visibilidad y el estado de los post a listar
        $visibilidad = ($estado == 'privados') ? Post::PRIVADO : 'todos';
        $estado = ( ($estado == 'pag') or ($estado == null) or ($estado == 'privados') ) ? 'todos' : $estado;
        //Determino el parametro a filtrar
        $parametro = ($parametro == 'pag')  ? null : $parametro;

        //Filtro los post
        $post = $post->filtrarPost($estado, $visibilidad, $parametro, $valor, 'desc');

        //Variable por si se desea filtrar en la vista según el estado
        $this->actual = strtolower($estado);
        //Numero de la pagina
        $this->numero   = ( Filter::get($num,'numeric') > 0 ) ? Filter::get($num,'numeric') : 1;
        //Contador del datagrid que depende del numero de la página
        $this->contador = ( ($pag === 'pag') && ($this->numero > 1) ) ? ( ($this->numero * 15) - 14 ) : 1;
        //Creo un paginador con el resultado, que muestre 15 filas y empieze por el numero de la página
        $this->post = new Paginated($post,15,$this->numero);
        //Variable para prevenir que se manipule el contador através de la url
        $this->registros = count($post);
        $this->contador = ( $this->registros >= $this->contador ) ? $this->contador : 1;
    }

    public function entrar() {
        $usuario = new Usuario();
        $usuario->entrar();
    }

    public function salir() {
        $usuario = new Usuario();
        $usuario->salir();
    }

    /**
     * Método para agregar un nuevo usuario
     */
    public function agregar() {
        Flash::info(Router::get('module'));
        //Titulo de la página
        $this->title = 'Nueva usuario';
        //Ckeck de los radios para habilitar comentarios
        // $this->check_si = (HABILITAR_USUARIO) ? false : true;
        // $this->check_no = (disabled) ? true : false;
        $this->check_si = true;
        $this->check_no = false;

        //Array para determinar la visibilidad de los post
        $this->tipo = array(
            Grupo::ADMINISTRADOR => 'Administrador',
            Grupo::AUTOR => 'Autor',
            Grupo::COLABORADOR => 'Colaborador',
            Grupo::EDITOR => 'Editor'
            );

        //Verifico si ha enviado los datos a través del formulario
        if(Input::hasPost('usuario')) {
            //Verifico que el formulario coincida con la llave almacenada en sesion
            if(SecurityKey::isValid()) {
                Load::models('usuario');
                $usuario = new Usuario(Input::post('usuario'));
                $resultado = $usuario->registrarUsuario();
                if($resultado) {
                    View::select('usuario');
                }/* else {
                    //Hago persitente los datos
                    $this->categoria = Input::post('categorias');
                    $this->etiquetas = Input::post('etiquetas');
                }
                $this->post = $post;*/
            } else {
                Flash::info('La llave de acceso ha caducado. Por favor intente nuevamente.');
            }
        }
    }

    /**
     * Método para agregar un nuevo usuario
     */
    public function perfil() {
        $usuario = new Usuario();

        $dataUsuario = $usuario->buscarUsuario(Session::get('id'), '');

        //Titulo de la página
        $this->title = "Modificar tu Perfil";

        $this->nombre = $dataUsuario->nombre;
        $this->apellido = $dataUsuario->apellido;
        $this->email = $dataUsuario->mail;
        $this->login = $dataUsuario->login;

        //Array para determinar la visibilidad de los post
        $this->tipo = array(
            Grupo::ADMINISTRADOR => 'Administrador',
            Grupo::AUTOR => 'Autor',
            Grupo::COLABORADOR => 'Colaborador',
            Grupo::EDITOR => 'Editor'
            );

        //Verifico si ha enviado los datos a través del formulario
        if(Input::hasPost('usuario')) {
            //Verifico que el formulario coincida con la llave almacenada en sesion
            if(SecurityKey::isValid()) {
                Load::models('usuario');
                $usuario = new Usuario(Input::post('usuario'));
                $resultado = $usuario->registrarUsuario();
                if($resultado) {
                    View::select('usuario');
                }
            } else {
                Flash::info('La llave de acceso ha caducado. Por favor intente nuevamente.');
            }
        }
    }

    public function eliminarTwitter($id) {
        $usuario = new Usuario();

        if (Session::get('id') != $id)  {
            Flash::error('No puedes eliminar una cuenta de Twitter que no es suya');
            $location = '/dc-admin/';
        } else {
            if ( $usuario->setTwitter($id) ){
                Flash::valid('Cuenta de Twitter borrada exitosamente');
            } else {
                Flash::error('Error al eliminar la cuenta de Twitter');
            }
            $location = Utils::getBack();
        }
        Router::redirect($location);
    }

    public function checkEmail(){
        $salida['status'] = "ERROR";

        if ( Input::hasPost('email') ) {

            $email = Input::post('email');
            Load::model('usuario');
            $usuario = new Usuario();

            if ( !$usuario->buscarEmail($email) ) {
                $salida['status'] = "OK";
            }

        }

        View::template(null);
        View::response('json');
        print json_encode($salida);
    }

    public function checkLogin(){
        $salida['status'] = "ERROR";

        if ( Input::hasPost('login') ) {

            $login = Input::post('login');
            Load::model('usuario');
            $usuario = new Usuario();

            if ( !$usuario->buscarLogin($login) ) {
                $salida['status'] = "OK";
            }

        }

        View::template(null);
        View::response('json');
        print json_encode($salida);
    }
}
?>
