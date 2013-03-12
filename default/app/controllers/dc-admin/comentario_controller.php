<?php
/**
 * Dailyscript - app | web | media
 *
 *
 *
 * @category    Administracion
 * @package     Controllers
 * @author      Jaro Marval
 * @copyright   Copyright (c) 2010 Icterus Team (http://www.icter.us)
 * @version     1.0
 */

Load::model('post');
Load::model('comentario');
Load::lib('paginacion/Paginated');

class ComentarioController extends AppController {

	public function index() {
		Router::toAction('listar/');
	}

    public function procesar($id=null,$estado=null,$key='key',$valueKey=''){
        if ($valueKey === md5($id.$this->ipKey.$this->expKey.'comentario')) {
            $estado = strtoupper($estado);
            $rs = Load::model('comentario')->procesarComentario($id,constant('Comentario::'.$estado));
            if ($rs) {
                Flash::valid('Comentario procesado de forma éxitosa!!!');
            }
        } else {
            Flash::error('Acceso incorrecto al sistema.');
        }
        Router::redirect(Utils::getBack());
    }

	public function listar($comentarios=null, $parametro=null, $valor=null, $pag='pag',$num='') {
		$comentario = new Comentario();

		//Determino la visibilidad y el estado de los post a listar
        $comentarios = ( ($comentarios == 'pag') or ($comentarios == null) ) ? 'todos' : $comentarios;

        //Determino si el estado corresponde al paginador. Ejemplo: post/listar/pag/2/
        if($comentarios && $comentarios == 'pag') {
            $pag = $comentarios;
            $num = $parametro;
        } else if($parametro && $parametro == 'pag') {//Determino si el parametro es el paginador. Ejemplo: post/listar/borradores/pag/2/
            $pag = $parametro;
            $num = $valor;
        } else if($valor && $valor == 'pag') {//Determino si el valor es el paginador. Ejemplo: post/listar/categoria/pag/2/
            Flash::error('Acceso denegado al sistema');
            Router::redirect('dc-admin/');
        }

        //Determino el parametro a filtrar
        $parametro = ($parametro == 'pag')  ? null : $parametro;

        //Numero de la pagina
        $this->numero   = ( Filter::get($num,'numeric') > 0 ) ? Filter::get($num,'numeric') : 1;

		//Contador del datagrid que depende del numero de la página
        $this->contador = ( ($pag === 'pag') && ($this->numero > 1) ) ? ( ($this->numero * 15) - 14 ) : 1;

		$comentario = $comentario->filtrarComentarios($comentarios);
        //Creo un paginador con el resultado, que muestre 15 filas y empieze por el numero de la página
        $this->comentario = new Paginated($comentario,15,$this->numero);

        // Variable para prevenir que se manipule el contador através de la url
        $this->registros = count($comentario);
        $this->contador = ( $this->registros >= $this->contador ) ? $this->contador : 1;
	}

}