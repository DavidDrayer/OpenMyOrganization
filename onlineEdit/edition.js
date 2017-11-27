// JavaScript Document
function ctrlTouche(event) {
	var holder;
     //IE uses this
     if(window.event){
            holder=window.event.keyCode;
     }
     //FF uses this
     else{
            holder=event.which;
     } 

	if (holder==8364 || holder==5) {
		countE+=1;
		if (countE>1) {
			loadEditor();
			return false;
		}
	} else countE=0;
}
function openPopUp(url,name,sizeX,sizeY) {
		toto=window.open(url,name,"width="+sizeX+", height="+sizeY+", scrollbars=yes");
		toto.focus();
}

function utf8_decode ( str_data ) {  
  
    var tmp_arr = [], i = ac = c1 = c2 = c3 = 0;  
  
    str_data += '';  
  
    while ( i < str_data.length ) {  
        c1 = str_data.charCodeAt(i);  
        if (c1 < 128) {  
            tmp_arr[ac++] = String.fromCharCode(c1);  
            i++;  
        } else if ((c1 > 191) && (c1 < 224)) {  
            c2 = str_data.charCodeAt(i+1);  
            tmp_arr[ac++] = String.fromCharCode(((c1 & 31) << 6) | (c2 & 63));  
            i += 2;  
        } else {  
            c2 = str_data.charCodeAt(i+1);  
            c3 = str_data.charCodeAt(i+2);  
            tmp_arr[ac++] = String.fromCharCode(((c1 & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));  
            i += 3;  
        }  
    }  
 
    return tmp_arr.join('');  
}  

function getXhr () {
	var xhr;
    try {  xhr = new ActiveXObject('Msxml2.XMLHTTP');   }
    catch (e) 
    {
        try {   xhr = new ActiveXObject('Microsoft.XMLHTTP'); }
        catch (e2) 
        {
           try {  xhr = new XMLHttpRequest();  }
           catch (e3) {  xhr = false;   }
         }
    }
    return xhr;
}

function getTextContent(noeud) {
	if (noeud.textContent) {
		return noeud.textContent;
	} else {
		return noeud.text;
	}
}

function getContent(page, param, dest, mode) {
	
	var xhr=getXhr(); 
	 
	    xhr.onreadystatechange  = function() 
	    { 

	       if(xhr.readyState  == 4)
	       {
	       	    	
	        if(xhr.status  == 200) {
	        	if (mode=="before") {
	        		var elem = document.createElement('div');
	        		var node= document.getElementById(dest);
	        		node.parentNode.insertBefore(elem,node);

				} else {
				
	        		elem=document.getElementById(dest)
	        	}
            	elem.innerHTML=xhr.responseText; 
	            ob=elem.getElementsByTagName("script");
	            for (var i=0;i<ob.length;i++) {
					console.log (getTextContent(ob[i]));
	            	window.eval (getTextContent(ob[i]));
	            }
	
	        } else
	        if(xhr.status  == 401) {
	           alert ("Problème: " + xhr.responseText);
	        }
	        else 
	            document.getElementById("info_canvas").innerHTML="Autre erreur";
	        }
	    }; 

 xhr.open("POST", page, true);


 xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded"); 
xhr.send(param); 
}
	
function loadEditor(user, password) {

		// Nouvelle version
		var param='user='+user+'&password='+password;
		getContent('onlineEdit/editeur2.php',param,'liquidEditor');

}
countE=0;
document.onkeypress=ctrlTouche;
document.write ('<div id="liquidEditor"></div>');
document.write(' <div id="rArrow" title="Drag to modify cell width." style="position:absolute; display:none; cursor: E-resize; z-index: 0" onmousedown="tEdit.markCellResize(this)" onmouseup="tEdit.stopCellResize(false)" ondragstart="handleDrag(0)"> <table border="0" cellpadding="0" cellspacing="0" width="7" height="7"> <tr><td bgcolor="#000000"></td></tr> </table> </div> <div id="dArrow" title="Drag to modify cell height." style="position:absolute; display:none; cursor: S-resize; z-index: 0" onmousedown="tEdit.markCellResize(this)" onmouseup="tEdit.stopCellResize(false)" ondragstart="handleDrag(0)"> <table border="0" cellpadding="0" cellspacing="0" width="7" height="7"> <tr><td bgcolor="#000000"></td></tr> </table> </div>');
