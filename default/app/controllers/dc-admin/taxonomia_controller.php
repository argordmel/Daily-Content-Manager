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

Load::models('taxonomia');

class TaxonomiaController extends ApplicationController {

    /**
     * Callback que se ejecuta antes de cualquier método
     */
    public function before_filter() {        
        if(Input::isAjax()) {
            View::template(null);
        }
    }

    /**     
     * Método principal para listar las taxonomías dependiendo de su tipo
     *
     * @param string $tipo Tipo de taxonomia a listar 'etiqueta' o 'categoria'
     */
    public function listar($tipo) {
        if( ($tipo === 'categoria') or ($tipo === 'etiqueta') ) {
            //Asigno el título a la página
            $this->title = ($tipo == 'categoria') ? 'Categorías' : 'Etiquetas';
            //Determino la clasificación
            $this->tipo = ($tipo == 'categoria') ? Taxonomia::CATEGORIA : Taxonomia::ETIQUETA;
            $taxonomia = new Taxonomia();
            if(Input::hasPost('parametro')) {
                $nombre = Input::post('parametro');
                $this->taxonomia = (strlen($nombre) > 0) ? $taxonomia->buscarTaxonomia($this->tipo, $nombre) : null;
                $this->busqueda = $nombre;
            } else {
                $this->taxonomia = $taxonomia->listarTaxonomia($this->tipo,'desc');
            }
            //Asigno la clasificación
            $this->clasificacion = ($this->tipo === Taxonomia::CATEGORIA) ? 'categoría' : 'etiqueta';
        } else {
            Flash::info('Acceso incorrecto al sistema.');
            Router::redirect('dc-admin/');
        }
    }

    /**
     * Método para registrar las taxonomias
     *
     * @param string $tipo Tipo de taxonomia a registrar 'etiqueta' o 'categoria'
     */
    public function registrar($tipo) {        
        if( ($tipo === 'categoria') or ($tipo === 'etiqueta') ) {
            //Asigno el título a la página
            $this->title = ($tipo == 'categoria') ? 'Editar categoría' : 'Editar etiqueta';
            //Verifico si ha enviado los datos a través del formulario
            if(Input::hasPost('taxonomia')) {
                //Verifico que el formulario coincida con la llave almacenada en sesion
                if(SecurityKey::isValid()) {
                    //Determino el tipo de taxonomia a registrar
                    $this->tipo = ($tipo == 'categoria') ? Taxonomia::CATEGORIA : Taxonomia::ETIQUETA;
                    $taxonomia = new Taxonomia(Input::post('taxonomia'));//Aplico la autocarga
                    $taxonomia = $taxonomia->registrarTaxonomia($this->tipo,true);
                } else {
                    Flash::info('La llave de acceso ha caducado. Por favor intente nuevamente');
                }
                Router::redirect('dc-admin/'.$tipo.'/');
            }
        } else {
            Flash::info('Acceso incorrecto al sistema.');
            Router::redirect('dc-admin/');
        }
    }

    /**
     * Método para editar las taxonomias
     *
     * @param string $tipo Tipo de taxonomia a editar 'etiqueta' o 'categoria'
     * @param int $id Codigo de la taxonomía
     * @param string $key Palabra 'key' que viene en la url
     * @param string $valueKey  Llave de seguridad para prevenir que se edite directamente desde la url
     */
    public function editar($tipo, $id=null,$key='key',$valueKey='') {
        if( ($tipo === 'categoria') or ($tipo === 'etiqueta') ) {
            //Asigno el título a la página
            $this->title = ($tipo == 'categoria') ? 'Editar categoría' : 'Editar etiqueta';
            //Verifico si ha enviado los datos a través del formulario
            if(Input::hasPost('taxonomia')) {
                //Verifico que el formulario coincida con la llave almacenada en sesion
                if(SecurityKey::isValid()) {
                    $taxonomia = new Taxonomia(Input::post('taxonomia'));//Aplico la autocarga
                    $taxonomia = $taxonomia->modificarTaxonomia(true);
                } else {
                    Flash::info('La llave de acceso ha caducado. Por favor intente nuevamente');
                }
                Router::redirect('dc-admin/'.$tipo.'/');
            } else {
                //Armo la llave con el código de la url
                $this->validKey = md5($id.$this->ipKey.$this->expKey.$tipo);
                //Verifico que la llave de la url sea igual a la llave creada internamente
                if($this->validKey === $valueKey) {
                    $this->tipo = ($tipo == 'categoria') ? Taxonomia::CATEGORIA : Taxonomia::ETIQUETA;
                    $taxonomia = new Taxonomia();
                    $this->taxonomia = $taxonomia->getInformacionTaxonomia($this->tipo, $id);
                    //Asigno la clasificación
                    $this->clasificacion = ($this->tipo === Taxonomia::CATEGORIA) ? 'categoría' : 'etiqueta';
                } else {
                    Flash::error('Acceso denegado al sistema.');
                    Router::redirect('dc-admin/'.$tipo.'/');
                }
            }                       
        } else {
            Flash::info('Acceso incorrecto al sistema.');
            Router::redirect('dc-admin/');
        }
    }

    /**
     * Método para eliminar las taxonomias
     *
     * @param string $tipo Tipo de taxonomia a eliminar 'etiqueta' o 'categoria'
     * @param int $id Codigo de la taxonomía
     * @param string $key Palabra 'key' que viene en la url
     * @param string $valueKey  Llave de seguridad para prevenir que se elimine directamente desde la url
     */
    public function eliminar($tipo,$id,$key='key',$valueKey='') {
        if( ($tipo === 'categoria') or ($tipo === 'etiqueta') ) {
            //Armo la llave con el código de la url
            $this->validKey = md5($id.$this->ipKey.$this->expKey.$tipo);
            //Verifico que la llave de la url sea igual a la llave creada internamente
            if($this->validKey === $valueKey) {
                $taxonomia = new Taxonomia();
                $taxonomia->eliminarTaxonomia($id,true);
            } else {
                Flash::error('Acceso denegado al sistema.');
            }
            Router::redirect('dc-admin/'.$tipo.'/');
        } else {
            Flash::info('Acceso incorrecto al sistema.');
            Router::redirect('dc-admin/');
        }        
    }
    
}
?>
