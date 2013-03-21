<?php
// Bootstrap de la aplicacion para personalizarlo
// Para cargar cambia en public/index.php el require del bootstrap a app

// Arranca KumbiaPHP
require_once CORE_PATH . 'kumbia/bootstrap.php';

// Configuración Inicial de CMS
$config = Load::model('configuracion')->getOpcion();

// Establezco el nombre de la aplicacion
define('APP_NAME', $config['titulo']);
// Defino el favicon
define('FAVICON', $config['favicon']);

/*Cargo las opciones generales de la aplicacion*/

// Defino el nombre del blog
define('NOMBRE_DEL_BLOG', $config['nombre_blog']);
// Defino el numero de post a mostrar por página
define('POST_POR_PAGINA', $config['post_por_pagina']);
// Defino el numero de post a mostrar en el widget
define('POST_POR_WIDGET', $config['post_por_widget']);


// Defino la categoría por defecto
define('CATEGORIA_POR_DEFECTO', $config['categoria_por_defecto']);
// Defino si se habilitan o no comentarios por defecto
define('HABILITAR_COMENTARIOS', $config['habilitar_comentarios']);

// Ejecuta el request
try {
    // Dispatch y renderiza la vista
    View::render(Router::execute($url), $url);
} catch (KumbiaException $e) {
    KumbiaException::handle_exception($e);
}

// Fin del request
exit();