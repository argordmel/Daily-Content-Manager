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
Load::model('grupo');
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

    public function listar($usuarios=null, $parametro=null, $valor=null, $pag='pag',$num='') {
        //Titulo de la página
        $this->title = 'Usuario';
        //Determino si el estado corresponde al paginador. Ejemplo: post/listar/pag/2/
        if($usuarios && $usuarios == 'pag') {
            $pag = $usuarios;
            $num = $parametro;
        } else if($parametro && $parametro == 'pag') {//Determino si el parametro es el paginador. Ejemplo: post/listar/borradores/pag/2/
            $pag = $parametro;
            $num = $valor;
        } else if($valor && $valor == 'pag') {//Determino si el valor es el paginador. Ejemplo: post/listar/categoria/pag/2/
            Flash::error('Acceso denegado al sistema');
            Router::redirect('dc-admin/');
        }

        $usuario = new Usuario();

        //Determino la visibilidad y el estado de los post a listar
        $usuarios = ( ($usuarios == 'pag') or ($usuarios == null) ) ? 'todos' : $usuarios;

        switch($usuarios) {
            case 'administradores':
                $usuarios = Grupo::ADMINISTRADOR;
                break;
            case 'editores':
                $usuarios = Grupo::EDITOR;
                break;
            case 'autores':
                $usuarios = Grupo::AUTOR;
                break;
            case 'colaboradores':
                $usuarios = Grupo::COLABORADOR;
                break;

            default:
                $usuarios = 'todos';
        }
        //Determino el parametro a filtrar
        $parametro = ($parametro == 'pag')  ? null : $parametro;

        // //Variable por si se desea filtrar en la vista según el estado
        // $this->actual = strtolower($estado);

        //Numero de la pagina
        $this->numero   = ( Filter::get($num,'numeric') > 0 ) ? Filter::get($num,'numeric') : 1;

        //Contador del datagrid que depende del numero de la página
        $this->contador = ( ($pag === 'pag') && ($this->numero > 1) ) ? ( ($this->numero * 15) - 14 ) : 1;
        $usuario = $usuario->listarUsuarios($usuarios);

        //Creo un paginador con el resultado, que muestre 15 filas y empieze por el numero de la página
        $this->usuario = new Paginated($usuario,15,$this->numero);

        // Variable para prevenir que se manipule el contador através de la url
        $this->registros = count($usuario);
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
        $this->title = $this->subtitle = 'Nueva Usuario';
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
        //Titulo de la página
        $this->title = $this->subtitle = "Modificar Usuario";

        $datos = new Usuario();

        $usuario = $datos->buscarUsuario(Session::get('id'), '');

        $this->nombre = $usuario->nombre;
        $this->apellido = $usuario->apellido;
        $this->email = $usuario->mail;
        $this->login = $usuario->login;
        $this->tipo = $usuario->grupo_id;

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

    /**
     * Método para editar usuarios
     */
    public function editar($id=null,$key='key',$valueKey='') {
        //Titulo de la páginas
        $this->title = $this->subtitle = "Modificar Usuario";

        $datos = new Usuario();

        $usuario = $datos->buscarUsuario($id, '');

        $this->nombre = $usuario->nombre;
        $this->apellido = $usuario->apellido;
        $this->email = $usuario->mail;
        $this->login = $usuario->login;

        //Array para determinar la visibilidad de los post
        $this->tipo = $usuario->grupo_id;
        View::select('perfil');

        //Verifico si ha enviado los datos a través del formulario
        if(Input::hasPost('usuario')) {
            //Verifico que el formulario coincida con la llave almacenada en sesion
            if(SecurityKey::isValid()) {
                Load::models('usuario');
                $usuario = new Usuario(Input::post('usuario'));
                $resultado = $usuario->registrarUsuario();
                if($resultado) {
                    View::select('listar');
                }
            } else {
                Flash::info('La llave de acceso ha caducado. Por favor intente nuevamente.');
            }
        } else {
            //Armo la llave con el código de la url
            if($valueKey !== md5($id.$this->ipKey.$this->expKey.'usuario')) {
                // $post = new Post();
                // $result = $post->verPost($id);
            // } else {
                Flash::error('Acceso incorrecto al sistema.');
                Router::redirect('dc-admin/usuario/listar/');
            }
        }
    }

    public function eliminar($id=null,$key='key',$valueKey='') {
        if($valueKey === md5($id.$this->ipKey.$this->expKey.'usuario')) {
            $datos = new Usuario();
            if ( $datos->eliminarUsuario($id) ){
                Flash::valid('Usuario eliminado con éxito');
            }
            Router::redirect('dc-admin/usuario/listar/');
        } else {
            Flash::error('Acceso incorrecto al sistema.');
            Router::redirect('dc-admin/usuario/listar/');
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

    public function eliminarFacebook($id) {
        $usuario = new Usuario();

        if (Session::get('id') != $id)  {
            Flash::error('No puedes eliminar una cuenta de Twitter que no es suya');
            $location = '/dc-admin/';
        } else {
            if ( $usuario->setFacebook($id) ){
                Flash::valid('Cuenta de Facebook borrada exitosamente');
            } else {
                Flash::error('Error al eliminar la cuenta de Facebook');
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
