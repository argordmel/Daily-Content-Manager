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

class Taxonomia extends ActiveRecord {

    const CATEGORIA = 1;
    const ETIQUETA = 2;
    public $logger = true;
    
    public function initialize() {
        $this->has_many('post_taxonomia');
        $this->has_and_belongs_to_many('post');
    }

    /**
     * Metodo para registrar la clasificacion: categoria o etiqueta
     * 
     * @param string $tipo Tipo de clasificación a registrar: categoria o etiqueta
     * @param boolean $mensaje Indica si se muestra el mensaje de confirmación del registro
     * @return array
     */
    public function registrarTaxonomia($tipo, $mensaje = false) {
        $this->tipo = $this->_getTipoTaxonomia($tipo);
        $this->mensaje = $mensaje;
        $rs = $this->save();
        if($this->mensaje && $rs) {
            $taxonomia = ($this->tipo == self::CATEGORIA) ? 'categoría' : 'etiqueta';
            Flash::valid('La '.$taxonomia.' se ha registrado correctamente.');
        }
        return $rs;        
    }

    /**
     * Metodo para buscar una la clasificacion: categoria o etiqueta
     *
     * @param string $tipo Tipo de clasificación a buscar: categoria o etiqueta
     * @param int $id Codigo de la clasificación a buscar.
     * @param string $nombre Nombre de la clasificación a buscar.
     * @return array
     */
    public function getInformacionTaxonomia($tipo, $id='', $nombre='', $slug='') {
        $condicion = "tipo = ".$this->_getTipoTaxonomia($tipo);
        $condicion.= ($id) ? " AND id = '".Filter::get($id,'int')."'" : '';
        $condicion.= ($nombre) ? " AND nombre = '".Filter::get($nombre,'string')."'" : '';
        $condicion.= ($slug) ? " AND url = '".Filter::get($slug,'string')."'" : '';
        return $this->find_first("conditions: $condicion");
    }

    /**
     * Método para buscar clasificaciones por su nombre
     *
     * @param string $tipo Tipo de clasificación a buscar: categoria o etiqueta
     * @param string $nombre
     * @return array
     */
    public function buscarTaxonomia($tipo,$nombre) {        
        $condicion = "tipo = ".$this->_getTipoTaxonomia($tipo);
        $condicion.= " AND nombre like '%".Filter::get($nombre,'string')."%'";
        return $this->find("conditions: $condicion", "orden: nombre ASC");
    }

    /**
     * Método que retorna las clasificaciones almacenadas segun su tipo con el total
     * de post registrados por cada clasificación
     *
     * @param string $tipo Tipo de clasificación a mostrar: categoria o etiqueta
     * @param string $orden Orden en que se mostrarán: ASC o DESC     
     * @return array
     */
    public function listarTaxonomia($tipo, $orden = 'asc', $utilizadas=false) {
        $tipo = $this->_getTipoTaxonomia($tipo);
        $orden = strtoupper($orden);
        if($orden != 'ASC' && $orden != 'DESC') {
            Flash::error('Error: TAX-LTR001. Se ha producido un error en la verificación de la información. <br />Al parecer no se logró establcer el orden del listado.');
            return false;
        }
        $join = ($utilizadas) ? 'INNER' : 'LEFT';
        $sql = 'SELECT taxonomia.*, COUNT(post_taxonomia.taxonomia_id) AS total_post ';
        $sql.= 'FROM taxonomia ';        
        $sql.= $join.' JOIN post_taxonomia ON taxonomia.id = post_taxonomia.taxonomia_id '.$join.' JOIN post ON post.id = post_taxonomia.post_id ';
        $sql.= "WHERE taxonomia.tipo = '$tipo' ";
        $sql.= ($utilizadas) ? ' AND post.estado = '.Post::PUBLICADO.' AND post.visibilidad = '.Post::PUBLICO.' ' : '';
        $sql.= 'GROUP BY taxonomia.id ';
        $sql.= "ORDER BY taxonomia.registrado_at $orden";
        
        return $this->find_all_by_sql($sql);
    }

