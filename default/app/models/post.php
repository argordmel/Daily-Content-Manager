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

Load::models('grupo','taxonomia','post_taxonomia','usuario');

class Post extends ActiveRecord {

    public $logger = true;

    const BORRADOR = 1;
    const PENDIENTE = 2;
    const PUBLICADO = 3;
    const ELIMINADO = 4;
    const PUBLICO = 1;
    const PRIVADO = 2;

    public function initialize() {
        $this->belongs_to('usuario');
        $this->has_many('pot_taxonomia');
        $this->has_and_belongs_to_many('taxonomia');
    }

    /**
     * Metodo para registrar publicaciones
     *
     * @return array
     */
    public function registrarPost() {

        //Determino el usuario logueado
        $usuario = Load::model('usuario')->getUsuarioLogueado();
        //Si el usuario pertenece al grupo de colaboradores no permite publicar, <br>
        //si no se encuentra en borrador lo deja en estado pendiente.
        if ($usuario->grupo_id == Grupo::COLABORADOR) {
            if($this->estado != self::BORRADOR) {
                $this->estado = self::PENDIENTE;
            }
        }
        //Determino el creador del post
        if(!isset($this->usuario_id)) {
            $this->usuario_id = $usuario->id;
        }

        //Verifico si se ha enviado a traves de un quickpress
        $quickpress = ( isset($this->quickpress) && ($this->quickpress=='quickpress') ) ? true : false;
        //Si es quickpress cargo algunas configuraciones por defecto
        if($quickpress) {
            $this->visibilidad = self::PUBLICO;
            $this->habilitar_comentarios = HABILITAR_COMENTARIOS;
            $this->contenido = "<p style=\"text-align: justify\">".nl2br($this->contenido)."</p>";
            $this->fecha_publicacion = date("Y-m-d H:i:s");
        } else {
            //Si no es quickpress le agrego la hora a la fecha de publicación
            $this->fecha_publicacion = $this->fecha_publicacion.' '.date("H:i:s");
        }

	$this->hora_publicacion = date("H:i:s");

        $rs = $this->save();

        if($rs) {
            //Si es quickpress imprimo el script para limpiar el formulario
            if($quickpress) {
                echo '<script type="text/javascript">document.getElementById(\'formulario\').reset();limpiar_err();</script>';
            }
            if($this->estado == self::PENDIENTE) {
                Flash::highlight('El borrador se ha almacenado correctamente y en espera de ser revisado. '.Html::link($this->getUrlPost('blog'),'Ver borrador.',array('target'=>'_blank')));
            } else if($this->estado == self::BORRADOR){
                Flash::valid('El borrador se ha registrado correctamente. '.Html::link($this->getUrlPost('blog'),'Ver artículo.',array('target'=>'_blank')));
            } else {
                Flash::valid('La publicación se ha registrado correctamente. '.Html::link($this->getUrlPost('blog'),'Ver artículo.',array('target'=>'_blank')));
            }

            return $this->id;
        }

        return $rs;

    }

    /**
     * Metodo para saber si ya se encuentra registrado un slug
     *
     * @return int
     */
    public function getSlugRegistrado() {
        $condicion = "slug = '$this->slug'";
        $condicion.= isset($this->id) ? " AND id != '$this->id'" : '';
        return $this->count('columns: slug',"conditions: $condicion");
    }

