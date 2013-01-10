<?php
/**
 * Dailyscript - app | web | media
 *
 *
 *
 * @category    Blog
 * @package     Controllers
 * @author      Iván D. Meléndez
 * @copyright   Copyright (c) 2010 Dailyscript Team (http://www.dailyscript.com.co)
 * @version     1.0
 */

Load::model('post');
//Cargo la librería para el manejo de fechas
Load::lib('ext_date');
//Incluyo la libreria de paginación
Load::lib('paginacion/Paginated');

class BlogController extends ApplicationController {

    public function before_filter() {
        if(Input::isAjax()) {
            View::template(null);
        }
    }

    /**
     * Método general para listar o ver las publicaciones de esta manera:
     * Analizo los parametros recibidos<br>
     * dominio.com/blog/pag/2/<br>
     * dominio.com/blog/2010/pag/2/<br>
     * dominio.com/blog/2010/01/pag/2/<br>
     * dominio.com/blog/2010/01/01/pag/2/<br>
     * dominio.com/blog/2010/01/01/mi-post/<br>
     * dominio.com/blog/2010/01/01/mi-post/preview/
     *
     * @param int|string $param1 Puede recibir 'pag' o el año de publicacion
     * @param int|string $param2 Puede recibir 'pag' o el mes de publicación
     * @param int|string $param3 Puede recibir 'pag' o del día de publicacion del (los) post
     * @param int|string $param4 Puede recibir 'pag' o la Url del post
     * @param int|string $param5 Puede recibir valores de la página u opciones extas, como preview para ver la vista previa
     */
    public function ver($param1=null,$param2=null,$param3=null,$param4=null, $param5=null) {                
        //Titulo de la página
        $this->title = 'Noticias';        
        //Determino si se muesta un solo post o un listado
        $this->unique_post = false;
        //Analizo las variables
        $year   = ($param1 != 'pag') ? $param1 : null;
        $month  = ($param2 != 'pag') ? $param2 : null;
        $day    = ($param3 != 'pag') ? $param3 : null;
        $slug   = ($param4 != 'pag') ? $param4 : null;
        $pag = 'pag';
        $num = 1;
        if($param1 == 'pag') {
            $num = $param2;
        } else if($param2 == 'pag') {
            $num = $param3;
        } else if($param3 == 'pag') {
            $num = $param4;
        } else if($param4 == 'pag') {
            $num = $param5;
        }        
        //Si contiene el slug del post, indico que es único para ser utilizado en la vista
        if($slug) {            
            $this->unique_post = true;
            View::select('ver_post');
        } else {            
            //Numero de la pagina
            $this->numero   = ( Filter::get($num,'numeric') > 0 ) ? Filter::get($num,'numeric') : 1;
            //Contador del datagrid que depende del numero de la página
            $this->contador = ( ($pag === 'pag') && ($this->numero > 1) ) ? ( ($this->numero * POST_POR_PAGINA) - (POST_POR_PAGINA-1) ) : 1;
            //Selecciono la vista para el listado
            View::select('ver_listado');
        }
                
        $post = new Post();

        //Determino si se encuentran algunos parámetros
        if($year or $month or $day or $slug) {
            //Determino si está en la vista previa
            if($param5 == 'preview') {
                View::template('borrador');
            }
            $result = $post->verPost(null, $year, $month, $day, $slug);
            if($this->unique_post) {
                //Si es único asigno el título a la página
                $this->title = $result->titulo;
            } else {                
                //Creo el título dependiendo de los filtros posibles
                $this->title = $year;
                $this->title.= ($month) ? ' '.ExtDate::getMonthName($month) : '';
                $this->title.= ($day) ? ' '.$day : '';
            }
        } else { //Si no contiene alguno de esos parámetros
            $result = $post->filtrarPost(Post::PUBLICADO, Post::PUBLICO, '', '' ,'desc');
        }
        if(!$result) {
            $this->title = 'No se encontró la página';
            $this->detalle_error = 'Publicación no encontrada';
            View::notFound();            
        }
        if(!$this->unique_post) {
            //Creo un paginador con el resultado, que muestre 15 filas y empieze por el numero de la página
            $result = new Paginated($result,POST_POR_PAGINA,$this->numero);
        }
        $this->post = $result;                
    }

