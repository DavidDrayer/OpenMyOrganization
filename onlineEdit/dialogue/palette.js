

		function popupModeless (url,tailleX,tailleY, posX,posY, mainWindow) {
			if (mainWindow==null) {mainWindow=document;}
					return window.showModelessDialog(url, mainWindow,'dialogLeft:'+posX+'px; dialogTop:'+posY+'px; dialogHeight:' + tailleY + 'px; help:no; dialogWidth:' + tailleX + 'px; center:true; edge:raised; scroll:no; status:no');
			}
			
		function popupModal (url,tailleX,tailleY) {
			
				if (!(window.dialogArguments.parentWindow==null)) {
					window.dialogArguments.parentWindow.showModalDialog(url, window.dialogArguments,'dialogHeight:' + tailleY + 'px; dialogWidth:' + tailleX + 'px; center:true; edge:raised; scroll:no; status:no');
				} else {
					window.showModalDialog(url, window.document,'dialogHeight:' + tailleY + 'px; dialogWidth:' + tailleX + 'px; center:true; edge:raised; scroll:no; status:no');
				}
		}
		

	
		function checkSelection() {
			   	if (getSelectionHtml()=="") {alert ('Sélectionner une zone de texte 2 ou une image'); return false;} 
				return true;
    		}
    		
		function getSelection() {
				return getSelectionHtml();
    		}
		function checkActive() {
				if (window.dialogArguments.all('editeur').style.display=='none') {alert ('Sélectionnez une zone à éditer'); return false;}
				window.dialogArguments.all('edition').focus();
				return true;
		}
		function insertHtml(html) {
   				window.dialogArguments.all('edition').focus(); 
   				var sel = window.dialogArguments.selection.createRange(); 
			   	if (window.dialogArguments.selection.type == 'Control') { 
    				return; 
   				} 
  				sel.pasteHTML(html); 
		}
		function applyStyle(format) {
				window.dialogArguments.execCommand('FormatBlock', false, format);
		}