    /**
     * Metodo que retorna el listado de post segun el filtro desado
     *
     * @param int|string $estado Estado de los post a filtrar
     * @param int|string $visibilidad Visibilidad de los post a filtrar
     * @param string $parametro Pametro de los post a filtrar
     * @param string $valor Valor del parametro a filtrar
     * @param string $orden Orden a mostrar los post
     * @param int $limite Limite del listado
     * @return array
     */
    public function filtrarPost($estado, $visibilidad, $parametro='', $valor='', $orden = 'asc', $limite = '', $mensaje = false) {

        $estado = $this->_getEstadoPost($estado);

        //Filtro algunas variables
        $parametro = Filter::get($parametro,'stripslashes', 'striptags', 'string');
        $valor = Filter::get($valor,'stripslashes', 'striptags', 'string');
        //Si no se recibe el valor retorna false
        if($parametro && !$valor) {
            return false;
        }

        //Armo la consulta
        $sql = 'SELECT post.*,usuario.login,usuario.grupo_id,COUNT(comentario.post_id) AS comentarios ';
        $sql.= 'FROM post ';
        $sql.= 'INNER JOIN usuario ON usuario.id = post.usuario_id ';
        $sql.= 'INNER JOIN post_taxonomia ON post.id = post_taxonomia.post_id ';
        $sql.= 'INNER JOIN taxonomia ON post_taxonomia.taxonomia_id = taxonomia.id  ';
        $sql.= 'LEFT JOIN comentario ON comentario.post_id = post.id ';
        $sql.= 'WHERE ';
        $sql.= ($estado && $estado != 'todos') ? "post.estado = '$estado'" : "post.estado != '".self::ELIMINADO."'";
        $sql.= (strtolower($visibilidad) != 'todos') ? ' AND post.visibilidad = \''.Filter::get($visibilidad,'numeric').'\'' : '';

        //Determino si se ha enviado parametros con su respectivo valor
        $sql.= ($parametro == 'autor')      ?   " AND usuario.login = '$valor'"                             : " AND usuario.estado = '1'";
        $sql.= ($parametro == 'categoria')  ?   " AND taxonomia.tipo = '1' AND taxonomia.url = '$valor'"    : '';
        $sql.= ($parametro == 'etiqueta')   ?   " AND taxonomia.tipo = '2' AND taxonomia.url = '$valor'"    : '';

        //Si está en el módulo de administración
        if(Router::get('module') == 'dc-admin')  {
            //Obtengo el usuario logueado
            $usuario = Load::model('usuario')->getUsuarioLogueado();
            if($usuario) {
                //Si el usuario no pertenece al grupo administrador o editor, solo se muestran los del usuario logueado.
                if ($usuario->grupo_id != Grupo::ADMINISTRADOR && ($usuario->grupo_id != Grupo::EDITOR)) {
                    $sql.= " AND usuario.id = $usuario->id";
                }
            } else {
                return false;
            }
        }

        //Determino el orden
        $orden = strtoupper($orden);
        if($orden != 'ASC' && $orden != 'DESC') {
            if($mensaje) {
                Flash::error('Error: PST-FTR001. Se ha producido un error en la verificación de la información. <br />Al parecer no se logró establecer el orden del listado.');
            }
            return false;
        }

        $sql.= ' GROUP BY post.id';
        $sql.= ' ORDER BY post.fecha_publicacion '.$orden;

        $sql.= ($limite) ? ' LIMIT '.Filter::get($limite,'numeric') : '';

        return $this->find_all_by_sql($sql);
    }

    /**
     * Método que retorna el numero de post segun el estado y/o el autor
     *
     * @param int $visibilidad Visibilidad de los post
     * @param int $estado Estado de los post.
     * @param int $usuario Codigo del usuario que ha realiazo los post
     * @return int
     */
    public function getContadorPost($visibilidad='', $estado='',$autor='') {
        $estado = ($estado) ? Filter::get($estado,'int') : '';
        $autor = ($autor) ? Filter::get($autor,'int') : '';

        $condicion = ($estado) ? "estado = '$estado'" : "estado != ".self::ELIMINADO;
        $condicion.= ($visibilidad != 'todos') ? " AND visibilidad = '".Filter::get($visibilidad, 'int')."'" : "";
        $condicion.= ($autor) ? " AND usuario_id = '$autor'" : '';

        return $this->count("conditions: $condicion");
    }

    /**
     * Método para obtener la url del post
     *
     * @param string $path Ruta del blog
     * @param string $fecha Fecha de la publicacion
     * @param string $slug Url amigable del post
     * @param string $estado Estado de los post
     * @return string
     */
    public function getUrlPost($path='', $fecha='',$slug='', $estado='') {
        $fecha = ($fecha) ? $fecha : $this->fecha_publicacion;
        $slug = ($slug) ? $slug : $this->slug;
        $estado = ($estado) ? $estado : $this->estado;
        $preview = ( ($estado == self::BORRADOR) or ($estado == self::PENDIENTE) ) ? 'preview/' : '';
        $url = ExtDate::getYear($this->fecha_publicacion) .'/'. ExtDate::getMonth($this->fecha_publicacion) .'/'. ExtDate::getDay($this->fecha_publicacion) .'/'. $slug .'/'. $preview;
        $url = ($path) ? trim($path,'/') .'/'. $url : $url;
        return $url;
    }

