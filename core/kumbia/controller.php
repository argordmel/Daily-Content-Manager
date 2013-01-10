<?php
/**
 * KumbiaPHP web & app Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://wiki.kumbiaphp.com/Licencia
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@kumbiaphp.com so we can send you a copy immediately.
 *
 * ApplicationController Es la clase principal para controladores de Kumbia
 * 
 * @category   Kumbia
 * @package    Controller 
 * @copyright  Copyright (c) 2005-2009 Kumbia Team (http://www.kumbiaphp.com)
 * @license    http://wiki.kumbiaphp.com/Licencia     New BSD License
 */
class Controller 
{
	/**
	 * Nombre del modulo actual
	 *
	 * @var string
	 */
	public $module_name;
	/**
	 * Nombre del controlador actual
	 *
	 * @var string
	 */
	public $controller_name;
	/**
	 * Nombre de la acción actual
	 *
	 * @var string
	 */
	public $action_name;
	/**
	 * Limita la cantidad correcta de 
	 * parametros de una action
	 *
	 * @var bool
	 */
	public $limit_params = TRUE;
	/**
	 * Nombre del scaffold a usar
	 *
	 * @var string
	 */
	public $scaffold;
	/**
	 * Constructor
	 *
	 * @param string $module modulo al que pertenece el controlador
	 * @param string $controller nombre del controlador
	 * @param string $action nombre de la accion
	 * @param array $parameters parametros enviados por url
	 **/
	public function __construct($module, $controller, $action, $parameters) {
		//TODO: enviar un objeto
		$this->module_name = $module;
		$this->controller_name = $controller;
		$this->parameters = $parameters;
		$this->action_name = $action;
	}	
	
    /**
     * BeforeFilter
     * 
     * @return bool
     */
    protected function before_filter()
    {
    }
    /**
     * AfterFilter
     * 
     * @return bool
     */
    protected function after_filter()
    {
    }
	/**
     * Initialize
     * 
     * @return bool
     */
    protected function initialize()
    {
    }
    /**
     * Finalize
     * 
     * @return bool
     */
    protected function finalize()
    {
    }

    final public function k_callback($method) 
    { 
        return $this->$method(); 
    }
}