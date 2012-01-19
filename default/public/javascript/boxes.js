boxes = ["actualmente","borradores","comentarios","quickpress"];
boxes_tam = boxes.length;
$(document).ready(function(){
    $('.box-titulo').click(function(){
        box_expandido = true;
	este = $(this);
	identificador = este.parent('.box').attr('id');
	clases = este.attr('class').split(' ');
        for(c = 0; c < clases.length; c++) { 
		if(clases[c] == 'box-colapsado') { 
			box_expandido = false; 
			break;
		}
	}
        box_contenido = este.parent('.box').find('div.box-contenido');
	box_icono = este.parent('.box').find('span.box-icono');
        if(box_expandido) { 
		$.cookie(identificador,'colapsado', { expires: 365 });
		box_contenido.slideToggle();
		ocultar_box(este,box_icono,'');
	} else {
		$.cookie(identificador,'expandido', { expires: 365 });
		box_contenido.slideDown();mostrar_box(este,box_icono,'');
	}
    });
});
for(b = 0; b < boxes_tam; b++) {
	box_search = $.cookie(boxes[b]);
	if(box_search){
		box_titulo = $('#'+boxes[b]+'-titulo');
		box_icono = $('#'+boxes[b]).find('span.box-icono');
		box_contenido = $('#'+boxes[b]).find('div.box-contenido');
		if(box_search == 'colapsado') {
			ocultar_box(box_titulo, box_icono, box_contenido);
		} else {
			mostrar_box(box_titulo, box_icono, box_contenido);
		}
	}
}
function mostrar_box(titulo,icono,contenido) {
	titulo.removeClass('box-colapsado').addClass('box-expandido');
	icono.removeClass('ui-icon-circle-triangle-e').addClass('ui-icon-circle-triangle-s');
	if(contenido != '') {
		contenido.show();
	}
}
function ocultar_box(titulo,icono,contenido) {
	titulo.removeClass('box-expandido').addClass('box-colapsado');
	icono.removeClass('ui-icon-circle-triangle-s').addClass('ui-icon-circle-triangle-e');
	if(contenido !='') {contenido.hide();
	}
}
