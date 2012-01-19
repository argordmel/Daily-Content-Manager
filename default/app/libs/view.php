<?php
/**
 * Esta clase permite extender o modificar la clase ViewBase de Kumbiaphp.
 *
 * @category KumbiaPHP
 * @package View
 **/

// @see KumbiaView
require_once CORE_PATH . 'kumbia/kumbia_view.php';

class View extends KumbiaView {

    /**
     * Cambia el view y el template por el de error
     */
    public static function notFound() {
        self::$_view = 'error';
        self::$_template = 'error';
    }

}
