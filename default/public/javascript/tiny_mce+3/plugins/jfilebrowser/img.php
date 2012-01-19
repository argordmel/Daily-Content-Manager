<?php

/**
 * @author Myokram
 * @copyright 2007

 
 * @edited Juaniquillo
 * @copyright Copyright © 2010, Victor Sanchez (Juaniquillo).
 * @email juaniquillo@gmail.com
 * @website http://juaniquillo.com

*/
 



if(isset($_GET['source'])) {
    highlight_file(__FILE__);
    exit;
}

$imagen = $_GET['file'];

$tama_imag = getimagesize($imagen);
$ancho_img = $tama_imag[0];
$alto_img = $tama_imag[1];

//echo $ancho_img.' - '.$alto_img;exit;

include_once "include/PHPImagen.lib.php"; 

// Instanciamos la clase
$imagen = new Imagen($imagen); 

// Redimension de la imagen. Los parámetros los 
// recibimos de la URL. Por motivos de seguridad,
// Los tamaños máximos permitidos son de 500x500 px.
$nuevo_ancho = ($_GET['ancho'] <= $ancho_img) ? $_GET['ancho'] : null; 
$nuevo_alto = ($_GET['alto'] <= $alto_img) ? $_GET['alto'] : null;
$cut = (isset($_GET['cut'])) ? true : false; 
$imagen->resize($nuevo_ancho, $nuevo_alto, $cut);

//Por la URL recibiremos el parámetro download 
if(isset($_GET['download'])) 
    $imagen->doDownload(); 
else 
    $imagen->doPrint(); 


?> 