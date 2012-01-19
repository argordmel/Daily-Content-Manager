<?php 

/**
 
 * $Id: jFileBrowser, 2010.
 * @author Juaniquillo
 * @copyright Copyright © 2010, Victor Sanchez (Juaniquillo).
 * @email juaniquillo@gmail.com
 * @website http://juaniquillo.com

 */

//variables
$seccion = $_GET['seccion'];
$id = $_GET['id'];
$cat = $_GET['cat'];
$set_cook = $_GET['set_c'];
$busqueda = $_GET['busqueda'];
$fecha_act = date('Y-m-d H:i:s');

///////////ruta donde estan los archivos
$ruta_completa_ar = str_replace('filebrowser.php', '', $_SERVER['PHP_SELF']).'archivos/';

$js_opt['admin_area'] = TRUE;

// cookie para las vistas de los archivos
$cookie_vbrw = $_COOKIE['jfilebrowser'];

if(!isset($cookie_vbrw) || $set_cook == 1) {
	//setear el cookie
	setcookie('jfilebrowser','1', time()+(60*60*24*365));
	//refrescar la pagina
	if(isset($_GET['busqueda'])) header('Location: filebrowser.php?seccion=1&busqueda='.$busqueda);
	elseif(isset($_GET['id'])) header('Location: filebrowser.php?seccion=1&id='.$id);
}
elseif($set_cook == 2) {
	//setear el cookie
	setcookie('jfilebrowser','2', time()+(60*60*24*365));
	//refrescar la pagina
	if(isset($_GET['busqueda'])) header('Location: filebrowser.php?seccion=1&busqueda='.$busqueda);
	elseif(isset($_GET['id']))  header('Location: filebrowser.php?seccion=1&id='.$id);
}

include("include/config.inc.php");

