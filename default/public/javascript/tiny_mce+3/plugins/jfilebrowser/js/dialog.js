/**
 
 * $Id: jFileBrowser, 2010.
 * @author Juaniquillo
 * @copyright Copyright © 2010, Victor Sanchez (Juaniquillo).
 * @email juaniquillo@gmail.com
 * @website http://juaniquillo.com

 */

tinyMCEPopup.requireLangPack();

var jFileBrowserDialog = {
	init : function() {
		var f = document.forms[0];

		// Get the selected contents as text and place it in the input
		//f.someval.value = tinyMCEPopup.editor.selection.getContent({format : 'text'});
		//f.somearg.value = tinyMCEPopup.getWindowArg('some_custom_arg');
	},

	insert : function(r, t, n) {
		// Insert the contents from the input into the document
		var html;
		if(t == 1){
			html = '<img src="' + r +'" title="' + n +'" alt="' + n +'" />'; 
		}
		else {
			html = '<a href="' + r +'" title="' + n +'" >' + n +'</a>'; 
		}
		
		tinyMCEPopup.editor.execCommand('mceInsertContent', false, html);
		tinyMCEPopup.close();
	},
	
	confirmar : function(m, d) {
		var f = d - 1;
		tinyMCEPopup.confirm(m, function(s) {
			if(s){
				document.forms[f].submit();
			}
			else {
				return false;
			}
		});
	}
};

tinyMCEPopup.onInit.add(jFileBrowserDialog.init, jFileBrowserDialog);
