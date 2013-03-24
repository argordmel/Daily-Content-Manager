<?php

class Album extends ActiveRecord {
    protected $logger = True;
    const PATH = '/img/dcm/galeria/';
    const INACTIVO = 0;
    const ACTIVO = 1;

    public function crearAlbum() {
        // exit();
        $this->begin();
        $path = dirname($_SERVER['SCRIPT_FILENAME']).self::PATH;

        $this->estado = self::$this->estado;
        $path .= $this->ruta = Utils::slug($this->nombre).'/';
        $this->usuario_id = Auth::get('id');

        if ( $this->exists("nombre = '$this->nombre'") || file_exists($path) ) {
            $this->rollback();
            Flash::error('Ya existe un álbum con ese nombre');
            return False;
        } elseif ( Util::mkpath($path) && $this->create() ) {
            $this->commit();
            return True;
        } else {
            if ( file_exists($path) ) {
                rmdir($path);
            }
            $this->rollback();
            Flash::error('Error al intentar crear álbum!!!');
            return False;
        }
    }

    public function cambiarEstado($id, $estado) {
        $cantidad = Load::model('fotos')->count("album_id = $id");
        if ( $cantidad == 0 && $estado == 'ACTIVO' ) {
            Flash::error('No se puede activar un album vacío');
        } else {
            $album = $this->find($id);
            $album->estado = constant('self::'.$estado);
            return $album->update();
        }
    }

     public function listarAlbum($page, $per_page, $estado='ACTIVO') {
        $page = Filter::get($page, 'int');
        $per_page = Filter::get($per_page, 'int');
        $conditions = ($estado=='todos')?'':"WHERE `album`.`estado` = ". constant('self::'.$estado);

        $sql = "SELECT `album`.`id`, `album`.`nombre`, `album`.`ruta`, `album`.`estado`,
        `usuario`.`login`, `album`.`fecha_creacion_at`, `album`.`hora_creacion_at`,
        count(`fotos`.`id`) AS cantidad, `fotos`.`nombre_ruta`
        FROM `album`
        LEFT JOIN `fotos` ON `album`.`id` = `fotos`.`album_id`
        INNER JOIN `usuario` ON `album`.`usuario_id` = `usuario`.`id`
        $conditions
        GROUP BY `album`.`id`";
        return $this->paginate_by_sql($sql, "page: $page", "per_page: $per_page");
    }

    public function eliminarAlbum($id){
        $this->begin();
        $album = $this->find_first($id);
        $ruta = dirname($_SERVER['SCRIPT_FILENAME']).self::PATH.$album->ruta;
        if ( $album->delete() ) {
            if ( rmdir($ruta) ) {
                $this->commit();
                return True;
            }
        }
        $this->rollback();
        return False;
    }

    public function getRuta($id) {
        $album = $this->find_first($id);
        return $album->ruta;
    }

}

?>