//incluir la pagina de validacion solo cuando es necesario
if(isset($_POST['validacion']) && !empty($_POST['validacion'])) {
	include('include/validacion.inc.php');
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>{#jfilebrowser_dlg.title}</title>
<link rel="stylesheet" type="text/css" href="css/style1.css">
<script type="text/javascript" src="../../tiny_mce_popup.js"></script>
<script type="text/javascript" src="js/dialog.js"></script>
</head>
<body>
<table width="100%" border="0" cellpadding="0" cellspacing="0" class="container_tb">
  <tr>
    <td class="col_1_td" valign="top"><table width="100%" border="0" cellpadding="0" cellspacing="0" class="menu_tb">
        <tr>
          <td><a href="filebrowser.php" title="Directorios"><img src="img/1244128462_folder_images.png" alt="" width="48" height="48" /></a></td>
        </tr>
        <tr>
          <td><a href="filebrowser.php?seccion=2" title="Subir archivos"><img src="img/1246627152_Stock Index Up.png" alt="" width="48" height="48" /></a></td>
        </tr>
      </table></td>
    <td valign="top"><div class="container_contenid_div">
        <form id="buscar_fm" method="get" action="" name="buscar_fm">
          <input name="busqueda" type="text" value="<?php echo $busqueda ?>" />
          <input type="submit" name="button" id="button" value="buscar" />
          <input name="seccion" type="hidden" id="seccion" value="1" />
        </form>
        <h2 class="titulo1">File Browser</h2>
        <div class="bredcrum">        </div>
		<?php if(isset($mensaje_err) && !empty($mensaje_err)) { ?>
        <div class="mensaje_error2">
          <ul>
            <?php echo $mensaje_err; ?>
          </ul>
        </div>
        <?php } 
		 ///////////////////////// Switch para las secciones
		 switch($seccion){
		 //imagenes individuales
		 case 1:{
			///////Archivos
			//Si hya una busqueda
			if(isset($_GET['busqueda'])) {
				if(empty($busqueda)) $archiv_bus = -1;
				elseif(isset($busqueda)) {
					$archiv_bus = (get_magic_quotes_gpc()) ? $busqueda : addslashes($busqueda);
				}
				else $archiv_bus = -1;
				$query_img  = "SELECT * FROM archivos WHERE nombre_archivos LIKE '%$archiv_bus%' ORDER BY fecha_archivos DESC";
				$busq_act = true;
			}
			//Mostrar todos
			else {
				if (isset($id)) {
					$id_cat = (get_magic_quotes_gpc()) ? $id : addslashes($id);
				}
				else $id_cat = -1;
				$query_img  = "SELECT * FROM archivos WHERE categoria_archivos = $id_cat ORDER BY fecha_archivos DESC";
			}
			
			//configuración de la paginacion
			$paging = new PHPPaging;
			$paging->agregarConsulta($query_img);  
			if($cookie_vbrw == 1)$paging->porPagina(12);
			else $paging->porPagina(10);
			
			$paging->ejecutar(); 
			$num_paginas = $paging->numTotalPaginas();
			$total_registros = $paging->numTotalRegistros();
			
			?>
			 <div class="contain_men_cat">
          <ul class="menu_cat">
            <li><a href="filebrowser.php?seccion=1&amp;<?php if($busq_act) echo "busqueda=$busqueda"; else echo "id=$id" ?>&amp;set_c=1" class="thumbnail_a<?php if($cookie_vbrw == 1) echo '_activo' ?>">Thumbnals</a></li>
            <li><a href="filebrowser.php?seccion=1&amp;<?php if($busq_act) echo "busqueda=$busqueda"; else echo "id=$id" ?>&amp;set_c=2" class="lista_a<?php if($cookie_vbrw == 2) echo '_activo' ?>">Lista</a></li>
             <?php if(!$busq_act){ ?>
             <li><a href="filebrowser.php?seccion=2&amp;cat=<?php echo $id_cat ?>" class="subir_img_a" title="Subir imagen en este directorio">Subir</a></li>
             <?php }?>
          </ul>
          <div class="clear"></div>
        </div>
			<?php 
			//enlaces de la navegacion
			if($num_paginas > 1) echo '<div class="paginacion">'.$paging->fetchNavegacion().'</div>';
			$cuenta_form = 2;
			//si esxisten achivos
			if($total_registros > 0){?>
			<ul class="imag_list_ul<?php if($cookie_vbrw == 2) echo '_lista' ?>">
			<?php 
			//loop de la navegacion
			while($result_categorias_img = $paging->fetchResultado()){ 
				//swith para las clases de los tipo de archivos
				switch($result_categorias_img['extension_archivos']){
					case 'jpg':
					case 'jpeg':
					case 'gif':
					case 'png':
						$mime_t = 'imagen';
					break;
					case 'pdf':
						$mime_t = 'pdf2';
					break;
					case 'doc':
					case 'docx':
						$mime_t = 'doc2';
					break;
					case 'xls':
					case 'xlsx':
						$mime_t = 'xls2';
					break;
					case 'ppt':
					case 'pptx':
						$mime_t = 'ppt2';
					break;
					default: $mime_t = 'gen2';
			   }?>
          <?php 
		  //si la vista es thumnails
		  if($cookie_vbrw == 1){?>
		  <li>
		  <?php if($mime_t == 'imagen') {?>
          <a href="filebrowser.php?seccion=4&amp;id=<?php echo $result_categorias_img['id_archivos'] ?>" title="<?php echo $result_categorias_img['nombre_archivos'] ?>"><img src="img.php?file=<?php echo $ruta ?>archivos/<?php echo $result_categorias_img['archivo_archivos'] ?>&amp;ancho=75&amp;alto=75&amp;cut" width="75" height="75" alt="" /></a>
          <?php } else {?>
          <div class="archivos_list_dv">
          	<a href="filebrowser.php?seccion=4&amp;id=<?php echo $result_categorias_img['id_archivos'] ?>" title="<?php echo $result_categorias_img['nombre_archivos'] ?>"><img src="img/<?php echo $mime_t ?>.png" alt=""  width="75" height="75" /></a>            </div>
          <?php } ?>
          <form id="form_submit" method="post" action="" class="centrar_2" onsubmit="jFileBrowserDialog.confirmar('estas seguro que quieres borrar esta imagen', <?php echo $cuenta_form ?>);return false">
            <input type="image" name="borrar_cat_bt" id="borrar_cat_bt" src="img/delete.png" />
              <input name="validacion" type="hidden" id="validacion2" value="4" />
              <input name="id" type="hidden" id="id" value="<?php echo $result_categorias_img['id_archivos'] ?>" />
          </form>
          </li>
          <?php } 
		  //si la vista es lista
		  elseif($cookie_vbrw == 2){?>
		  <li><div>
          <form id="form_submit<?php if($cookie_vbrw == 2) echo '_lista' ?>" method="post" action="" class="centrar_2" onsubmit="jFileBrowserDialog.confirmar('estas seguro que quieres borrar esta imagen', <?php echo $cuenta_form ?>);return false">
            <input type="image" name="borrar_cat_bt" id="borrar_cat_bt" src="img/delete.png" />
              <input name="validacion" type="hidden" id="validacion2" value="4" />
              <input name="id" type="hidden" id="id" value="<?php echo $result_categorias_img['id_archivos'] ?>" />
          </form>
          <a href="filebrowser.php?seccion=4&amp;id=<?php echo $result_categorias_img['id_archivos'] ?>" title="<?php echo $result_categorias_img['nombre_archivos'] ?>" class="archivos_mime_<?php echo $mime_t ?>"><?php echo $result_categorias_img['nombre_archivos'] ?></a>          </div></li>
		  <?php }
		  ?>
          
        <?php ++$cuenta_form; }?>
		</ul>
		<?php }
		else echo 'no se encontr&oacute; ning&uacute;n archivo';
		 }
		 break;
		 //subir imagen
		 case 2:{?>
        <h3 class="titulo2">Subir archivo</h3>
        
        <form action="" method="post" enctype="multipart/form-data" name="form1">
          <div class="centrar">Archivos permitidos: <strong>jpg, gif,  png, PDF, Word, PowerPoint y Excel</strong></div>
          <table border="0" cellspacing="0" cellpadding="0" class="princip">
            <tr>
              <td valign="top" class="log_in_label">archivo<span class="form_requerido">*</span></td>
              <td class="log_in_field"><input name="archivo[]" type="file" class="multi" maxlength="5" id="subir_imagen_fl"/></td>
            </tr>
            <tr>
              <td valign="top" class="log_in_label">directorio*</td>
              <td class="log_in_field"><?php 
	$query_rs_producto2 = "SELECT * FROM categorias WHERE tipo_cat = 5 AND status_cat = 1 ORDER BY name_cat";
	$rs_producto2 = mysql_query($query_rs_producto2, $conexion_gal) or die(mysql_error());
	//$row_rs_producto2 = mysql_fetch_assoc($rs_producto2);
	$totalRows_rs_producto2 = mysql_num_rows($rs_producto2);
	  
	  if($totalRows_rs_producto2 < 1) {?>
                Favor de <a href="filebrowser.php?seccion=3">crear un directorio</a> antes de subir un archivo
                <?php } else { ?>
                <select name="categoria" id="categoria">
                  <option value="-1">Seleccione directorio</option>
                  <?php while($row_rs_producto2 = mysql_fetch_assoc($rs_producto2)){?>
                  <option value="<?php echo $row_rs_producto2['cat_id_cat'] ?>" <?php if($cat == $row_rs_producto2['cat_id_cat']) echo 'selected="selected"'?>><?php echo $row_rs_producto2['name_cat'] ?></option>
                  <?php } ?>
                </select>
                <?php } ?>              </td>
            </tr>
            <tr>
              <td colspan="2" class="enviar_td"><input name="enviar" type="submit" id="enviar" value="Enviar" onclick="this.value='Esperar...';this.disable = true;this.style.color = '#9d9b93';"/>
                <span id="loading_imag_sp"></span>
                <!--<input name="categoria" type="hidden" id="categoria" value="1" />-->
                <input name="categoria2" type="hidden" id="categoria2" value="<?php echo $cat ?>" />
              <input name="validacion" type="hidden" value="3" /></td>
            </tr>
          </table>
        </form>
        <?php }
		 break;
		 //agregar categorias
		 case 3:{?>
        <h3 class="titulo2">Crear directorio</h3>
        <form action="" method="post" enctype="multipart/form-data" name="form12">
          <table border="0" cellspacing="0" cellpadding="0" class="princip">
            <tr>
              <td valign="top" class="log_in_label">Directorio</td>
              <td class="log_in_field"><input name="nombre" type="text" id="nombre" value="<?php echo((isset($_POST["nombre"]))?stripslashes($_POST["nombre"]):$row_rs_categoria2['name_cat']) ?>" size="35" />              </td>
            </tr>
            <tr>
              <td colspan="2" class="enviar_td"><input name="enviar3" type="submit" id="enviar3" value="Enviar" />
                <input name="validacion" type="hidden" id="validacion" value="1" />
                <input name="tipo_cat" type="hidden" id="tipo_cat" value="5" /></td>
            </tr>
          </table>
        </form>
        <?php }
		 break;
		 //insertar imagenes en tinyMCE
		 case 4:{
		 if (isset($id)) {
			 $id_cat = (get_magic_quotes_gpc()) ? $id : addslashes($id);
		 }
		 else $id_cat = -1;
		 $id_cat;
		 $query_img = SeleccionarInfo('archivos', '*', $sql_db, $conexion_gal, TRUE, "id_archivos = $id_cat");
		 $num_img = NumerodeCampos($query_img);
		 $result_categorias_img = ResultadoArrayAssoc($query_img);
		 //swith para las clases de los tipo de archivos
		 switch($result_categorias_img['extension_archivos']){
			case 'jpg':
			case 'jpeg':
			case 'gif':
			case 'png':
				$mime_t = 'imagen';
			break;
			case 'pdf':
				$mime_t = 'pdf2';
			break;
			case 'doc':
			case 'docx':
				$mime_t = 'doc2';
			break;
			case 'xls':
			case 'xlsx':
				$mime_t = 'xls2';
			break;
			case 'ppt':
			case 'pptx':
				$mime_t = 'ppt2';
			break;
			default: $mime_t = 'gen2';
	   		}
		
		 ///////////ruta donde estan los archivos
		 $ruta_completa_ar = $ruta_completa_ar.$result_categorias_img['archivo_archivos'];
		
		 ///////////tipos de archivos
		 //si es imagen
		 if($mime_t == 'imagen') $tipo_arch = 1;
		 //si es archivo
		 else $tipo_arch = 2;
		 $nombre_ar = $result_categorias_img['nombre_archivos'];
		?>
		   <div class="archiv_indiv_dv">
           <a href="<?php echo $ruta_completa_ar ?>" title="<?php echo $result_categorias_img['nombre_archivos'] ?>" target="_blank">
		  <?php if($mime_t == 'imagen') {?>
          <img src="img.php?file=<?php echo $ruta ?>archivos/<?php echo $result_categorias_img['archivo_archivos'] ?>&amp;ancho=350&amp;alto=255" alt="<?php echo $result_categorias_img['nombre_archivos'] ?>" />
          <?php } else {?>
          	<img src="img/<?php echo $mime_t ?>.png" alt="" />
          <?php } ?>
          </a>
         <div class="centrar"><?php echo $result_categorias_img['nombre_archivos'] ?></div>
        </div>
	    <form class="centrar" method="post" action="" onsubmit="jFileBrowserDialog.insert('<?php echo $ruta_completa_ar ?>', <?php echo $tipo_arch ?>, '<?php echo $nombre_ar; ?>');return false;">
	      <input type="submit" name="insertar" id="insertar" value="Insertar" />
                    </form>
	     <?php }
		 break;
		 case 5:{
		 if (isset($busqueda)) {
			 $archiv_bus = (get_magic_quotes_gpc()) ? $busqueda : addslashes($busqueda);
		 }
		 else $archiv_bus = -1;
		 $id_cat;
		 $query_img = SeleccionarInfo('archivos', '*', $sql_db, $conexion_gal, TRUE, "nombre_archivos LIKE '%$archiv_bus%'");
		 $num_img = NumerodeCampos($query_img);
		 $result_categorias_img = ResultadoArrayAssoc($query_img);
		 ?>
		 
		 <?php }
		 break;
		 //directorios
		 default:{
			
			$query_cat  = "SELECT * FROM categorias WHERE tipo_cat = 5 ORDER BY name_cat";
			
			$paging = new PHPPaging;
			$paging->agregarConsulta($query_cat);  
			$paging->porPagina(15);
			
			$paging->ejecutar(); 
			$num_paginas = $paging->numTotalPaginas();
			$total_registros = $paging->numTotalRegistros();
		  
		  ?>
        <div class="contain_men_cat">
          <ul class="menu_cat">
            <li><a href="filebrowser.php?seccion=3" class="crear_cat_a" title="Crear directorio">crear directorio</a></li>
          </ul>
          <div class="clear"></div>
        </div>
        <?php  if($total_registros > 0){
		if($num_paginas > 1) echo '<div class="paginacion">'.$paging->fetchNavegacion().'</div>';
		?>
        <ul class="lista_cat">
          <?php 
		  $cuenta_form = 2;
		  while($result_categorias_img = $paging->fetchResultado()){
			$id_cat = $result_categorias_img['cat_id_cat'];
			//imagenes
			$query_img = SeleccionarInfo('archivos', '*', $sql_db, $conexion_gal, TRUE, "categoria_archivos = $id_cat");
			$num_img = NumerodeCampos($query_img);
			
			?>
          <li>
            <form id="borr_cat_fm" method="post" action="" onsubmit="jFileBrowserDialog.confirmar('estas seguro que quieres borrar este directorio', <?php echo $cuenta_form ?>);return false">
             <input type="image" name="borrar_cat_bt" id="borrar_cat_bt" src="img/delete.png" />
              <span class="enviar_td">
              <input name="validacion" type="hidden" id="validacion" value="2" />
              <input name="id" type="hidden" id="id" value="<?php echo $id_cat ?>" />
              </span>
            </form>
              <span class="info_cat_sp"><a href="filebrowser.php?seccion=1&amp;id=<?php echo $id_cat ?>"<?php if($num_img > 0) echo ' class="cat_con_img"';?>><?php echo $result_categorias_img['name_cat'] ?></a> (<?php echo $num_img ?>)</span>
          </li>
          <?php ++$cuenta_form;}?>
        </ul>
        <?php }else {?>
        No existe ning&uacute;n directorio
        <?php }
	}
}?>
      </div></td>
  </tr>
</table>
<form onsubmit="jFileBrowserDialog.insert();return false;" action="#">
  <div class="mceActionPanel">
    <div style="float: left">
    </div>
    <div style="float: right">
      <input type="button" id="cancel" name="cancel" value="{#cancel}" onclick="tinyMCEPopup.close();" />
    </div>
  </div>
</form>
</body>
</html>