    /**
     * Método que retorna el post o un listado de post dependiendo del año de publicacion,
     * el mes, el día y el slug del mimo
     *
     * @param int $year Año de la publicacion
     * @param int $month Mes de la publicacion
     * @param int $day Dia de la publicacion
     * @param string $slug Url amigable de la publicacion
     * @return string
     */
    public function verPost($codigo=null, $year=null, $month=null, $day=null, $slug=null) {

        $codigo = Filter::get($codigo,'int');
        $year = Filter::get($year,'numeric');
        $month = Filter::get($month,'numeric');
        $day = Filter::get($day,'numeric');
        $slug = Filter::get($slug, 'stripslashes', 'striptags','string');

        //Armo la consulta
        $sql = 'SELECT post.*,usuario.login,usuario.grupo_id,COUNT(comentario.post_id) AS comentarios ';
        // $sql = 'SELECT post.*,usuario.login,usuario.grupo_id AS comentarios ';
        $sql.= 'FROM post ';
        $sql.= 'INNER JOIN usuario ON usuario.id = post.usuario_id ';
        $sql.= 'LEFT JOIN comentario ON comentario.post_id = post.id ';
        $sql.= "WHERE post.estado != '".self::ELIMINADO."'";
        print 'sql ->'.$sql;
        if($codigo) { //Si tiene el código del post
            $sql.=" AND post.id = '$codigo'";
            $rs = $this->find_by_sql($sql);
        } else if($slug) { //Si tiene la url del post
            $sql.=" AND post.slug = '$slug'";
            $rs = $this->find_by_sql($sql);
        } else { //Si tiene el año, mes y/o día
            if($year && !$month && !$day) {
                $sql.=" AND post.fecha_publicacion BETWEEN '$year-01-01 00:00:01' AND '$year-12-31 23:59:59'";
            } else if($year && $month && !$day) {
                //Verificar el último día del mes
                $sql.=" AND post.fecha_publicacion BETWEEN '$year-$month-01 00:00:01' AND '$year-$mont-28 23:59:59'";
            } else if($year && $month && $day) {
                $sql.=" AND post.fecha_publicacion BETWEEN '$year-$month-$day 00:00:01' AND '$year-$month-$day 23:59:59'";
            } else {
                return false;
            }

            $sql.= ' GROUP BY post.id';
            $sql.= ' ORDER BY post.fecha_publicacion DESC';
            $rs = $this->find_all_by_sql($sql);
        }

        if(!$rs) {
            Flash::info('Lo sentimos, pero no podemos encontrar lo que estás buscando. Quizás la búsqueda te ayudará.');
        }
        return $rs;
    }

    /**
     * Método que retorna el post o un listado de post dependiendo del año de publicacion,
     * el mes, el día y el slug del mimo
     *
     * @param int $year Año de la publicacion
     * @param int $month Mes de la publicacion
     * @param int $day Dia de la publicacion
     * @param string $slug Url amigable de la publicacion
     * @return string
     */
    public function listarPost($page, $per_page,$year=null, $month=null, $day=null, $slug=null) {

        $year = Filter::get($year,'numeric');
        $month = Filter::get($month,'numeric');
        $day = Filter::get($day,'numeric');
        $slug = Filter::get($slug, 'stripslashes', 'striptags','string');

        //Armo la consulta
        $sql = 'SELECT post.*,usuario.login,usuario.grupo_id,COUNT(comentario.post_id) AS comentarios ';
        // $sql = 'SELECT post.*,usuario.login,usuario.grupo_id AS comentarios ';
        $sql.= 'FROM post ';
        $sql.= 'INNER JOIN usuario ON usuario.id = post.usuario_id ';
        $sql.= 'LEFT JOIN comentario ON comentario.post_id = post.id ';
        $sql.= "WHERE post.estado != '".self::ELIMINADO."'";

        if($slug) { //Si tiene la url del post
            $sql.=" AND post.slug = '$slug'";
            $rs = $this->find_by_sql($sql);
        } else { //Si tiene el año, mes y/o día
            if($year && !$month && !$day) {
                $sql.=" AND post.fecha_publicacion LIKE '$year-%'";
            } else if($year && $month && !$day) {
                //Verificar el último día del mes
                $month = ($month<10) ? '0'.$month:$month;
                $sql.=" AND post.fecha_publicacion LIKE '$year-$month-%'";
            } else if($year && $month && $day) {
                $sql.=" AND post.fecha_publicacion = '$year-$month-$day'";
            // } else {
            //     return false;
            }

            $sql.= ' GROUP BY post.id';
            $sql.= ' ORDER BY post.fecha_publicacion DESC';
            // $rs = $this->find_all_by_sql($sql);
            $rs = $this->paginate_by_sql($sql, "page: $page", "per_page: $per_page");
        }

        if(!$rs) {
            Flash::info('Lo sentimos, pero no podemos encontrar lo que estás buscando. Quizás la búsqueda te ayudará.');
        }
        return $rs;
    }