    /**
     * Método para listar las publicaciones según su clasificación: categoria o etiqueta
     *
     * @param string $tipo Tipo de clasificación a mostrar
     * @param string $slug Url de la clasificación
     * @param string $pag Palabra 'pag' en la url
     * @param int $num Número de página a mostrar
     */
    public function taxonomia($tipo, $slug, $pag='pag', $num=1) {
        $post = null;
        if($tipo === 'categoria' or $tipo === 'etiqueta') {
            //Determino la clasificación a mostrar
            $clasificacion = ($tipo === 'categoria') ? Taxonomia::CATEGORIA : Taxonomia::ETIQUETA;
            $slug = Filter::get($slug,'stripslashes', 'striptags','string');

            $taxonomia = new Taxonomia();
            $taxonomia = $taxonomia->getInformacionTaxonomia($clasificacion, null, null, $slug);
            
            //Verifico que exista la taxonomia
            if($taxonomia) {
                //Agrego el título a la página
                $this->title = $taxonomia->nombre;
                //Numero de la pagina
                $this->numero   = ( Filter::get($num,'numeric') > 0 ) ? Filter::get($num,'numeric') : 1;
                //Contador del datagrid que depende del numero de la página
                $this->contador = ( ($pag === 'pag') && ($this->numero > 1) ) ? ( ($this->numero * POST_POR_PAGINA) - (POST_POR_PAGINA-1) ) : 1;
                $post = new Post();
                $post = $post->filtrarPost(Post::PUBLICADO, Post::PUBLICO, $tipo, $slug, 'desc');
            }
        }
        if($post) {
            $this->post = new Paginated($post,POST_POR_PAGINA,$this->numero);
            View::select('ver_listado');
        } else {
            $this->title = 'No se encontró la página';
            $this->detalle_error = 'Clasificación no encontrada';
            View::notFound();
        }
        
    }

    /**
     * Método para listar las publicaciones según su autor
     *
     * @param string $login Alias del autor a buscar
     * @param string $pag Palabra 'pag' en la url
     * @param int $num Número de página a mostrar
     */
    public function autor($login, $pag='pag', $num=1) {
        $post = null;
        $login = Filter::get($login,'stripslashes', 'striptags','string');
        //Busco el usuario
        $usuario = new Usuario();
        $usuario = $usuario->buscarUsuario(null, $login);        
        if($usuario) {
            //Agrego el título a la página
            $this->title = $usuario->login;
            //Numero de la pagina
            $this->numero   = ( Filter::get($num,'numeric') > 0 ) ? Filter::get($num,'numeric') : 1;
            //Contador del datagrid que depende del numero de la página
            $this->contador = ( ($pag === 'pag') && ($this->numero > 1) ) ? ( ($this->numero * POST_POR_PAGINA) - (POST_POR_PAGINA-1) ) : 1;
            $post = new Post();
            $post = $post->filtrarPost(Post::PUBLICADO, Post::PUBLICO, 'autor', $login, 'desc');
        }                
        if($post) {
            $this->post = new Paginated($post,POST_POR_PAGINA,$this->numero);
            View::select('ver_listado');
        } else {
            $this->title = 'No se encontró la página';
            $this->detalle_error = 'Usuario no encontrado';
            View::notFound();
        }        
    }

    public function buscar($param='', $pag='pag', $num=1) {
        $this->title = 'Búsqueda';
        $post = null;
        if(Input::hasPost('parametro')) {
            //Aplico un filtro para reemplazar los espacios por un +
            $param = Filter::get(Input::post('parametro'),'limpiar_espacio');
            //Redirecciono a la misma acción
            Router::toAction('buscar/'.$param.'/');
        } else {
            $this->title = 'Búsqueda';
            if($param){
                $post = new Post();
                $post = (strlen($param) > 2) ? $post->buscarPost($param) : null;
                //Aplico un filtro para reemplazar los + por espacios
                $this->parametro = Filter::get($param,'agregar_espacio');
                //Numero de la pagina
                $this->numero   = ( Filter::get($num,'numeric') > 0 ) ? Filter::get($num,'numeric') : 1;
                //Contador del datagrid que depende del numero de la página
                $this->contador = ( ($pag === 'pag') && ($this->numero > 1) ) ? ( ($this->numero * POST_POR_PAGINA) - (POST_POR_PAGINA-1)) : 1;                
            }
            if($post) {
                //Creo un paginador con el resultado, que muestre 15 filas y empieze por el numero de la página
                $this->post = new Paginated($post,POST_POR_PAGINA,$this->numero);
                View::select('ver_listado');
            } else {
                $this->detalle_error = isset($this->parametro) ? 'Resultado para "'.$this->parametro.'"' : 'Búsqueda de publicaciones';
                View::select('buscar');
            }
        }                
    }
    
}
?>
