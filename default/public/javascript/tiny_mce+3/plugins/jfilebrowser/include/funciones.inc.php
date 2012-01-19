<?php 

/*
 * $Id: jFileBrowser, 2010.
 * @author Juaniquillo
 * @copyright Copyright © 2010, Victor Sanchez (Juaniquillo).
 * @email juaniquillo@gmail.com
 * @website http://juaniquillo.com
*/


//funciones para la conexion
function db_connect($sql_host, $sql_user, $sql_password) {
	return mysql_connect($sql_host, $sql_user, $sql_password);
}

function db_select_db($sql_db, $conexion_gal) {
	return mysql_select_db($sql_db, $conexion_gal);
}


//Escape characters
if (!function_exists("GetSQLValueString")) {
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;

  $theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? "'" . doubleval($theValue) . "'" : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}
}



//Funcion que compara dos fechas y devuelve la diferencia (day-month-year)
function compara_fechas($fecha1,$fecha2){
	 if (preg_match("/[0-9]{1,2}\/[0-9]{1,2}\/([0-9][0-9]){1,2}/",$fecha1)) 
		 list($dia1,$mes1,$año1)=split("/",$fecha1);
	 if (preg_match("/[0-9]{1,2}-[0-9]{1,2}-([0-9][0-9]){1,2}/",$fecha1)) 
		 list($dia1,$mes1,$año1)=split("-",$fecha1);
	 if (preg_match("/[0-9]{1,2}\/[0-9]{1,2}\/([0-9][0-9]){1,2}/",$fecha2)) 
		 list($dia2,$mes2,$año2)=split("/",$fecha2);
	 if (preg_match("/[0-9]{1,2}-[0-9]{1,2}-([0-9][0-9]){1,2}/",$fecha2)) 
		 list($dia2,$mes2,$año2)=split("-",$fecha2);
		 
	 $dif = mktime(0,0,0,$mes1,$dia1,$año1) - mktime(0,0,0, $mes2,$dia2,$año2);
	 return ($dif);                         
}

//Funcion que compara dos fechas y devuelve la diferencia (year-month-day)
function compara_fechas2($fecha1,$fecha2){
	 if (preg_match("/[0-9]{1,2}\/[0-9]{1,2}\/([0-9][0-9]){1,2}/",$fecha1)) 
		 list($año1,$mes1,$dia1)=split("/",$fecha1);
	 if (preg_match("/[0-9]{1,2}-[0-9]{1,2}-([0-9][0-9]){1,2}/",$fecha1)) 
		 list($año1,$mes1,$dia1)=split("-",$fecha1);
	 if (preg_match("/[0-9]{1,2}\/[0-9]{1,2}\/([0-9][0-9]){1,2}/",$fecha2)) 
		 list($año2,$mes2,$dia2)=split("/",$fecha2);
	 if (preg_match("/[0-9]{1,2}-[0-9]{1,2}-([0-9][0-9]){1,2}/",$fecha2)) 
		 list($año2,$mes2,$dia2)=split("-",$fecha2);
		 
	 $dif = mktime(0,0,0,$mes1,$dia1,$año1) - mktime(0,0,0, $mes2,$dia2,$año2);
	 return ($dif);                         
}

//Funcion que compara dos fechas y devuelve la diferencia (year-month-day)
function compara_fechas3($fecha1,$fecha2){
	 $fecha_1 = split(" ", $fecha1);
	 list($año1,$mes1,$dia1)=split("-", $fecha_1[0]);
	 list($hora1,$min1,$seg1)=split(":", $fecha_1[1]);
	 
	 $fecha_2 = split(" ", $fecha2);
	 list($año2,$mes2,$dia2) = split("-", $fecha_2[0]);
	 list($hora2,$min2,$seg2) = split(":", $fecha_2[1]);
	 
	 $dif = mktime($hora1, $min1, $seg1, $mes1, $dia1, $año1) - mktime($hora2, $min2, $seg2, $mes2, $dia2, $año2);
	 return ($dif);                         
}


//calcural dia proximso y previos
function proxPrevDia($fecha, $tipo = 'pro', $offset = 1){
	 list($año,$mes,$dia) = explode("-",$fecha);
	 
	 switch($tipo){
	 	case 'pro':{
			$fecha2 = date("Y-m-d", mktime(0, 0, 0, $mes, ($dia+$offset), $año));
			//$fecha2 = date($año.'-'.$mes.'-'.$dia)+1;
		}
		break;
		case 'pre':{
			$fecha2 = date("Y-m-d", mktime(0, 0, 0, $mes, ($dia-$offset), $año));
			//$fecha2 = date($año.'-'.$mes.'-'.$dia)-1;
		}
		break;
		default: return false;
	 }
	 return $fecha2;
}



