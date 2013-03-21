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

Load::models('post');
Load::lib('paginacion/Paginated');

class PostController extends AppController {

    /**
     * Callback que se ejecuta antes de cualquier método
     */
    public function before_filter() {
        if(Input::isAjax()) {
            View::template(null);
        }
    }

    /**
     * Método principal para listar las publicaciones y la búsqueda de las mismas
     */
    public function index() {
        //Titulo de la página
        $this->title = 'Publicaciones ';
        //Verifico si ha enviado algún parámetro para inicializar la búsqueda
        if(Input::hasPost('parametro')) {
            $param = Input::post('parametro');
            //Aplico un filtro para reemplazar los espacios por un +
            $param = Filter::get($param,'limpiar_espacio');
            //Redirecciono a la action buscar
            Router::toAction('buscar/'.$param.'/');
        } else {
            Router::toAction('listar/');
        }
    }

    /**
     * Método para buscar publicaciones.
     *
     * @param string $param Cadena de texto a buscar
     * @param string $pag Palabra 'pag' que viene en la url
     * @param int $num Numero de la página del listado
     */
    public function buscar($param='',$pag='pag',$num='') {
        //Titulo de la página
        $this->title = 'Búsqueda de publicaciones';
        if($param){

            $post = new Post();
            $resultado = (strlen($param) > 2) ? $post->buscarPost($param) : null;
            //Aplico un filtro para reemplazar los + por espacios
            $this->parametro = Filter::get($param,'agregar_espacio');
            //Variable por si se desea filtrar en la vista, filtre 'todos'
            $this->actual = 'todos';
            //Numero de la pagina
            $this->numero   = ( Filter::get($num,'numeric') > 0 ) ? Filter::get($num,'numeric') : 1;
            //Contador del datagrid que depende del numero de la página
            $this->contador = ( ($pag === 'pag') && ($this->numero > 1) ) ? ( ($this->numero * 15) - 14 ) : 1;
            //Creo un paginador con el resultado, que muestre 15 filas y empieze por el numero de la página
            $this->post = new Paginated($resultado,15,$this->numero);
            //Variable para prevenir que se manipule el contador através de la url
            $this->registros = count($resultado);
            $this->contador = ( $this->registros >= $this->contador ) ? $this->contador : 1;
            //Cambio la vista
            View::select('listar');
        } else {
            Flash::warning('Ingresa algún parámetro para inicializar la búsqueda.');
            Router::toAction('listar/');
        }

    }

    /**
     * Método para agregar un nuevo post
     */
    public function agregar() {
        //Titulo de la página
        $this->title = 'Nueva publicación';
        //Ckeck de los radios para habilitar comentarios
        $this->check_si = (HABILITAR_COMENTARIOS) ? true : false;
        $this->check_no = (HABILITAR_COMENTARIOS) ? false : true;
        //Array para determinar la visibilidad de los post
        $this->visibilidad = array(Post::PUBLICO=>'Público', Post::PRIVADO=>'Privado');
        //Defino el listado de categorias
        $this->categorias = Load::model('taxonomia')->listarTaxonomia('categoria');
        //Listo los usuarios
        Load::models('usuario');
        $usuario = new Usuario();
        //$this->usuarios = $usuario->listarUsuarios(Usuario::ACTIVO);
        $this->usuarios = array('usuario' , 'listarUsuarios' , Usuario::ACTIVO);
        $this->amend = (Session::get('nivel') >= Grupo::AUTOR) ? 'disabled' : '';

        //Verifico si ha enviado los datos a través del formulario
        if(Input::hasPost('post')) {
            //Verifico que el formulario coincida con la llave almacenada en sesion
            if(SecurityKey::isValid()) {
                $post = new Post(Input::post('post'));
                $quickpress = isset($post->quickpress) ? true : false;
                $resultado = $post->registrarPost();
                if($resultado) {
                    //Consulto las taxonomias
                    $this->categoria = $post->getTaxonomiaPost(Taxonomia::CATEGORIA, $resultado);
                    $this->etiquetas = $post->getTaxonomiaPost(Taxonomia::ETIQUETA, $resultado);
                    $post->id = $resultado;
                    View::select('editar');
                } else {
                    //Hago persitente los datos
                    $this->categoria = Input::post('categorias');
                    $this->etiquetas = Input::post('etiquetas');
                }
                $this->post = $post;
                //Si se ha enviado atavés del quickpress
                if($quickpress) {
                    View::select('agregar_ajax');
                }
            } else {
                Flash::info('La llave de acceso ha caducado. Por favor intente nuevamente.');
            }
        }
    }

