<?php

/**
 
 * $Id: jFileBrowser, 2010.
 * @author Juaniquillo
 * @copyright Copyright © 2010, Victor Sanchez (Juaniquillo).
 * @email juaniquillo@gmail.com
 * @website http://juaniquillo.com

 */


$validacion = $_POST['validacion'];
$sub_validacion = $_POST['sub_validacion'];

//validaciones
if (isset($validacion) && !empty($validacion)) {
	
	include('mensajes.inc.php');
	//recogiendo los valores del form
	
	$usuario_val_1 = trim($_POST['usuario']);
	$nombre_val_1 = trim($_POST['nombre']);
	$id_val_1 = trim($_POST['id']);
	$valor_val_1 = trim($_POST['value']);
	$pwd_val_1 = trim($_POST['contrasena']);
	$pwd2_val_1 = trim($_POST['contrasena2']);
	$email_val_1 = trim($_POST['email']);
	$email2_val_1 = trim($_POST['email2']);
	$apellido_val_1 = trim($_POST['apellido']);
	$pueblo_id_val_1 = $_POST['pueblos'];
	$categoria_val_1 = $_POST['categoria'];
	$categoria2_val_1 = $_POST['categoria2'];
	$direccion1_val_1 = trim($_POST['direccion1']);
	$direccion2_val_1 = trim($_POST['direccion2']);
	$pueblo2_id_val_1 = $_POST['pueblo2'];
	$zip_val_1 = trim($_POST['zip']);
	$telefono_val_1 = trim($_POST['telefono']);
	$telefono2_val_1 = trim($_POST['telefono2']);
	$fax_val_1 = trim($_POST['fax']);
	$descripcion_val_1 = $_POST['descripcion'];
	$descripcion_info_val_1 = $_POST['descripcion_info'];
	$descripcion_pag_val_1 = $_POST['descripcion_pag'];
	$anuncio_val_1 = $_POST['anuncio_editable_ta'];
	$id_anuncio_val_1 = trim($_POST['id_anuncio']);
	$disponible_val_1 = $_POST['disponible'];
	$web_val_1 = $_POST['web'];
	$enviar_web_val_1 = $_POST['enviar_web'];
	$orden_val_1 = $_POST['orden'];
	$anunciante_val_1 = $_POST['anunciante'];
	$nivel_val_1 = $_POST['nivel'];
	$areal_val_1 = $_POST['area'];
	$activo_val_1 = $_POST['activo'];
	$publico_val_1 = $_POST['publico'];
	$costum_val_1 = $_POST['costum'];
	$meta_val_1 = $_POST['meta'];
	$domain_val_1 = $_POST['domain'];
	$domain_seguro_val_1 = $_POST['domain_seguro'];
	$anio_comienzo_val_1 = $_POST['anio_comienzo'];
	$nombre_dueno_val_1 = $_POST['nombre_dueno'];
	$direccion_dueno_val_1 = $_POST['direccion_dueno'];
	$archivo_costum_val_1 = $_POST['archivo_costum'];
	$codigo_val_1 = $_POST['codigo'];
	$precio_val_1 = $_POST['precio'];
	$precio2_val_1 = $_POST['precio2'];
	$descuento_val_1 = $_POST['descuento'];
	$descuento2_val_1 = $_POST['descuento2'];
	$stock_val_1 = $_POST['stock'];
	$producto_val_1 = $_POST['producto'];
	$contenido_val_1 = $_POST['contenido'];
	$select_cont_val_1 = $_POST['select_cont'];
	$select_mod_val_1 = $_POST['select_mod'];
	$html_mode_val_1 = $_POST['html_mode'];
	$script_val_1 = $_POST["script"];
	$orden_val_1 = $_POST["ordenar1"];
	$peso_val_1 = $_POST["peso"];
	$estilos_val_1 = $_POST["estilos"];
	$button_ml = $_POST['button_ml'];
	$tipo_cat_val_1 = $_POST['tipo_cat'];
	$content_pos_val_1 = $_POST['content_pos'];
	$email_gen_usar_val_1 = $_POST['email_gen_usar'];
	$titulo_val_1 = $_POST['titulo'];
	$disclaimer_val_1 = $_POST['disclaimer'];
	$sub_padre_val_1 = $_POST['sub-padre'];
	$sub_nivel_val_1 = $_POST['sub-nivel'];
	$disponible_car_val_1 = $_POST['disponible_car'];
	$en_menu_val_1 = trim($_POST['en_menu']);
	$att_grupo_val_1 = $_POST['att_grupo'];
	$att_indiv_val_1 = $_POST['att_indiv'];
	$id_cat_val_1 = $_POST['id_cat'];
	$ajax_val_1 = $_POST['ajax'];
	$cantidad_val_1 = $_POST['cantidad'];
	$shipping_val_1 = $_POST['shipping'];
	$descripcion_email_val_1 = $_POST['descripcion_email'];
	
	$imagen_val_1 = $_FILES['imagen']['tmp_name'];
	$borrar_imagen_val_1 = $_POST['borrar_imagen'];
	
	$archivo_val_1 = $_FILES['archivo']['tmp_name'];
	
	//array de tipos de imagenes y archivos
	//$get_imag = array('1' => 'gif', '2' => 'jpg', '3' => 'png');
	//$get_archiv = array('1' => 'pdf', '2' => 'doc', '3' => 'docx', '4' => 'xls', '5' => 'xlsx', '6' => 'ppt', '7' => 'pptx');
	$get_archiv_img = array('pdf', 'doc', 'docx', 'xls','xlsx','ppt', 'pptx', 'gif', 'jpg', 'txt', 'jpeg', 'png');
	
	$header_generico = "Location: home.php?mostrar=$mostrar&sub=$sub_secc&id=$id&modulo=$modulo&id2=$id2";
	
	//expresiones regulares
	$lat = "a-zA-ZÁÉÍÓÚáéíóú"; //caracteres latinos alfanumricos
	$lat2 = "a-zA-Z0-9_-"; //caracteres latinos alfanumricos con guion
	$lat3 = "0-9"; //numeros del 0 al 9
	$lat4 = "0-9 ()-"; //numeros del 0 al 9 mas parentisis, guion y espacio
	$lat5 = "a-zA-Z -_"; //caracteres latinos alfanumricos con guion y espacio
	$lat6 = "a-zA-Z_-"; //caracteres latinos alfanumricos con guion
	$lat7 = "a-zA-Z0-9 _-"; //caracteres latinos alfanumricos con guion y espacio
	
	switch($validacion){
		//crear categoria
		case 1:{
			if(strlen($nombre_val_1) > 50) $mensaje_err .= $mensaje_glob_v[2];
			$mensaje_err .= $mensaje_glob_v[validarReq($nombre_val_1, 1)];
			//$mensaje_err .= $mensaje_glob_v[validarReq($activo_val_1, 80)];
			//$mensaje_err .= $mensaje_glob_v[validarReq($tipo_cat_val_1, 11)];
			
			if(empty($mensaje_err)) {
				$insert_nom = GetSQLValueString($nombre_val_1, "text");
				//$insert_descrip_lar = GetSQLValueString($descrip_larg_val_1, "text");
				$tipo_cat_nom = GetSQLValueString($tipo_cat_val_1, "int");
				$activo_cat = GetSQLValueString($activo_val_1, "int");
				$insert_date = GetSQLValueString($fecha_act, "date");
				$insert_usu = GetSQLValueString($usuario_nombre, "text");
				$insert_padre = GetSQLValueString($sub_padre_val_1, "int");
				$insert_nivel = GetSQLValueString($sub_nivel_val_1, "int");
				
				$campos_cat = 'tipo_cat, name_cat, status_cat, fecha_cat, usu_cat';
				$valores_cat = "$tipo_cat_nom, $insert_nom, 1, $insert_date, $insert_usu";
				
				if(InsertarInfo('categorias', $campos_cat, $valores_cat, $sql_db, $conexion_gal)) $mensaje_err = $mensaje_glob_v[11];
				header('Location: filebrowser.php');
			}
		}
		break;
		//borrar categoria
		case 2:{
			$mensaje_err .= $mensaje_glob_v[validarReq($id_val_1, 11)];
			
			if(empty($mensaje_err)) {
				$insert_id = GetSQLValueString($id_val_1, "int");
				//exit;
				$query_img = SeleccionarInfo('archivos', '*', $sql_db, $conexion_gal, TRUE, "categoria_archivos = $insert_id");
				$num_img = NumerodeCampos($query_img);
				//exit;
				$query_rs_contenido2 = "DELETE FROM categorias WHERE cat_id_cat = $insert_id";
				if(!$rs_contenido2 = mysql_query($query_rs_contenido2, $conexion_gal)) $mensaje_err = $mensaje_glob_v[11];
				else {
					$query_img = SeleccionarInfo('archivos', '*', $sql_db, $conexion_gal, TRUE, "categoria_archivos = $insert_id");
					$num_img = NumerodeCampos($query_img);
					if($num_img > 0){
						while($result_categorias_img = ResultadoArrayAssoc($query_img)){
							$id_archv_brr = $result_categorias_img['id_archivos'];
							if(!BorrarArchivo($id_archv_brr, $sql_db, $conexion_gal, $ruta.'archivos/')) {
								$mensaje_err = $mensaje_glob_v[11];
								break;
							}
						}
					}
				header('Location: filebrowser.php');
				}
			}
		}
		break;
		//subir imagen
		case 3:{
			//echo count($archivo_val_1);exit;
			//if(count($archivo_val_1) < 1) $mensaje_err .= $mensaje_glob_v[3];
			if(count($archivo_val_1) > 6) $mensaje_err .= $mensaje_glob_v[4];
			$mensaje_err .= $mensaje_glob_v[validarMenu($categoria_val_1, 2)];
			
			$tamano_img = explode('.',$_FILES['archivo']['name'][$key]);
			$cantidad_archv = count($tamano_img);
			$extension = strtolower($tamano_img[--$cantidad_archv]);
			
			foreach($_FILES['archivo']['name'] as $key2 => $valor){
				if(!empty($valor)){
					$tamano_img = explode('.',$_FILES['archivo']['name'][$key2]);
					$cantidad_archv = count($tamano_img);
					$extension = strtolower($tamano_img[--$cantidad_archv]);
					
					if(!in_array($extension, $get_archiv_img)) $mensaje_err .= $mensaje_glob_v[5];
				}
				else $mensaje_err .= $mensaje_glob_v[3];
				if($_FILES['archivo']['size'][$key2] > 4194304) $mensaje_err .= $mensaje_glob_v[6];
			}
			//if(empty($extension)) $mensaje_err .= $mensaje_glob_v[validarReq($id_val_1, 11)];
			
			if(empty($mensaje_err)) {
				
				foreach($_FILES['archivo']['tmp_name'] as $key => $valor) {
					if(!empty($valor)){
						//$insert_nom = GetSQLValueString($nombre_val_1, "text");
					
						$insert_cat = GetSQLValueString($categoria_val_1, "int");
						$insert_categoria = GetSQLValueString($categoria_val_1, "int");
						$insert_nombre_orig = GetSQLValueString($_FILES['archivo']['name'][$key], "text");
						$insert_tipo = GetSQLValueString('1', "int");
						$insert_descrip = GetSQLValueString($descrip_cort_val_1, "text");
						$insert_date = GetSQLValueString($fecha_act, "date");
						
						$insert_id = GetSQLValueString($id_val_1, "int");
						
						/*$tamano_img = explode('.',$_FILES['archivo']['name'][$key]);
						$cantidad_archv = count($tamano_img);
						$extension = $tamano_img[--$cantidad_archv];*/
						
						//movemos el archivo
						$archivo_nuevo = date('YmdHis');
						$add_dir = 'archivos';
						$insert_archiv = $archivo_nuevo.'_'.$key.'.'.$extension;
						$add = $add_dir."/".$insert_archiv;
						
						$mover_archivos = (move_uploaded_file($valor, $add));
						chmod("$add",0777);
						
						if($mover_archivos) {
							$campos_img = 'tipo_archivos, categoria_archivos, id_tipo_archivos, nombre_archivos, archivo_archivos, extension_archivos, fecha_archivos';
							$valores_img = "'tinymce', $insert_categoria, $insert_id, $insert_nombre_orig, '$insert_archiv', '$extension', $insert_date";
				
							if(InsertarInfo('archivos', $campos_img, $valores_img, $sql_db, $conexion_gal)) $mensaje_err = $mensaje_glob_v[11];
							else{
								$se_movio = TRUE;
							}
						}
						else $mensaje_err = $mensaje_glob_v[34];
					}
				}
				if($se_movio == TRUE) {
					if(!empty($cat)) {
						//$seccion = 1;
						//$id = $cat;
						header('Location: filebrowser.php?seccion=1&id='.$cat);
					}
					else unset($seccion);
				}
			}	
		}
		break;
		//borrar imagen
		case 4:{
			$mensaje_err .= $mensaje_glob_v[validarReq($id_val_1, 11)];
			if(empty($mensaje_err)) {
				$insert_id = GetSQLValueString($id_val_1, "int");
				
				$query_rs_archivos = "SELECT * FROM archivos WHERE id_archivos = $insert_id";
				$rs_archivos = mysql_query($query_rs_archivos, $conexion_gal) or die('no se pudo conectar a la base de datos');
				$row_rs_archivos = mysql_fetch_assoc($rs_archivos);
				$totalRows_rs_archivos = mysql_num_rows($rs_archivos);
				
				if($totalRows_rs_archivos > 0){
					if(!BorrarArchivo($insert_id, $sql_db, $conexion_gal, $ruta.'archivos/')) {
						$mensaje_err = $mensaje_glob_v[11];
					}
					else {
					}
				}
			}
		}
		break;
		default:{}
	}
}
?>