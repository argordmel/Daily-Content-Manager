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

Load::models('post');

class IndexController extends ApplicationController {
    
    public function before_filter() {

    }

    public function index() {
        //Defino el titulo de la vista
        $this->title = 'Escritorio';

        $post = new Post();
        $taxonomia = new Taxonomia();
        
        //Totalizadores de publicaciones y taxonomias
        $this->total_post = $post->getContadorPost('todos');
        $this->total_borradores = $post->getContadorPost('todos',Post::BORRADOR);
        $this->total_categorias = $taxonomia->getContadorTaxonomia(Taxonomia::CATEGORIA);
        $this->total_etiquetas = $taxonomia->getContadorTaxonomia(Taxonomia::ETIQUETA);        
        //Totalizadores de comentarios
        $this->total_comentarios = 0;
        $this->total_pendientes = 0;
        $this->total_spam = 0;

        /* Nombre a mostrar en los link */
        $this->detalle_post = ($this->total_post > 1) ? 'Publicaciones' : 'Publicación';
        $this->detalle_borrador = ($this->total_borradores > 1) ? 'Borradores' : 'Borrador';
        $this->detalle_categoria = ($this->total_categorias > 1) ? 'Categorías' : 'Categoría';
        $this->detalle_etiqueta = ($this->total_etiquetas > 1) ? 'Etiquetas' : 'Etiqueta';
        $this->detalle_comentario = ($this->total_comentarios > 1) ? 'Comentarios' : 'Comentario';
        $this->detalle_pendiente = ($this->total_pendientes > 1) ? 'Pendientes' : 'Pendiente';
        $this->detalle_spam = 'Spam';
    }
   
}
?>