    /**
     * Método general para listar los post. <br>Ejemplos:
     * post/listar/publicados/<br>
     * post/listar/publicados/pag/2/<br>
     * post/listar/publicados/categoria/mi-categoria/<br>
     * post/listar/publicados/categoria/mi-categoria/pag/2/<br>
     *
     * @param string $estado Estado de los post a listar
     * @param string $parametro Parámetro a buscar. Ejem: etiqueta, categoria
     * @param string $valor Valor del parametro a buscar. Ejem: mi-categoria
     * @param string $pag Palabra 'pag' en la url
     * @param int $num Número de la pagina actual
     */
    public function listar($estado=null, $parametro=null, $valor=null, $pag='pag',$num='') {
        //Titulo de la página
        $this->title = 'Publicaciones';
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

    /**
     * Método para eliminar publicaciones.
     *
     * @param int $id Código de la publicación a eliminar
     * @param string $key Palabra 'key' que viene en la url
     * @param string $valueKey  Llave de seguridad para prevenir que se edite directamente desde la url
     */
    public function eliminar($id,$key='key',$valueKey='') {
        if($valueKey === md5($id.$this->ipKey.$this->expKey.'post')) {
            $post = new Post();
            $post->eliminarPost($id,true);
        } else {
            Flash::error('Acceso denegado al sistema.');
        }
        Router::redirect('dc-admin/post/listar/');
    }

    /**
     * Método para editar publicaciones
     *
     * @param int $id Código de la publicación
     * @param string $key Palabra 'key' que viene en la url
     * @param string $valueKey  Llave de seguridad para prevenir que se edite directamente desde la url
     */
    public function editar($id=null,$key='key',$valueKey='') {
        //Titulo de la página
        $this->title = 'Editar publicación';

        $this->categorias = Load::model('taxonomia')->listarTaxonomia('categoria');
        $this->visibilidad = array(Post::PUBLICO=>'Público', Post::PRIVADO=>'Privado');

        //Verifico si se ha enviado el formulario
        if(Input::hasPost('post')) {
            //Verifico que el formulario coincida con la llave almacenada en sesion
            if(SecurityKey::isValid()) {
                $post = new Post(Input::post('post'));
                $id = $post->id;
                $post->modificarPost(true);
                $result = $post->verPost($id);
                Router::redirect('dc-admin/post/editar/'.$result->id.'/key/'.md5($result->id.$this->ipKey.$this->expKey.'post').'/');
            } else {
                Flash::info('La llave de acceso ha caducado. Por favor intente nuevamente');
                Router::toAction('listar/');
            }
        } else {
            //Armo la llave con el código de la url
            if($valueKey === md5($id.$this->ipKey.$this->expKey.'post')) {
                $post = new Post();
                $result = $post->verPost($id);
            } else {
                Flash::error('Acceso incorrecto al sistema.');
                Router::redirect('dc-admin/post/listar/');
            }
        }

        //Ckeck de los radios para habilitar comentarios
        $this->check_si = ($result->habilitar_comentarios == 'SI') ? true : false;
        $this->check_no = ($result->habilitar_comentarios == 'SI') ? false : true;

        $this->post = $result;
        $this->post_categorias = $post->getTaxonomiaPost('categorias', $result->id);
        $this->post_etiquetas = $post->getTaxonomiaPost('etiquetas', $result->id);

    }

}
?>
