<?php

Load::lib('upload');

class Album extends ActiveRecord {
    const PATH = '/img/dcm/galeria/';
    const INACTIVO = 0;
    const ACTIVO = 1;

    public function crearAlbum() {
        // exit();
        $this->begin();
        $path = dirname($_SERVER['SCRIPT_FILENAME']).self::PATH;

        $this->estado = self::$this->estado;
        $path .= $this->ruta = Util::underscore(strtolower($this->nombre));
        $this->usuario_id = Auth::get('id');

        if ( $this->exists("nombre = '$this->nombre'") ) {
            $this->rollback();
            Flash::error('Ya existe una galería con ese nombre');
            return False;
        } elseif ( Util::mkpath($path) && $this->create() ) {
            $this->commit();
            return True;
        } else {
            if ( file_exists($path) ) {
                rmdir($path);
            }
            $this->rollback();
            Flash::error('Error al intentar subir imagen!!!');
            return False;
        }
    }

    public function listarAlbum() {
        return $this->find();
    }

    public function verAlbum($id) {

    }

}

?>