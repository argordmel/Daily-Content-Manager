Array.prototype.inArray = function(valor) { var i; for (i = 0; i < this.length; i++) { if (this[i] === valor) return true;}return false;};
var elementosErr= []; etiquetas	= ["input", "select", "textarea", "button"];
validaciones	= ["requerido", "alfabetico", "numerico", "alfanumerico", "texto", "slug", "fecha", "email", "lista", "usuario", "pass", "fotografia"];


function validarForm(formulario) {
    
    var elemento= window.document.forms[formulario].elements;
    var enviar	= true;
    var cont = 0;
    var longitud= elemento.length;
    var input;

    for (i = 0; i < longitud; i++) {
        if (etiquetas.inArray(elemento[i].tagName.toLowerCase())) {
            var clases = extraerClases(elemento[i]);
            if (clases != "" && clases.length != 0) {
                for (c = 0; c < clases.length; c++) {
                    if (validaciones.inArray(clases[c])) {
                        if (!eval(clases[c] + '(elemento[i].value,"err_" + elemento[i].id)')) {
                            elementosErr.push("err_" + elemento[i].id);
                            if(cont == 0) {
                                input = elemento[i];
                            }
                            cont++;
			}
                    }
		}
            }
        }
    }

    if (cont > 0) {
        enviar = false;

        try {
            $('#error-form').dialog('open');
        } catch(e) {
            alert('Se han encontrado errores al procesar el formulario. Por favor verifica los datos e intenta nuevamente.');
        }
        try { limpiarClaves() } catch(e) { }
        setTimeout(function(){ $("#"+input.id).focus(); }, 2500);
        return false;

    }
    return enviar;
}

function extraerClases(elemento) { var clases = elemento.className; var listaClases = clases.split(" "); return listaClases; }

function requerido(valor, idEtiqueta) { if (valor == null || valor.length == 0 || /^\s+$/.test(valor) ) { document.getElementById(idEtiqueta).innerHTML = 'Campo requerido'; return false; }else { document.getElementById(idEtiqueta).innerHTML = '&nbsp;';return true;}}

function alfabetico(valor, idEtiqueta) {
    if (!(valor == null || valor.length == 0 || /^\s+$/.test(valor))) {
        if (!(/^[a-zA-ZüñÑáéíóúÁÉÍÓÚÜ\s]+$/.test(valor))) {
            document.getElementById(idEtiqueta).innerHTML = 'Introduzca solo valores alfabéticos';
            return false;
        }
        else {
            document.getElementById(idEtiqueta).innerHTML = '&nbsp;';
            return true;
        }
    } else { return true; }
}

function numerico(valor, idEtiqueta) {
    if (!(valor == null || valor.length == 0 || /^\s+$/.test(valor))) {
         if (! (/^[-]?\d+(\.\d+)?$/.test(valor)) ) {
            document.getElementById(idEtiqueta).innerHTML = 'Introduzca solo valores numéricos';
            return false;
	}
	else {
            document.getElementById(idEtiqueta).innerHTML = '&nbsp;';
            return true;
	}
    } else { return true; }
}

function alfanumerico(valor, idEtiqueta) {
    if (!(valor == null || valor.length == 0 || /^\s+$/.test(valor))) {
        if (!(/^[a-zA-Z0-9-ZüñÑáéíóúÁÉÍÓÚÜ._\s]+$/.test(valor))) {
            document.getElementById(idEtiqueta).innerHTML = 'Introduzca solo valores alfanuméricos';
            return false;
        }
        else {
            document.getElementById(idEtiqueta).innerHTML = '&nbsp;';
            return true;
        }
    } else { return true; }
}

