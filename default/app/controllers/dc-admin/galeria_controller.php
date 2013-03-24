<?php

Load::model('album');
Load::model('fotos');

class GaleriaController extends AppController {
    protected $per_page = 20;

    public function index() {
        Router::toAction('crear_album/');
    }

    public function crear_album() {
        if ( Input::hasPost('galeria') ) {
            $album = new Album( Input::post('galeria') );
            if ( $album->crearAlbum() ) {
                Flash::valid('Álbum creado con éxito!!!');
            }
        }
    }

    public function administrar($pag='pag', $num=1) {
        View::select('listar');
        $albumnes = new Album();
        $this->album = $albumnes->listarAlbum($num, $this->per_page, 'todos');
    }

    public function album($id=null,$key='key',$valueKey='',$pag='pag',$num=1) {
        if ( Input::hasPost('galeria') ) {
            $post = Input::post('galeria');
            $post['album_id'] = $id;
            $fotos = new Fotos($post);
            if ( $fotos->subirFoto() ) {
                Flash::valid('Imagen subida de forma exitosa al álbum');
            }
        }
        $fotos = new Fotos();
        $this->fotos = $fotos->listarFotos($id, $num, $this->per_page);
    }

    public function modificar_album($id=null,$estado=null,$key='key',$valueKey=''){
        if ( $valueKey == md5($id.$this->ipKey.$this->expKey.'galeria') ) {
            $album = new Album();
            if ( $album->cambiarEstado($id, $estado) ) {
                Flash::valid('Álbum modificado exitosamente!!!');
            } else {
                Flash::error('Álbum no ha podido ser modificado');
            }
        } else {
            Flash::error('Llave inválida');
        }
        Router::redirect(Utils::getBack());
    }

    public function eliminar_album($id=null,$key='key',$valueKey='') {
        if ( $valueKey == md5($id.$this->ipKey.$this->expKey.'galeria') ) {
            $album = new Album();
            if ( $album->eliminarAlbum($id) ) {
                Flash::valid('Álbum eliminada exitosamente!!!');
            } else {
                Flash::error('Error al eliminar álbum<br/>Un álbum no puede ser eliminado si posee fotos en el');
            }
        } else {
            Flash::error('Llave inválida');
        }
        Router::redirect(Utils::getBack());
    }

    public function eliminar_foto($id=null,$key='key',$valueKey='') {
        if ( $valueKey == md5($id.$this->ipKey.$this->expKey.'galeria') ) {
            $fotos = new Fotos();
            if ( $fotos->eliminarFoto($id) ) {
                Flash::valid('Foto eliminada exitosamente!!!');
            } else {
                Flash::error('Foto no ha podido ser eliminado');
            }
        } else {
            Flash::error('Llave inválida');
        }
        Router::redirect(Utils::getBack());
    }
}

?>