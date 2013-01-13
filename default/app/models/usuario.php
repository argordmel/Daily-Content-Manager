<?php
/**
 * Dailyscript - app | web | media
 *
 *
 *
 * @category    Administracion
 * @package     Models
 * @author      Iván D. Meléndez
 * @copyright   Copyright (c) 2010 Dailyscript Team (http://www.dailyscript.com.co)
 * @version     1.0
 */

class Usuario extends ActiveRecord {

    public $logger = true;

    const ACTIVO = 1;
    const INACTIVO = 0;

    public function initialize() {
        $this->belongs_to('grupo');
    }

    /**
     * Metodo para inciar sesion.
     *
     * @return boolean
     */
    public function entrar() {
        //Verifico si ha enviado los datos através del formulario
        if(Input::hasPost('login') && Input::hasPost('pass')) {
            //Verifico que el formulario recibido sea igual al que se envió
            if(SecurityKey::isValid()) {
                $this->usr = Input::post('login');
                $this->pwd = md5(Input::post('pass'));//Encripto nuevamente la contraseña, pues ya viene encriptada con el sha1
                //Utilizo Auth
                $auth = new Auth('model', 'class: usuario', "login: $this->usr", "password: $this->pwd", 'estado: 1');
                $auth->sleep_on_fail(true,2);//En caso de que falle duermo la aplicacion por 2 segundos

                if( $auth->authenticate() ) {
                    $this->codigo = Auth::get('id');
                    $this->ip     = Utils::getIp(); //Determino la ip del visitante
                    $this->valido = true;
                    //Obtengo el grupo del usuario
                    $grupo = $this->getGrupo();
                    if(!$grupo) {
                        Flash::error("Se ha producido un error en la verificación de los datos.");
                        Auth::destroy_identity();
                    } else {
                        //Almaceno en sesion algunos parámetros
                        Session::set('id', $this->codigo);
                        Session::set("usuario", $this->usr);
                        Session::set("ip", $this->ip);

                        Session::set('nivel',$grupo->id);
                        Session::set('grupo',$grupo->descripcion);

                        Flash::info("¡ Bienvenido <strong>$this->usr</strong> !.");
                        Router::redirect('dc-admin/');
                    }
                } else {
                    Flash::error('El usuario y/o contraseña incorrectos.');
                }
             } else {
                 Flash::error('La llave de acceso ha expirado. <br />Por favor intente nuevamente.');
             }
         }
    }

    /**
     * Metodo para cerrar la sesion del usuario actual
     */
    public function salir() {
        if(!Auth::is_valid()) {
            Flash::info("Identifícate nuevamente.");
        } else {
            Auth::destroy_identity();
            Session::delete('id');
            Session::delete('ip');
            Session::delete("usuario");
            Session::delete('grupo');
            Session::delete('nivel');
            Flash::valid("La sesión se ha cerrado correctamente.");
        }
        //Cambio la vista
        View::setPath('dc-admin/usuario');
        View::select('entrar','login');
    }

    /**
     * Retorna el usuario que ha iniciado sesion
     */
    public function getUsuarioLogueado() {
        //Verifico que haya iniciado sesión para no generar una excepción
        if(Auth::is_valid()) {
            return $this->find_first('id = '. Auth::get('id') );
        } else {
            return false;
        }
    }

    /**
     * Retorna el usuario según la búsqueda
     *
     * @param int $codigo Código del usuario
     * @param string $login Alias del usuario
     * @return array
     */
    public function buscarUsuario($codigo, $login) {
        //Filtro los datos
        $codigo = Filter::get($codigo, 'numeric');
        $login = Filter::get($login, 'string');
        //Armo la consulta
        $condicion = 'registrado_at != \'\'';
        $condicion.= ($codigo) ? " AND id = '$codigo'" : '';
        $condicion.= ($login) ? " AND login = '$login'" : '';

        return $this->find_first('conditions: '.$condicion);
    }


    public function listarUsuarios($estado) {

        $estado = Filter::get($estado, 'int');
        $columnas = 'usuario.id, usuario.login, usuario.mail, usuario.grupo_id, usuario.nombre, usuario.apellido, usuario.estado, grupo.grupo_descripcion';
        $join = 'INNER JOIN grupo ON grupo.id = usuario.grupo_id';
        $condicion = ($estado) ? "usuario.estado = '$estado'" : '';
        return $this->find('columns: '.$columnas, 'join: '.$join, 'conditions: '.$condicion);

    }

    public function registrarUsuario(){
        // Determino el usuario logueado
        $usuario = Load::model('usuario')->getUsuarioLogueado();

        if ($usuario->grupo_id == Grupo::COLABORADOR || $usuario->grupo_id == Grupo::LECTOR) {
            Flash::error('Este usuario no puede crear ning&uacute;n tipo de usuario. ');
        } elseif ( $usuario->grupo_id <= $this->grupo_id ) {
            $this->password = md5($this->password);
            $result = $this->save();
            if ( $result ) {
                Flash::valid('Usuario creado con &eacute;xito. ');
            } else {
                Flash::error('Error al crear usuario. ');
            }
        } else {
            Flash::error('No puede crear un usuario con mayor privilegios que el suyo. ');
        }
    }

    public function buscarEmail($email) {
        $email = Filter::get($email, 'string');
        $condicion = "mail LIKE '" . $email . "'";
        return $this->exists($condicion);
    }

    public function buscarLogin($login) {
        $login = Filter::get($login, 'string');
        $condicion = "login LIKE '" . $login . "'";
        return $this->exists($condicion);
    }

    public function getGrupo() {
        $grupo = Load::model('grupo');
        return $grupo->find_first('id = ' . Auth::get('grupo_id'));
    }


    // Redes Sociales

    ///// Twitter //////
    function getTwitter($id) {
        $r = $this->find($id);
 
        $o = array(
            'id' => $r->id,
            'token' => $r->user_token,
            'secret' => $r->user_secret,
        );
        
        if ($r->user_token == '') {
            return FALSE;
        } else {
            return $o;
        }
 
    }

    public function setTwitter($id, $token = '', $secret = '') {
        $r = $this->find($id);
        $r->user_token = $token;
        $r->user_secret = $secret;
 
        if ( $r->save() ) {
            return True;
        } else {
            return False;
        }
    }
    ///// Twitter //////
}

?>
