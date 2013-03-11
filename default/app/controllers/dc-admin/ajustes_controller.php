<?php
/**
 * Dailyscript - app | web | media
 *
 *
 *
 * @category    Administracion
 * @package     Controllers
 * @author      Iván D. Meléndez
 * @copyright   Copyright (c) 2010 Dailyscript Team (http://www.dailyscript.com.co)
 * @version     1.0
 */

Load::models('configuracion','grupo');

class AjustesController extends AppController {

    public $title = 'Ajustes';

    /**
     * Callback que se ejecuta antes de cualquier método
     */
    public function before_filter() {
        if(Input::isAjax()) {
            View::template(null);
        }
    }

    public function index() {
        $title = 'Ajustes Generales';
        print_r(Router::get());
        if( Input::hasPost('general') ){
            if ( $this->configurar->setConfiguracion(Input::post('general')) ) Flash::valid('Configuración Guardada con éxito');
        }
        $this->favon = ( $this->config['favicon'] == 'on' )?True:False;
        $this->favoff = ( $this->config['favicon'] == 'off' )?True:False;
    }

    public function blog() {
        $title = 'Ajustes del Blog';
        if( Input::hasPost('blog') ){
            if ( $this->configurar->setConfiguracion(Input::post('blog')) ) Flash::valid('Configuración Guardada con éxito');
        }
        $this->comentarioOn = ( $this->config['habilitar_comentarios'] == 'on' )?True:False;
        $this->comentarioOff = ( $this->config['habilitar_comentarios'] == 'off' )?True:False;
    }

    /**
     * Método principal para listar las categorías y la búsqueda de las mismas
     */
    // public function publicaciones() {

    //     $configuracion = new Configuracion();
    //     $configuracion = $configuracion->getOpcion();
    //     $configuracion->habilitar_comentarios = $configuracion;



    //     $categoria = new Taxonomia();
    //     if(Input::hasPost('parametro')) {
    //         $nombre = Filter::get(Input::post('parametro'),'alnum');
    //         $this->categoria = (strlen($nombre) > 0) ? $categoria->buscarTaxonomia(Taxonomia::CATEGORIA,$nombre) : null;
    //         $this->busqueda = $nombre;
    //     } else {
    //         $this->categoria = $categoria->listarTaxonomia('categoria','desc');
    //     }
    // }

//    /**
//     * Método para registrar las etiquetas
//     */
//    public function registrar() {
//        $this->title = 'Agregar categoría ‹ Daily Content - Dailyscript';
//        //Verifico si ha enviado los datos a través del formulario
//        if(Input::hasPost('categoria')) {
//            //Verifico que el formulario coincida con la llave almacenada en sesion
//            if(SecurityKey::isValid()) {
//                $categoria = new Taxonomia(Input::post('categoria')); //Aplico la autocarga
//                $resultado = $categoria->registrarTaxonomia(Taxonomia::CATEGORIA,true);
//            } else {
//                Flash::info('La llave de acceso ha caducado. Por favor intente nuevamente');
//            }
//            Router::redirect('dc-admin/categoria/');
//        }
//    }
//
//    /**
//     * Método para eliminar las categorías
//     *
//     * @param int $id Codigo de la taxonomía
//     * @param string $key Palabra 'key' que viene en la url
//     * @param string $valueKey  Llave de seguridad para prevenir que se elimine directamente desde la url
//     */
//    public function eliminar($id,$key='key',$valueKey='') {
//        //Armo la llave con el código de la url
//         $this->validKey = md5($id.$this->ipKey.$this->expKey.'categoria');
//        //Verifico que la llave de la url sea igual a la llave creada internamente
//        if($this->validKey === $valueKey) {
//            $categoria = new Taxonomia();
//            $categoria->eliminarTaxonomia($id,true);
//        } else {
//            Flash::error('Acceso denegado al sistema.');
//        }
//        Router::redirect('dc-admin/categoria/');
//    }
//
//    /**
//     * Método para editar las categorías
//     *
//     * @param int $id Codigo de la taxonomía
//     * @param string $llave Palabra 'key' que viene en la url
//     * @param string $key  Llave de seguridad para prevenir que se edite directamente desde la url
//     */
//    public function editar($id=null,$key='key',$valueKey='') {
//        $this->title = 'Editar categoría ‹ Daily Content - Dailyscript';
//        //Verifico si ha enviado los datos a través del formulario
//        if(Input::hasPost('categoria')) {
//            //Verifico que el formulario coincida con la llave almacenada en sesion
//            if(SecurityKey::isValid()) {
//                $categoria = new Taxonomia(Input::post('categoria'));//Aplico la autocarga
//                $resultado = $categoria->modificarTaxonomia(true);
//            } else {
//                Flash::info('La llave de acceso ha caducado. Por favor intente nuevamente');
//            }
//            Router::redirect('dc-admin/categoria/');
//        } else {
//            //Armo la llave con el código de la url
//            $this->validKey = md5($id.$this->ipKey.$this->expKey.'categoria');
//            //Verifico que la llave de la url sea igual a la llave creada internamente
//            if($this->validKey === $valueKey) {
//                $categoria = new Taxonomia();
//                $this->categoria = $categoria->getInformacionTaxonomia(Taxonomia::CATEGORIA, $id);
//            } else {
//                Flash::error('Acceso denegado al sistema.');
//                Router::redirect('dc-admin/categoria/');
//            }
//        }
//    }

}
?>
