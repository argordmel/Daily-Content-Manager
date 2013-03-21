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

Load::lib('paginacion/Paginated');

class BibliotecaController extends AppController {
    public function index() {

    }

    public function subir() {
        if ( Input::hasPost('rsa32_key') ) {
            $subir = Load::model('subir');
            if ( $subir->subirArchivo() ) {
                Flash::valid('Operación Exitosa');
            } else {
                Flash::error('Error al intentar subir imagen!!!');
            }
        }
    }

    public function upload() {
        View::template('solo');

        // Listar Archivos
        $this->path = 'files/dcm/';
        $this->system_path=dirname($_SERVER['SCRIPT_FILENAME']).'/'.$this->path;
        $this->directorio = dir($this->system_path);
        $this->archivos = array();
        while ($archivo = $this->directorio->read()) {
            if ( $archivo != '.' && $archivo != '..' && $archivo != 'index.html' ) {
                $this->archivos[]=$archivo;
            }
        }
        $this->directorio->close();


        if ( Input::hasPost('rsa32_key') ) {
            $subir = Load::model('subir');
            if ( $subir->subirArchivo() ) {
                print "<script>
                    window.opener.e('<img src=\"".$subir->getSaved()."\" />');
                    window.close();
                    </script>";
            } else {
                Flash::error('Error al intentar subir imagen!!!');
            }
        }
    }


    public function listar(){

    }
}
?>