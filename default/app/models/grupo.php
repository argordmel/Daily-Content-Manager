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

class Grupo extends ActiveRecord {

    const COLABORADOR = 1;
    const AUTOR = 2;
    const EDITOR= 3;
    const ADMINISTRADOR = 4;

    /**
     * Metodo para definir las relaciones
     */
    public function initialize() {
        $this->has_many('usuario');
    }

    /**
     * Metodo para obtener los datos del grupo del usuario
     * @param int $grupo Identificador del grupo del usuario
     * @return array
     */
    public function getGrupoUsuario($grupo) {
        $grupo = Filter::get($grupo,'numeric');
        if($grupo != "") {
            $condicion = "id = '$grupo'";
            return $this->find_first('columns: descripcion', 'conditions: '.$condicion);
        } else {
            return false;
        }
    }

    /**
     * Metodo para listar todos los grupos de usuario
     * @return array
     */
    public function getListado() {        
        return $this->find('conditions: '.$condicion,'order: descripcion ASC');
    }
    
}

?>
