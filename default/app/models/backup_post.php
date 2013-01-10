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

Load::models('grupo');

class BackupPost extends ActiveRecord {

    public $logger = true;        

    const BORRADOR = 1;
    const PENDIENTE = 2;
    const PUBLICADO = 3;
    const ELIMINADO = 4;
    const POST_PUBLICO = 1;
    const POST_PRIVADO = 2;
    
    
    public function initialize() {
        $this->belongs_to('usuario');
        //$this->belongs_to('categoria');
        //$this->has_many('comentario');
        //$this->has_many('post_etiqueta');
    }

    /**
     * Metodo para registrar publicaciones
     *
     * @return array
     */
    public function registrarPost() {

        //Determino el usuario logueado
        $usuario = Load::model('usuario')->getUserLogged();
        //Si el usuario pertenece al grupo 4 no permite publicar, si no se encuentra en borrador lo deja en estado pendiente.
        if ($usuario->grupo_id == Grupo::COLABORADOR) {
            if($this->estado != self::BORRADOR) {
                $this->estado = self::PENDIENTE;
            }
        }
        $this->usuario_id = $usuario->id;

        //Verifico si se ha enviado a traves de un quickpress y lo enmarco en un parrafo
        $quickpress = ( isset($this->quickpress) && ($this->quickpress=='quickpress') ) ? true : false;
        
        if($quickpress) {
            $this->visibilidad = self::POST_PUBLICO;
            $this->contenido = "<p style=\"text-align: justify\">".nl2br($this->contenido)."</p>";
        }

        $this->fecha_publicacion = date("Y-m-d H:i:s");        
        $this->habilitar_comentarios = 'SI';

        $rs = $this->save();

        if(!$rs) {            
            Flash::error('Se ha producido un error en el registro del articulo. Por favor intente nuevamente');
        } else {
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
        }

        return $rs;
                
    }

