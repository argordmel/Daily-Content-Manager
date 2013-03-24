<?php

Load::model('album');
Load::model('fotos');

class GaleriaController extends AppController{
    protected $per_page = 20;

	public function index($pag='pag', $num=1) {
        $albumnes = new Album();
        $this->album = $albumnes->listarAlbum($num, $this->per_page);
	}

}

?>