    public function test($page=1){

        //Armo la consulta
        // $sql = 'SELECT post.*,usuario.login,usuario.grupo_id,COUNT(comentario.post_id) AS comentarios ';
        $sql = 'SELECT post.*,usuario.login,usuario.grupo_id AS comentarios ';
        $sql.= 'FROM post ';
        $sql.= 'INNER JOIN usuario ON usuario.id = post.usuario_id ';
        $sql.= 'LEFT JOIN comentario ON comentario.post_id = post.id ';
        $sql.= "WHERE post.estado != '".self::ELIMINADO."'";
        return $this->paginate_by_sql($sql, "page: $page", "per_page: 1");
        // return $this->paginate("page: $page", "per_page: 1", 'order: fecha_publicacion desc');
    }

    /**
     * Método para buscar un post por algún parametro que coincida
     * con el titulo o con con algún texto dentro del post
     *
     * @param string $param Palabra o frase a buscar
     * @param int $estado Permite buscar solo en los estados de los post
     * @return array
     */
    public function buscarPost($param,$estado=POST::PUBLICADO,$visibilidad=POST::PUBLICO) {
        //Aplico un filtro al parametro
        $param = Filter::get($param, 'stripslashes', 'striptags');
        if(strlen($param) > 2) {
            //Aplico un filtro para el estado
            $estado = Filter::get($estado, 'int');
            //Armo la consulta
            $sql = 'SELECT post.*,usuario.login,usuario.grupo_id,usuario.nombre, usuario.apellido,COUNT(comentario.post_id) AS comentarios ';
            $sql.= 'FROM post ';
            $sql.= 'INNER JOIN usuario ON usuario.id = post.usuario_id ';
            $sql.= 'LEFT JOIN comentario ON comentario.post_id = post.id ';
            $sql.= 'WHERE ';
            $sql.= ($estado) ? "post.estado = '$estado'" : "post.estado != '".self::ELIMINADO."'";
            $sql.= " AND post.titulo like '%".$param."%' OR post.contenido like '%".$param."%'";
            $sql.= ' GROUP BY post.id';
            $sql.= ' ORDER BY post.fecha_publicacion DESC';
            return $this->find_all_by_sql($sql);

        } else {
            Flash::info('Ingresa algún parámetro para inicializar la búsqueda.');
            return false;
        }
    }


    /**
     * Método para listar las clasificaciones de los post.
     *
     * @param int $tipo Tipo de clasificación
     * @param int $post Código de la publicación
     * @return array
     */
    public function getTaxonomiaPost($tipo='',$post='') {
        if($tipo) {
            //Determino el tipo de la taxonomia
            if(is_numeric($tipo)) {
                $tipo = ($tipo == 1) ? 1 : 2;
            } else {
                $tipo = ( ($tipo == 'categorias') or ($tipo == 'categoria') ) ? 1 : 2;
            }
        }
        //Determino el codigo del post
        $post = ($post) ? Filter::get($post,'int') : $this->id;

        $columnas = 'post_taxonomia.post_id, post_taxonomia.taxonomia_id, taxonomia.id, taxonomia.tipo, taxonomia.nombre, taxonomia.url';
        $join = 'INNER JOIN post_taxonomia ON post.id = post_taxonomia.post_id INNER JOIN taxonomia ON post_taxonomia.taxonomia_id = taxonomia.id ';
        $condicion = "post.id = '$post'";
        $condicion.= ($tipo) ? " AND taxonomia.tipo = '$tipo'" : '';

        return $this->find("columns: $columnas", "join: $join", "conditions: $condicion");

    }

    /**
     * Metodo para eliminar un post
     *
     * @param int $id Codigo del post a eliminiar
     * @param boolean $mensaje Indica si se muestra el mensaje de confirmación de la eliminación
     * @return boolean
     */
    public function eliminarPost($id, $mensaje=false) {
        $id = Filter::get($id,'numeric');
        $rs = $this->find_first($id);
        if($rs) {
            $this->estado = self::ELIMINADO;
            $delete = $this->update();
            if($mensaje && $delete) {
                Flash::valid('El post se ha eliminado correctamente.');
            }
        } else {
            Flash::error('Error: PST-DEL001. Se ha producido un error en la verificación de la información. <br />Al parecer no se logró encontrar algún registro relacionado.');
        }
        return $rs;
    }