    /**
     * Método que retorna los post almacenados segun su estado, autor, orden y cantidad a mostrar
     *
     * @param int $estado Estado de los post a listar
     * @param string $autor Nombre del usuario que realizo los post
     * @param string $orden Orden en que se mostrarán los post: ASC o DESC
     * @param int $limite Cantitdad máxima a mostrar por página
     * @return array
     */
    public function listarPost($estado='', $autor='', $orden = 'asc', $limite=0) {
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
               Flash::error('PST-LTR001. Se ha producido un error en la verificación de la información. <br />Al parecer no se logró establcer el filtro el listado.');
               return false;
           }
        } else {
            $estado = ($estado) ? Filter::get($estado,'int') : null;
        }
        $autor = ($autor) ? Filter::get($autor,'alnum') : '';
        $orden = strtoupper($orden);
        $limite = ($limite) ? 'limit: '.Filter::get($limite,'numeric'): '';
        if($orden != 'ASC' && $orden != 'DESC') {            
            Flash::error('Error: PST-LTR002. Se ha producido un error en la verificación de la información. <br />Al parecer no se logró establecer el orden del listado.');
            return false;
        }

        $columnas = 'post.*, usuario.login, usuario.grupo_id, usuario.nombre, usuario.apellido, usuario.estado';
        $join = 'INNER JOIN usuario ON usuario.id = post.usuario_id';
        $condicion = ($estado) ? "post.estado = '$estado'" : "post.estado = '".self::BORRADOR."' OR post.estado = '".self::PUBLICADO."' or post.estado = '".self::PRIVADO."'";
        $condicion.= ($autor) ? " AND usuario.login = '$autor'" : '';
        return $this->find("columns: $columnas", "join: $join", "conditions: $condicion", "order: fecha_publicacion $orden", "$limite");
    }

    /**
     * Metodo que retorna el listado de post segun el filto desado
     *
     * @param int $estado Estado de los post a filtrar
     * @param string $parametro Pametro de los post a filtrar
     * @param string $valor Valor del parametro a filtrar
     * @param string $orden Orden a mostrar los post
     * @param int $limite Limite del listado
     * @return array
     */
    public function filtrarPost($estado='', $parametro='', $valor='', $orden = 'asc', $limite = '') {
        
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
               Flash::error('PST-RTR001. Se ha producido un error en la verificación de la información. <br />Al parecer no se logró establcer el filtro el listado.');
               return false;
           }
        } else {
            $estado = ($estado) ? Filter::get($estado,'int') : null;
        }

        $columnas = 'post.*, usuario.login, usuario.grupo_id, usuario.nombre, usuario.apellido';
        $join = 'INNER JOIN usuario ON usuario.id = post.usuario_id';
        $condicion = ($estado) ? "post.estado = '$estado'" : "post.estado != '".self::ELIMINADO."'";

        //Determino si se han enviado parametros
        if($parametro == 'autor') {
            $condicion.= ($valor) ? " AND usuario.login = '$valor'" : '';
        } else {
            $condicion.= " AND usuario.estado = '1'";
        }

        //Determino el orden
        $orden = strtoupper($orden);
        if($orden != 'ASC' && $orden != 'DESC') {            
            Flash::error('Error: PST-FTR002. Se ha producido un error en la verificación de la información. <br />Al parecer no se logró establecer el orden del listado.');
            return false;
        }
        //Determino el limite si existe
        $limite = ($limite) ? 'limit: '.Filter::get($limite,'numeric'): '';
        
        return $this->find("columns: $columnas", "join: $join", "conditions: $condicion", "order: fecha_publicacion $orden", $limite);
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

        $condicion = ($visibilidad != 'todos') ? "visibilidad = '".Filter::get($visibilidad, 'int')."' AND " : "";
        $condicion.= ($estado) ? "estado = '$estado'" : "estado != ".self::ELIMINADO;
        $condicion.= ($autor) ? " AND usuario_id = '$autor'" : '';        

        return $this->count("conditions: $condicion");
    }

    /**
     * Metodo para saber si ya se encuentra registrado un slug
     * 
     * @return int
     */
    public function getSlugRegistrado() {
        return $this->count('columns: slug',"conditions: slug = '$this->slug'");
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
            $this->usuario_id = $rs->usuario_id; //Determino el creador del post
            $delete = $this->delete($rs->id);
            if($mensaje && $delete) {                
                Flash::valid('El post se ha eliminado correctamente.');
            }
        } else {
            Flash::error('Error: PST-DEL001. Se ha producido un error en la verificación de la información. <br />Al parecer no se logró encontrar algún registro relacionado.');
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
    public function verPost($year, $month, $day, $slug=null) {

        $year = Filter::get($year,'numeric');
        $month = Filter::get($month,'numeric');
        $day = Filter::get($day,'numeric');
        $slug = Filter::get($slug, 'stripslashes', 'striptags');
        
        $columnas = 'post.*, usuario.login, usuario.grupo_id, usuario.nombre, usuario.apellido';
        $join = 'INNER JOIN usuario ON usuario.id = post.usuario_id';       
        $condicion = '';

        if($slug) {
            $condicion.="post.fecha_publicacion >= '".date("Y-m-d", strtotime("$year-$month-$day"))."' AND post.slug = '$slug'";
            $rs = $this->find_first("columns: $columnas", "join: $join", "conditions: $condicion");
        } else {
            if($year) {
                $condicion.=" post.fecha_publicacion >= '$year'";
                if($month) {
                    $condicion.=" AND post.fecha_publicacion >= '$year-$month-01'";
                    if($day) {
                        $condicion.=" AND post.fecha_publicacion = '$year-$month-$day'";
                    }
                }
                $rs = $this->find("columns: $columnas", "join: $join", "conditions: $condicion", 'order: post.fecha_publicacion DESC');
            } else {
                $rs = null;
            }
        }

        if(!$rs) {
            Flash::info('Lo sentimos, pero no podemos encontrar lo que estás buscando. Quizás la búsqueda te ayudará.');
        }
        return $rs;
    }

    /**
     * Método para buscar un post por algún parametro que coincida
     * con el titulo o con con algún texto dentro del post
     *
     * @param string $what Palabra o frase a buscar
     * @param string $estado Permite buscar solo en los estados de los post
     * @return array
     */
    public function buscarPost($what,$estado=self::PUBLICADO) {
        $what = Filter::get($what, 'stripslashes', 'striptags');
        if(strlen($what) > 2) {
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
                   Flash::error('PST-BSC001. Se ha producido un error en la verificación de la información. <br />Al parecer no se logró establcer el filtro el listado.');
                   return false;
               }               
            } else {
                $estado = ($estado) ? Filter::get($estado,'int') : null;
            }
            
            $columnas = 'post.*, usuario.login, usuario.grupo_id, usuario.nombre, usuario.apellido';
            $join = 'INNER JOIN usuario ON usuario.id = post.usuario_id';
            $condicion = ($estado) ? "post.estado = '$estado'" : "post.estado != '".self::ELIMINADO."'";
            $condicion.= " AND post.titulo like '%".$what."%' OR post.contenido like '%".$what."%'";
            
            return $this->find("columns: $columnas", "join: $join", "conditions: $condicion", "order: fecha_publicacion DESC");

        } else {
            Flash::info('Ingresa algún parámetro para inicializar la búsqueda.');
            return false;
        }                
    }

    /**
     * Callback que se ejecuta antes de insertar un nuevo registro
     */
    public function before_create() {
        //Verifico que no exista otro titulo registrado
        $cuenta = $this->getSlugRegistrado();
        if($cuenta > 0) {
            Flash::error('El título de la publicación ya se encuentra almacenado.');
            return 'cancel';
        }
    }

    /**
     * Callback que se ejecuta antes de guardar un registro
     */
    public function before_save() {
        //Compongo el slug del post
        Load::lib('utils');
        $this->slug = Utils::slug($this->titulo);
        if (preg_match('/<!-- pagebreak(.*?)?-->/', $this->contenido, $matches)) {
            $matches = explode($matches[0], $this->contenido, 2);
            $this->resumen = Utils::balanceTags($matches[0]) . '<a href="' . PUBLIC_PATH . date("Y") . '/' .date("m")  . '/' .date("d") .'/'. $this->slug . '/" title="Sigue Leyendo">Sigue leyendo...</a>';
        } else {
            $this->resumen = $this->contenido;
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
