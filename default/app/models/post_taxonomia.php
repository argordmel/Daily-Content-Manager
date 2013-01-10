<?php
/**
 * Dailyscript - app | web | media
 *
 *
 *
 * @category    Administracion
 * @package     Models
 * @author      Iván D. Meléndez
 * @copyright   Copyright (c) 2010 Dailyscript Team (http://www.dailyscript.com.co)
 * @version     1.0
 */

class PostTaxonomia extends ActiveRecord {

    public $logger = true;        
    
    public function initialize() {
        $this->belongs_to('taxonomia');
        $this->belongs_to('post');
    }

    /**
     * Metodo para registrar la clasificación del post
     * 
     * @param string $tipo Tipo de clasificación a registrar
     * @param arrray | string $taxonomia Array o String de taxonomias a registrar
     * @param int $post Código del post
     * @param boolean $mensaje Indica si se muestra el mensaje de confirmación del registro
     * @return array
     */
    public function registrarPostTaxonomia($tipo, $taxonomia, $post, $mensaje = false) {
        //verifico que contenga algo
        if($taxonomia) {
            //Verifico si es categoria
            if( $tipo == Taxonomia::CATEGORIA) {
                //Verifico si la categoria es un array
                if(is_array($taxonomia)) {
                    foreach($taxonomia as $fila) {
                        $post_categoria = new PostTaxonomia();
                        $post_categoria->post_id = $post;
                        $post_categoria->taxonomia_id = $fila;
                        $post_categoria->save();
                    }
                } else {
                    $post_categoria = new PostTaxonomia();
                    $post_categoria->post_id = $post;
                    $post_categoria->taxonomia_id = $taxonomia;
                    $post_categoria->save();
                }
            } else {
                //Separo las etiquetas por coma
                $taxonomia = explode(',', $taxonomia);
                //Si es un array
                if(is_array($taxonomia)) {
                    foreach ($taxonomia as $fila) {
                        $fila = trim($fila);//Quito los espacios
                        $etiqueta = new Taxonomia();
                        //Verifico si está registrada
                        if(!$etiqueta->getInformacionTaxonomia(Taxonomia::ETIQUETA, '', $fila)){
                            $etiqueta = new Taxonomia();
                            $etiqueta->nombre = $fila;
                            $etiqueta->tipo = Taxonomia::ETIQUETA;
                            $etiqueta->save();
                        }
                        //Si guardo la etiqueta
                        if(isset($etiqueta->id)) {
                            $post_etiqueta = new PostTaxonomia();
                            $post_etiqueta->post_id = $post;
                            $post_etiqueta->taxonomia_id = $etiqueta->id;
                            $post_etiqueta->save();
                        }
                        //Tengo que eliminar los pk para poder continuar porque lo que hace es actualizar ¿?
                        $post_etiqueta->id = null;
                        $etiqueta->id = null;
                    }
                } else {
                    $taxonomia = trim($taxonomia);
                    $etiqueta = new Taxonomia();
                    if(!$etiqueta->getInformacionTaxonomia(Taxonomia::ETIQUETA, '', $taxonomia)){
                        $etiqueta = new Taxonomia();
                        $etiqueta->nombre = $taxonomia;
                        $etiqueta->tipo = Taxonomia::ETIQUETA;
                        $etiqueta->save();
                    }
                    if(isset($etiqueta->id)) {
                        $post_etiqueta = new PostTaxonomia();
                        $post_etiqueta->post_id = $post;
                        $post_etiqueta->taxonomia_id = $etiqueta->id;
                        $post_etiqueta->save();
                    }
                }

            }
            
            return true;
        }

    }
    
    /**
     * Metodo para contar los post registrados segun su clasificacion
     */
    public function getContadorPostTaxonomia($taxonomia) {
        $taxonomia = Filter::get($taxonomia,'int');
        $condicion = "taxonomia_id = $taxonomia";
        return $this->count("conditions: $condicion");
    }


    public function eliminarPostTaxonomia($post) {
        $post = Filter::get($post,'int');
        if($post) {
            if($this->find_first('condition: post_id = '.$post)) {
                $this->delete_all("post_id = '$post'");
            }
            return true;
        }
        return false;

    }

    /**
     * Callback que se ejecuta antes de guardar o modificar un registro
     */
    public function before_save() {        
        if($this->find_first("conditions: post_id = $this->post_id AND taxonomia_id = $this->taxonomia_id")) {
            return 'cancel';
        }
    }

    /**
     * Callback que se ejecuta antes de eliminar un registro
     */
    public function before_delete() {
        //Verificar que no existan post con esta categoria
    }
       
}

?>