////////////////////////// Validacion ////////////////////////// 
function validarReq($valor, $men){
	if(empty($valor)) $error = $men;
	return $error;
}
function validarEmail($valor, $men){
	if (!ereg("^([a-zA-Z0-9_\-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$",$valor)) $error = $men;
	return $error;
}
function validarMenu($valor, $men, $max_val_act = FALSE, $max_val = 3){ 
	if(empty($valor) || $valor < 1) $error = $men;
	if($max_val_act == TRUE) if($valor > $max_val) $error = $men;
	return $error;exit;
}
function validarRegEx($valor, $men, $regex){
	if(empty($valor) || empty($regex)) $error = $men;
	if (!ereg($regex,$valor)) $error = $men;
	return $error;
}

////////////////////////// Interaccion con la base de datos ///////////////////
//insert
function InsertarInfo($tabla, $campos, $valores, $database='', $coneccion='',$ver_query = FALSE){
	if(!empty($database) && !empty($coneccion))mysql_select_db($database, $coneccion);
	$query = "INSERT INTO $tabla ($campos) VALUES ($valores)";
	//echo $query;exit;
	if($ver_query) echo $query;
	if(!$resultado = mysql_query($query)) {
		echo mysql_error();//return FALSE;
	}
}
//editar
function EditarInfo($tabla, $campos_valores, $id_campo, $id_valor, $database='', $coneccion='', $ver_query = FALSE){
	if(!empty($database) && !empty($coneccion))mysql_select_db($database, $coneccion);
	$query = "UPDATE $tabla SET $campos_valores WHERE $id_campo = $id_valor";
	if($ver_query == TRUE) {
		echo $query;
		return;
	}
	if(!$resultado = mysql_query($query)) echo mysql_error();//return FALSE;
	else return true;
}
//seleccionar
function SeleccionarInfo($tabla, $campo, $database='', $coneccion='', $where = FALSE, $campo_valor_where = '', $orden = FALSE, $orden_campo_AD = '', $limit = FALSE, $limit_valor = '', $show_query = FALSE){
	if(!empty($database) && !empty($coneccion)) mysql_select_db($database, $coneccion);
	$query = "SELECT $campo FROM $tabla";
	if($where == TRUE)  $query .= " WHERE $campo_valor_where";
	if($orden == TRUE)  $query .= " ORDER BY $orden_campo_AD";
	if($limit == TRUE ) $query .= " LIMIT $limit_valor";
	//echo $query;exit;
	if(!$resultado = mysql_query($query)) {
		echo mysql_error();//return FALSE;
		echo '<br />'.$query;exit;
	}
	if($show_query) echo $query;
	else return $resultado;
}
//numero de campos
function NumerodeCampos($query){
	$cantidad_query = mysql_num_rows($query);
	return $cantidad_query;
}
//obtener mysql_fetch_assoc
function ResultadoArrayAssoc($query){
	return mysql_fetch_assoc($query);
}
//borrar record
function borrarRecord($tabla, $valor_id, $valor){
	$query = "DELETE FROM $tabla WHERE $valor_id = $valor";
	if(!$resultado = mysql_query($query)) echo mysql_error();//return FALSE;
	else return $resultado;
}
//Borrar archivo
function BorrarArchivo($id_archiv, $db, $con, $direct='../archivos/productos/'){
	
	mysql_select_db($db, $con);
	$query_arch_bor = "SELECT * FROM archivos WHERE id_archivos = $id_archiv";
	$arch_bor = mysql_query($query_arch_bor, $con) or die(mysql_error());
	$row_arch_bor = mysql_fetch_assoc($arch_bor);
	$num_rows_arch_bor = mysql_num_rows($arch_bor);
	
	$arch_a_borr = $direct.$row_arch_bor['archivo_archivos'];
	
	
	if($num_rows_arch_bor > 0){
		$query_borrar = "DELETE FROM archivos WHERE id_archivos = $id_archiv";
		
		mysql_select_db($db, $con);
		if(!$rs_pueblo = mysql_query($query_borrar, $con)) {
			return FALSE;
			//echo $query_borrar;exit;
		}
		else {
			if(file_exists ($arch_a_borr)) {
				unlink ($arch_a_borr);
				return TRUE;
			}
			else{
				return FALSE;
			}
		}
	}
	else return FALSE;
}
//borrar archivo 1
function BorrarArchivo2($arch_a_borr){
	if(file_exists ($arch_a_borr)) {
		unlink ($arch_a_borr);
		return TRUE;
	}
	else{
		return FALSE;
	}
}

/////////////////////////// creacion de Passwords//////////////////////
function genera_password($longitud,$tipo="alfanumerico"){

    if ($tipo=="alfanumerico"){
        $exp_reg="[^A-Z0-9]";
    } elseif ($tipo=="numerico"){
        $exp_reg="[^0-9]";
    }
    
    return substr(eregi_replace($exp_reg, "", md5(rand())) .
       eregi_replace($exp_reg, "", md5(rand())) .
       eregi_replace($exp_reg, "", md5(rand())),
       0, $longitud);
} 

//remover los elementod vacios del array
function limpiarArr($arr){
	foreach ($arr as $key => $value) {
		if (is_null($value) || $value=="" || is_null($key) || $key=="") {
			unset($arr[$key]);
		}
	}
	return $arr;
}

