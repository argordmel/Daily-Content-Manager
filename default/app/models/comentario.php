<?php
/**
 * Dailyscript - app | web | media
 *
 *
 *
 * @category    Administracion
 * @package     Models
 * @author      Jaro Marval
 * @copyright   Copyright (c) 2010 Dailyscript Team (http://www.dailyscript.com.co)
 * @version     1.0
 */

Load::models('post','usuario');

class Comentario extends ActiveRecord {

    public $logger = true;

    const PENDIENTE = 1;
    const APROBADO = 2;
    const SPAM = 3;
    const ELIMINADO = 4;

    public function registarComentario() {

    }

    public function filtrarComentarios($estado) {
        $conditions = 'conditions: comentario.';
        $conditions .= ( $estado == 'todos' )? 'estado != '.self::SPAM :  'estado = '.$estado;
        $columns = "columns: comentario.id, autor, email, mensaje, ip, comentario.registrado_at, titulo, comentario.estado";
        $join = "join: INNER JOIN post ON comentario.post_id = post.id";

        return $this->find($conditions, $columns, $join);
    }

    public function procesarComentario(){

    }

    public function getContadorComentario($estado) {
        $condicion = ( $estado == 'todos' )? 'estado LIKE \'%\'' :  'estado = '.Filter::get($estado,'int');
        return $this->count("conditions: $condicion");
    }

}