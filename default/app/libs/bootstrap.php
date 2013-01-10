<?php
// Bootstrap de la aplicacion

// Establezco el nombre de la aplicacion
define('APP_NAME', $config['application']['name']);

/*Cargo las opciones generales de la aplicacion*/
//Defino el nombre del blog
define('NOMBRE_DEL_BLOG', $config['application']['nombre_blog']);
//Defino el numero de post a mostrar por página
define('POST_POR_PAGINA', $config['application']['post_por_pagina']);
//Defino el numero de post a mostrar en el widget
define('POST_POR_WIDGET', $config['application']['post_por_widget']);


//Defino la categoría por defecto
define('CATEGORIA_POR_DEFECTO', $config['application']['categoria_por_defecto']);
//Defino si se habilitan o no comentarios por defecto
define('HABILITAR_COMENTARIOS', $config['application']['habilitar_comentarios']);