<?php
/**
 * Dailyscript - app | web | media
 *
 *
 *
 * @category    Opciones
 * @package     Models
 * @author      Iván D. Meléndez
 * @copyright   Copyright (c) 2010 Dailyscript Team (http://www.dailyscript.com.co)
 * @version     1.0
 */

class Configuracion extends ActiveRecord {

    public $logger = true;
    public $app_config = array();

    public function initialize() {

    }

    public function getOpcion($opcion='') {
        if(!$this->app_config) {
            $rs = $this->find();
            foreach($rs as $fila) {
                $this->app_config[$fila->opcion] = $fila->valor;
            }
        }
        if($opcion) {
            if(isset($this->app_config[$opcion])) {
                return $this->app_config[$opcion];
            } else {
                return null;
            }
        } else {
            return $this->app_config;
        }
    }

    public function setOpcion($opcion, $valor) {
        $salida = False;
        $rs = $this->find('opcion = '.$opcion);
        if (!$rs) {
            $this->opcion = Filter::get($opcion, 'string');
            $this->valor = $valor;
            $salida = $this->create();
        } else {
            $rs->valor = $valor;
            $salida = $rs->update();
        }
        return $salida;
    }

    public function setConfiguracion($array){
        $rs = True;
        if (!is_array($array)) throw new Exception('el parametro debe ser un array');
        $this->begin();
        foreach ($array as $key => $value) {
            $rs = $rs && $this->setOpcion($key, $value);
            if (!$rs) break;
        }
        if ($rs) ? $this->commit() : $this->rollback();
        return $rs;
    }
}

?>