    /**
     * Metodo para eliminar una clasificación
     * 
     * @param int $id Codigo de la clasificacion a eliminiar
     * @param boolean $mensaje Indica si se muestra el mensaje de confirmación de la eliminación
     * @return boolean
     */
    public function eliminarTaxonomia($id, $mensaje=false) {
        $id = Filter::get($id,'int');
        $rs = $this->find_first($id);
        if($rs) {
            if($rs->id == 1) {
                Flash::error('Error: TAX-DEL001. Lo sentimos, pero esta categoría no puede ser eliminada.');
                return 'cancel';
            } else {
                $delete = $this->delete($rs->id);
                if($mensaje && $delete) {
                    $taxonomia = ($rs->tipo == self::CATEGORIA) ? 'categoría' : 'etiqueta';
                    Flash::valid('La '.$taxonomia.' se ha eliminado correctamente.');
                }
            }
        } else {
            Flash::error('Error: TAX-DEL002. Se ha producido un error en la verificación de la información. <br />Al parecer no se logró encontrar algún registro relacionado.');
        }
        return $rs;
    }

    /**
     * Metoddo para modificar una clasificacion
     * 
     * @param boolean $mensaje Indica si se muestra el mensaje de confirmación de la modificacion
     * @return array
     */
    public function modificarTaxonomia($mensaje=false) {        
        $this->mensaje = $mensaje;
        $rs = $this->update();
        if($this->mensaje && $rs) {
            $taxonomia = ($this->tipo == self::CATEGORIA) ? 'categoría' : 'etiqueta';
            Flash::valid('La '.$taxonomia.' se ha actualizado correctamente.');
        }        
        return $rs;
    }

    /**
     * Metodo para contar las clasificaciones registradas segun su tipo
     */
    public function getContadorTaxonomia($tipo) {
        $condicion = "tipo = ".$this->_getTipoTaxonomia($tipo);
        return $this->count("conditions: $condicion");
    }

    /**
     * Método para verificar la existencia de una url
     * @return int
     */
    public function getUrlRegistrada($tipo='', $url='') {
        $url = ($url) ? $url : $this->url;
        $tipo = ($tipo) ? $this->_getTipoTaxonomia($tipo) : $this->tipo;
        $condicion = "url = '$url' AND tipo = '$tipo'";
        $condicion.= isset($this->id) ? " AND id != '$this->id'" : '';        
        return $this->find_first('columns: url',"conditions: $condicion");
    }

    /**
     * Metodo para obtener el tipo de taxonomia
     *
     * @param string $tipo. Espeficica el tipo de taxonomia.<br> Ejemplo: categoria o Taxonomia::CATEGORIA o 1
     * @return int
     */
    protected function _getTipoTaxonomia($tipo) {
        if(!is_numeric($tipo)) {
           $tipo = strtolower($tipo);
           $tipo = ( ($tipo == 'categoria') or ($tipo == 'categorias') ) ? self::CATEGORIA : self::ETIQUETA;
        } else {
            $tipo = ($tipo) ? Filter::get($tipo,'int') : null;
            if($tipo != self::CATEGORIA && $tipo != self::ETIQUETA) {
                return null;
            }
        }        
        return $tipo;
    }

    /**
     * Callback que se ejecuta antes de modificar un registro
     */
    public function before_update() {
        if($this->id == 1 && $this->tipo == 1) {
            Flash::error('Error: TAX-UPD001. Lo sentimos, pero esta categoría no puede ser editada.');
            return 'cancel';
        }
    }
        
    /**
     * Callback que se ejecuta antes de guardar o modificar un registro
     */
    public function before_save() {        
        Load::lib('utils');
        $this->url = ($this->url) ? Utils::slug($this->url) : Utils::slug($this->nombre);        
        if($this->getUrlRegistrada()) {
            if($this->mensaje) {
                $taxonomia = ($this->tipo == self::CATEGORIA) ? 'categoría' : 'etiqueta';
                Flash::error('La '.$taxonomia.' con el nombre dado ya se encuentra registrada.');
            }
            return 'cancel';
        }        
    }

    /**
     * Callback que se ejecuta antes de eliminar un registro
     */
    public function before_delete() {
        //Utilizo las relaciones de kumbiaphp :)
        $post = $this->getPostTaxonomia();
        if($post) {
            $taxonomia = ($this->tipo == self::CATEGORIA) ? 'categoría' : 'etiqueta';
            Flash::error('Lo sentimos, pero esta '.$taxonomia.' se encuentra relacionada con alguna publicación y no puede ser eliminada.');
            return 'cancel';
        }        
    }
       
}

?>