    /**
     * Metodo para obtener el estado y la visibilidad de los post
     *
     * @param string $tipo. Espeficica el estado del post.<br> Ejemplo: publicado o POST::PUBLICADO o 1
     * @return int
     */
    protected function _getEstadoPost($estado) {
        if(!is_numeric($estado)) {
            $estado = strtolower($estado);
            if( ($estado == 'borrador') or ($estado == 'borradores') ) {
               $estado = self::BORRADOR;
            } else if( ($estado == 'pendiente') or ($estado == 'pendientes') ) {
               $estado = self::PENDIENTE;
            } else if( ($estado == 'publicado') or ($estado == 'publicados') ) {
               $estado = self::PUBLICADO;
            } else if( ($estado == 'eliminado') or ($estado == 'eliminados')) {
               $estado = self::ELIMINADO;
            } else if($estado == 'todos') {
                   $estado = null;
            } else {
                return null;
            }
        } else {
            $estado = ($estado) ? Filter::get($estado,'int') : null;
            if($estado != self::BORRADOR && $estado != self::PENDIENTE && $estado != self::PUBLICADO && $estado != self::ELIMINADO) {
                return null;
            }
        }
        return $estado;
    }

    /**
     * Metodo para modificar publicaciones
     *
     * @return array
     */
    public function modificarPost($mensaje=false) {

        //Determino el usuario logueado
        $usuario = Load::model('usuario')->getUsuarioLogueado();
        //Si el usuario pertenece al grupo de colaboradores no permite publicar, <br>
        //si no se encuentra en borrador lo deja en estado pendiente.
        if ($usuario->grupo_id == Grupo::COLABORADOR) {
            if($this->estado != self::BORRADOR) {
                $this->estado = self::PENDIENTE;
            }
        }

        $rs = $this->update();
        if($rs && $mensaje) {
            Flash::valid('La publicación se ha actualizado correctamente.');
        }
        return $rs;
    }

    /**
     * Callback que se ejecuta antes de insertar un nuevo registro
     */
    public function before_create() {

    }

    /**
     * Callback que se ejecuta antes de guardar un registro
     */
    public function before_save() {

        //Verifico que la fecha de publicación no sea mayor a la de hoy
        if($this->fecha_publicacion > date("Y-m-d")) {
            $this->fecha_publicacion = date("Y-m-d H:i:s");
        }
        //Verifico que no exista otro titulo registrado
        if($this->getSlugRegistrado()) {
            Flash::error('El título de la publicación ya se encuentra almacenado.');
            return 'cancel';
        }
        //Compongo el slug del post
        Load::lib('utils');
        $this->slug = isset($this->slug) ? $this->slug : Utils::slug($this->titulo);
        //Realizo el resumen del post
        if (preg_match('/<!-- pagebreak(.*?)?-->/', $this->contenido, $matches)) {
            $matches = explode($matches[0], $this->contenido, 2);
            $this->resumen = Utils::balanceTags($matches[0]) . '<a href="' . PUBLIC_PATH . $this->getUrlPost('blog') . '" title="Sigue Leyendo">Sigue leyendo...</a>';
        } else {
            $this->resumen = $this->contenido;
        }
    }

    public function after_save() {
        //Cargo el modelo para agregar las taxonomias al post
        Load::models('post_taxonomia');
        $taxonomia = new PostTaxonomia();
        //Determino si se han enviado categorias
        $categorias = (Input::post('categorias')) ? (Input::post('categorias')) : CATEGORIA_POR_DEFECTO;
        //Elimino las taxonomias registradas para no crear conflicto
        $taxonomia->eliminarPostTaxonomia($this->id);
        //Registro las categorias
        $taxonomia->registrarPostTaxonomia(Taxonomia::CATEGORIA, $categorias, $this->id, false);
        //Determino si se han enviado etiquetas
        $etiquetas = Input::post('etiquetas');
        if($etiquetas) {
            //Registro las etiquetas
            $taxonomia->registrarPostTaxonomia(Taxonomia::ETIQUETA, $etiquetas, $this->id, false);
        }
    }

    /**
     * Callback que se ejecuta antes de eliminar un registro
     */
    public function before_delete() {
        //Determino el usuario logueado y que el post sea de él o que tenga los permisos suficientes
        $usuario = Load::model('usuario')->getUserLogged();
        if ($usuario->grupo_id == Grupo::COLABORADOR or $usuario->grupo_id == Grupo::AUTOR) {
            if($this->usuario_id != $usuario->id) {
                Flash::error('Lo sentimos, pero no posees los permisios suficientes para realizar esta acción.');
                return 'cancel';
            }
        }
    }
}
?>
