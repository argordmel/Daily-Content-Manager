<?php

Load::model('album');
// Load::model('fotos');

class GaleriaController extends AppController {

    public function index() {
        Router::toAction('crear_album/');
    }

    public function crear_album() {
        if ( Input::hasPost('galeria') ) {
            $album = new Album( Input::post('galeria') );
            if ( $album->crearAlbum() ) {
                Flash::valid('Album creado con éxito!!!');
            }
        }
    }

    public function administrar() {
        View::select('listar');

    }

    public function subirFotos() {

    }
}

?>