function texto(valor, idEtiqueta) {
    if (!(valor == null || valor.length == 0 || /^\s+$/.test(valor))) {
	if (!(/^[a-zA-Z0-9-ZüñÑáéíóúÁÉÍÓÚÜ#.,-_#$\s]+$/.test(valor))) {
            document.getElementById(idEtiqueta).innerHTML = 'Haz introducido un caracter no válido';
            return false;
	}
	else {
            document.getElementById(idEtiqueta).innerHTML = '&nbsp;';
            return true;
	}
    } else {        
        return true;
    }
}

function slug(valor, idEtiqueta) {
    return texto(valor,idEtiqueta);
}

function fecha(valor, idEtiqueta) {
    if (!(valor == null || valor.length == 0 || /^\s+$/.test(valor))) {
       if ( !(/^[-]?\d+(\.\d+)?$/.test(valor)) ){
            document.getElementById(idEtiqueta).innerHTML = '&nbsp;';
            return true;
	}
	else {
            document.getElementById(idEtiqueta).innerHTML = 'Fecha incorrecta';
            return false;
	}
    } else { return true; }
}

function email(valor, idEtiqueta) {
    if (!(valor == null || valor.length == 0 || /^\s+$/.test(valor))) {
        if (!(/^([a-zA-Z0-9_\.\-])+(\+[a-zA-Z0-9]+)*\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/.test(valor))) {
            document.getElementById(idEtiqueta).innerHTML = 'El formato de la dirección no es válido';
            return false;
        }
	else {
            document.getElementById(idEtiqueta).innerHTML = '&nbsp;';
            return true;
	}
    } else { return true; }
}

function lista(valor, idEtiqueta) {
    if (valor == '') {
        document.getElementById(idEtiqueta).innerHTML = 'Seleccione una opción de la lista';
	return false;
    }
    else {
        document.getElementById(idEtiqueta).innerHTML = '&nbsp;';
        return true;
    }
}

function usuario(valor, idEtiqueta) {
    var limiteMenor = 4;
    var limiteMayor = 10;
    if (!(valor == null || valor.length == 0 || /^\s+$/.test(valor))) {
        if ( alfanumerico(valor,idEtiqueta) ){
            if ((valor.length >= limiteMenor) && (valor.length <= limiteMayor)) {
                document.getElementById(idEtiqueta).innerHTML = '&nbsp;';
		return true;
            }
            else {
                document.getElementById(idEtiqueta).innerHTML = 'El usuario debe tener entre 4 o 10 caracteres';
		return false;
            }
	}
	else {
            document.getElementById(idEtiqueta).innerHTML = 'Haz introducido un caracter no válido';
            return false;
	}
    } else { return true; }
}

function pass(valor, idEtiqueta) {
    var limite = 8;
    if (!(valor == null || valor.length == 0 || /^\s+$/.test(valor))) {
        if ( alfanumerico(valor,idEtiqueta) ){
            if ((valor.length >= limite) ) {
                document.getElementById(idEtiqueta).innerHTML = '&nbsp;';
		return true;
            }
            else {
                document.getElementById(idEtiqueta).innerHTML = 'La contraseña debe tener entre mínimo 8 caracteres';
		return false;
            }
	}
	else {
            document.getElementById(idEtiqueta).innerHTML = 'Haz introducido un caracter no válido';
            return false;
	}
    } else { return true; }

}

/*function repass(valor,  idEtiqueta) {
    var limite = 8;
    if (valor == ) {
        if ( alfanumerico(valor,idEtiqueta) ){
            if ((valor.length >= limite) ) {
                document.getElementById(idEtiqueta).innerHTML = '&nbsp;';
        return true;
            }
            else {
                document.getElementById(idEtiqueta).innerHTML = 'La contraseña debe tener entre mínimo 8 caracteres';
        return false;
            }
    }
    else {
            document.getElementById(idEtiqueta).innerHTML = 'Haz introducido un caracter no válido';
            return false;
    }
    } else { return true; }

}*/

function fotografia(file, idEtiqueta) {
    var ext;
    if (!(file == null || file.length == 0 || /^\s+$/.test(file))) {
        ext = getFileExtension(file);
        if(ext != "jpeg" && ext != "jpg" && ext != "png" && ext != "gif") {
            document.getElementById(idEtiqueta).innerHTML = 'Formato de imagen no válido.';
            return false;
        }
        else {
            document.getElementById(idEtiqueta).innerHTML = '&nbsp;';
            return true;
	}
    } else { return true; }
}

function getFileExtension(filename) {
    var i = filename.lastIndexOf(".");
    return (i > -1) ? filename.substring(i + 1, filename.length).toLowerCase() : "";
}

function limpiar_err() { var total = elementosErr.length;for (var i = 0; i < total; i++)document.getElementById(elementosErr.shift()).innerHTML = '&nbsp;';}
