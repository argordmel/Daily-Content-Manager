var server = location.hostname;
var path = location.pathname;
var dir = path.split("dc-admin");
jQuery(function($){
	$.datepicker.myscript= {
            showOn: 'both',
            autoSize: false,
            buttonImage: 'http://'+server+dir[0]+'img/admin/calendario.png',
            buttonImageOnly: true,
            closeText: 'Cerrar',
            prevText: '&#x3c;Ant',
            nextText: 'Sig&#x3e;',
            currentText: 'Hoy',
            monthNames: ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
            monthNamesShort: ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'],
            dayNames: ['Domingo','Lunes','Martes','Mi&eacute;rcoles','Jueves','Viernes','S&aacute;bado'],
            dayNamesShort: ['Dom','Lun','Mar','Mie;','Juv','Vie','Sab'],
            dayNamesMin: ['D','L','M','M','J','V','S'],
            dateFormat: 'yy-mm-dd',
            firstDay: 1,
            isRTL: false,
            changeMonth: true,
            changeYear: true,
            buttonText: 'Calendario',
            showButtonPanel: true
        };
	$.datepicker.setDefaults($.datepicker.myscript);
});




