<?php
/**
 * Dailyscript - app | web | media
 *
 *
 * @category    Administracion
 * @package     Models
 * @author      Jaro Marval
 * @copyright   Copyright (c) 2013 Icterus Team (http://www.icter.us)
 * @version     1.0
 */
Load::lib('upload');

class subir{

    protected $_path;
    protected $_saved;

    public function __construct() {
        $this->_saved = null;
        $this->_path = dirname($_SERVER['SCRIPT_FILENAME']);
    }

    public function subirFavicon(){
        $this->subir = Upload::factory('favicon', 'image');
        $this->subir->setExtensions(array('ico', 'png', 'gif')); // Extensiones permitidas
        $this->subir->setPath($this->_path . '/img'); // Directorio destino de CMS
        $this->subir->overwrite(True);
        $this->subir->setMaxWidth(16);
        $this->subir->setMaxHeight(16);
        return $this->guardar('icon');
    }

    public function subirImagen(){
        $this->subir = Upload::factory('imagen', 'image');
        $this->subir->setExtensions(array('jpg', 'jpeg', 'png', 'gif')); // Extensiones permitidas
        $ruta = '/img/dcm/imagenes';
        $this->subir->setPath($this->_path.$ruta); // Directorio destino de CMS
        $salida = $this->guardar();
        $this->setSaved($ruta.'/'.$salida);
        return $salida;
    }

    public function subirArchivo(){
        $this->subir = Upload::factory('archivo');
        $ruta = '/files/dcm';
        $this->subir->setPath($this->_path.$ruta); // Directorio destino de CMS
        $salida = $this->guardar();
        $this->setSaved($ruta.'/'.$salida);
        return $salida;
    }

    /**
     * Guardar el archivo subido
     * Tamaño minimo: 250 bytes(no se puden subir archivos vacios)
     * Tamaño maximo: 3 Megabytes
     */
    private function guardar($nombre=null){
        $this->subir->setMinSize("250"); // Minimo permitido 250bytes
        $this->subir->setMaxSize("3M");

        // Verificamos si la imagen subio
        if(!$this->subir->isUploaded()) {
            return FALSE;
        } else {
            // Guarda el archivo
            $name = $this->subir->save($nombre);
            if($name) {
                return $name;
            }
        }
    }


    public function setSaved($path) {
        $this->_saved = PUBLIC_PATH.$path;
    }

    public function getSaved() {
        return $this->_saved;
    }

}