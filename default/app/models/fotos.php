<?php

Load::lib('upload');

class Fotos extends ActiveRecord {

    // FIXME: Limpiar y reparar el código
    public function subirFoto() {
        $subir = Upload::factory('fotos', 'image');
        $subir->setExtensions(array('jpg', 'jpeg', 'png', 'gif')); // Extensiones permitidas

        $this->begin();
        $ext = '.'.pathinfo($_FILES['fotos']['name'], PATHINFO_EXTENSION);
        $this->nombre_ruta = Utils::slug($_FILES['fotos']['name']);
        $nombre_ruta = str_replace($ext, '', $this->nombre_ruta);
        $this->usuario_id = Auth::get('id');
        $path = dirname($_SERVER['SCRIPT_FILENAME']).Album::PATH;
        $ruta =  $path.Load::model('album')->getRuta($this->album_id);
        $imagen = $ruta.$nombre_ruta;
        $subir->setPath($ruta); // Directorio destino de CMS


        if ( $this->exists("nombre_ruta = '$this->nombre_ruta'") || file_exists($imagen) ) {
            $this->rollback();
            Flash::error('Ya existe una foto con el mismo nombre en esta álbum');
            return False;
        } elseif ( $subir->save($nombre_ruta) && $this->create() ) {
            $this->commit();
            return True;
        } else {
            if ( file_exists($imagen) ) {
                unlink($imagen);
            }
            $this->rollback();
            return False;
        }
    }

    public function listarFotos($id, $page, $per_page) {
        $page = Filter::get($page, 'int');
        $per_page = Filter::get($per_page, 'int');

        $sql = "SELECT `fotos`.`id`, `fotos`.`nombre`, `fotos`.`descripcion`, `nombre_ruta`,
        `login`, `fotos`.`fecha_creacion_at`, `fotos`.`hora_creacion_at`, `ruta`
        FROM `fotos`
        LEFT JOIN `album` ON `album`.`id` = `fotos`.`album_id`
        INNER JOIN `usuario` ON `album`.`usuario_id` = `usuario`.`id`
        WHERE `album`.`id` = " . Filter::get($id, 'int');
        return $this->paginate_by_sql($sql, "page: $page", "per_page: $per_page");
    }

    public function eliminarFoto($id){
        $this->begin();
        $foto = $this->find_first($id);
        $ruta =  dirname($_SERVER['SCRIPT_FILENAME']).Album::PATH.Load::model('album')->getRuta($this->album_id);
        $imagen = $ruta.$foto->nombre_ruta;
        if ( $foto->delete() ) {
            if ( $this->count("album_id=$this->album_id") == 0 ) Load::model('album')->cambiarEstado($this->album_id, 'INACTIVO');
            if ( unlink($imagen) ) {
                $this->commit();
                return True;
            }
        }
        $this->rollback();
        return False;
    }

}

?>