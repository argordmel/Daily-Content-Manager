<?php 

/*
 * $Id: jFileBrowser, 2010.
 * @author Juaniquillo
 * @copyright Copyright © 2010, Victor Sanchez (Juaniquillo).
 * @email juaniquillo@gmail.com
 * @website http://juaniquillo.com
*/

$sql_host = 'localhost';

/////informacion MySQL

$sql_db = "Base_de_datos";
$sql_user = "Usuario";
$sql_password = "Password";

//Funciones
include('funciones.inc.php');
//PHPPaging
include('PHPPaging.lib.php');


//Conexion
$conexion_gal = db_connect($sql_host, $sql_user, $sql_password) or die('No se puede conectar a la base de datos');
db_select_db($sql_db, $conexion_gal);

?>