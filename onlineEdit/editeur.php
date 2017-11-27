<?
	session_start();

	if (preg_match ("/page=([0-9]*)/", getenv("HTTP_REFERER"), $regs)) {
		$page=$regs[1];
	} else {
		$page=$_SESSION["currentPage"];
	}


	$user="";
	$password="";
	@$user=$_GET["user"];
	@$password=$_GET["password"];
	include("db.php");
	$dbh =  connectDb(); 
	echo '<'.'?xml version="1.0" encoding="iso-8859-1"?'.'>';
	// Si le user et le mot de passe sont renseignés
	if (isset($user) && isset($password)) {
		// Essaie de trouver dans la base l'utilisateur correspondant
		$query = "select * from t_personne where pers_user='$user' and pers_password='$password' and pers_active=1";
		$result = mysql_query($query, $dbh);
		// Si la personne est trouvée
		if ($result>0 && mysql_num_rows($result)>0) {
			$_SESSION['userID']=mysql_result($result,0,"pers_id");
			$_SESSION['userName']=$user;
		}
	}
	if (isset($_SESSION['userID'])) {
		$_SESSION['userID']='yop';
		
?>
<editeur style="onlineEdit/editeur.xsl">
	<programme>
		
		
		<fonction nom="findPosY">
			<parametre nom="obj"/>
			<code>
				var curtop = 0;
				if (obj.offsetParent) {
					while (obj.offsetParent) {	
						curtop += obj.offsetTop
						obj = obj.offsetParent;	}
				} else if (obj.y) {curtop+= obj.y}
			
				return curtop;
			</code>		
		</fonction>
		
	
			<fonction nom="findPosX">
			<parametre nom="obj"/>
			<code>
				var curleft = 0;
				if (obj.offsetParent) {
					while (obj.offsetParent) {
						curleft += obj.offsetLeft;
						obj = obj.offsetParent;}
						} else if (obj.x) {curleft+= obj.x;}
				return curleft;
			</code>
		</fonction>
		
		<fonction nom="cancel">
			<code>editeur.style.display='none'; editedObject=null;</code>
		</fonction>
		
		<fonction nom="cursEl">
		 <code>
              whichEl2 = event.srcElement;
			  if (editedObject==null) {
            	while (whichEl2.id.indexOf('EDIT') == -1 &amp;&amp; whichEl2.id.indexOf('MOD') == -1) { whichEl2 = whichEl2.parentElement; if (whichEl2 == null) { return }}
				if (whichEl2.id.indexOf('EDIT') !=-1 || whichEl2.id.indexOf('MOD') != -1) {
					displayPlace.style.height=(whichEl2.offsetHeight+2)+'px'
					displayPlace.style.width=whichEl2.offsetWidth+'px'
					displayPlace.style.top=findPosY(whichEl2)+'px'
					displayPlace.style.left=findPosX(whichEl2)+'px'
					displayPlace.style.display=''		
					editedElement=whichEl2;	
				}}; 

    	</code>
		</fonction>
		
		<fonction nom="outEl">
		 <code> whichEl2 = event.srcElement;     
				if (whichEl2.id.indexOf('displayPlace') !=-1) {
					displayPlace.style.display='none'
				}
    	</code>
		</fonction>
		
		<fonction nom="dropEl">
		 	<code>if (whichEl!=null) {whichEl.style.visibility='hidden'
				whichEl.style.visibility='visible'
				whichEl = null}
			</code>
		</fonction>
		
		<fonction nom="newPostit">
		 	<code>
			    login.innerHTML+=Postitmodele.outerHTML.replace(/Postitmodele/g,'Postit4').replace(/none/g,'');
			</code>
		</fonction>
		
		<fonction nom="validate">
			<code>
				if (editHTML==false) {
					code=edition.innerHTML
				} else {
					code=edition.innerText
				}
				noZone=editedObject.id.substring(8,editedObject.id.length)
				var xmlMessage = '&lt;zone id=\"' + noZone + '\"&gt;' + code + '&lt;/zone&gt;'
				var xmlHttp = new ActiveXObject('Microsoft.XMLHTTP')
				xmlHttp.open ('POST', 'onlineEdit/posterMessage.php', false)
				xmlHttp.setRequestHeader ('Content-type', 'text/xml; charset=ISO-8859-1')
				xmlHttp.send (xmlMessage)
				var xmlReponse = new ActiveXObject('MSXML2.DOMDocument.3.0')
				xmlReponse.async = false				
				
				alert(xmlHttp.responseText)
				
				var xmlMessage = '&lt;zone id=\"' + noZone + '\"&gt;' + 'converti' + '&lt;/zone&gt;'
				var xmlHttp = new ActiveXObject('Microsoft.XMLHTTP')
				xmlHttp.open ('POST', 'onlineEdit/getZone.php', false)
				xmlHttp.setRequestHeader ('Content-type', 'text/xml; charset=ISO-8859-1')
				xmlHttp.send (xmlMessage)
				xmlReponse.async = false
				editedObject.innerHTML=xmlHttp.responseText
				editeur.style.display='none'
				editedObject=null
			</code>
		</fonction>
		
		<fonction nom="switchMode">
			<code>
				if (!editHTML) { 
					edition.innerText = edition.innerHTML
				} else {
					edition.innerHTML = edition.innerText	
				}; 
				editHTML = !editHTML;
			</code>
		</fonction>
		<fonction nom="savePostIt">
			<parametre nom="texte"/>
			<parametre nom="numero"/>
			<code>
				if (texte.innerHTML != ancienTxt) {
				var xmlHttp = new ActiveXObject('Microsoft.XMLHTTP')
				xmlHttp.open ('POST', 'posterMessage.php', false)
				xmlHttp.setRequestHeader ('Content-type', 'text/xml; charset=ISO-8859-1')
				xmlHttp.send (texte.innerHTML)}
			</code>
		</fonction>
		

		<fonction nom="edit">
			<parametre nom="objDiv"/>
			<parametre nom="objSrc"/>
			<code>
				if (objSrc==null) objSrc=objDiv;
				editHTML = false
				editedObject = objDiv
				sourceObject = objSrc
				edition.style.width=(objSrc.offsetWidth+18)+'px'
				edition.style.height=(objSrc.offsetHeight+2+18)+'px'
				editeur.style.width=(objSrc.offsetWidth+18)+'px'
				edition.className = objSrc.className
		
				noZone=editedObject.id.substring(8,editedObject.id.length)
				
				var xmlMessage = '&lt;zone id=\"' + noZone + '\"&gt;' + '&lt;/zone&gt;'
				var xmlHttp = new ActiveXObject('Microsoft.XMLHTTP')
				xmlHttp.open ('POST', 'onlineEdit/getZone.php', false)
				xmlHttp.setRequestHeader ('Content-type', 'text/xml; charset=ISO-8859-1')
				xmlHttp.send (xmlMessage)
				var xmlReponse = new ActiveXObject('MSXML2.DOMDocument.3.0')
				xmlReponse.async = false
				edition.innerHTML = xmlHttp.responseText
	
				editeur.style.top=(findPosY(objSrc)-3)+'px'
				editeur.style.left=(findPosX(objSrc)-3)+'px'
				editeur.style.display=''		
			</code>
	</fonction>


	
	<fonction nom="execMyCommand">
		<parametre nom="cmd"/>
		<code>
			if (cmd=='addColumn') tEdit.processColumn('add');
			if (cmd=='removeColumn') tEdit.processColumn('remove');
			if (cmd=='addRow') tEdit.processRow('add');
			if (cmd=='removeRow') tEdit.processRow('remove');
			if (cmd=='mergeHorizontal') tEdit.processColumn('add');
			if (cmd=='mergeVertical') tEdit.processColumn('add');
		</code>
	</fonction>
	
	<fonction nom="moveEl">
	<parametre nom="e"/>
		<code>
			
			if (whichEl == null) { if (tEdit) { tEdit.changePos(); tEdit.resizeCell() }; return;}
			newX = (event.clientX + document.body.scrollLeft)
			newY = (event.clientY + document.body.scrollTop)
			distanceX = (newX - currentX)
			distanceY = (newY - currentY)
			currentX = newX
			currentY = newY
			whichEl.style.pixelLeft += distanceX
			whichEl.style.pixelTop += distanceY
			event.returnValue = false
		</code>
	</fonction>
	
	<fonction nom="popupModal">
		<parametre nom="url"/>
		<parametre nom="tailleX"/>
		<parametre nom="tailleY"/>
		<code>
			window.showModalDialog(url, document,'dialogHeight:' + tailleY + 'px; dialogWidth:' + tailleX + 'px; center:true; edge:raised; scroll:no; status:no');
		</code>
	</fonction>
	
	<fonction nom="popupModeless">
		<parametre nom="url"/>
		<parametre nom="tailleX"/>
		<parametre nom="tailleY"/>
		<parametre nom="posX"/>
		<parametre nom="posY"/>
		<code>
			return window.showModelessDialog(url, document,'dialogLeft:'+posX+'px; dialogTop:'+posY+'px; dialogHeight:' + tailleY + 'px; help:no; dialogWidth:' + tailleX + 'px; center:true; edge:raised; scroll:no; status:no');
		</code>
	</fonction>
	
	<fonction nom="editModule">
		<parametre nom="elem"/>
		<code>
			if (elem.className=='module') {
				var re = new RegExp('(name=.*)$'); 
			  	var m = re.exec(elem.src);
			  	if (m != null) {
					window.showModalDialog('/onlineEdit/dialogue/popup.php?title=Paramètres&amp;url=../editModule.php?'+escape(m[1])+escape('&amp;page=<?=$page?>'), elem,'dialogHeight:490px; dialogWidth:600px; center:true; edge:raised; scroll:no; status:no')
			  }}
		</code>
	</fonction>
	

	<fonction nom="grabEl">
		<parametre nom="e"/>
		<code>

			if (editedObject==null &amp;&amp; whichEl==null) {
			if (event.srcElement.id == 'displayPlace') {
				displayPlace.style.display='none';
				if (editedElement.id.indexOf('EDIT') !=-1) edit(editedElement);
				if (editedElement.id.indexOf('MOD') !=-1) edit(eval('EDIT_'+editedElement.id.substr(4)),eval(editedElement.id));
				
				return;
			}
			} else {
			whichEl = event.srcElement;
           	while (whichEl.id == '') { whichEl = whichEl.parentElement; if (whichEl == null) { return }}
			if (whichEl.id.indexOf('DRAG') !=-1) {
           		while (whichEl.style.position != 'absolute') { whichEl = whichEl.parentElement; if (whichEl == null) { return }}
				if (whichEl != activeEl) {
				if (activeEl==null) {maxZ=101}
				if (whichEl.style.zIndex&gt;10 &amp;&amp; whichEl.style.zIndex&lt;10000) {	whichEl.style.zIndex = maxZ+1; maxZ+=1 ;	activeEl = whichEl;
				} else {activeEl = whichEl;}}
				whichEl.style.pixelLeft = whichEl.offsetLeft;
				whichEl.style.pixelTop = whichEl.offsetTop;
				currentX = (event.clientX + document.body.scrollLeft);
				currentY = (event.clientY + document.body.scrollTop); 
			} else {whichEl=null} }
        	
		</code>
	</fonction>
	
	<fonction nom="changecss">
		<parametre nom="theClass"/>
		<parametre nom="element"/>
		<parametre nom="value"/>
		<code>
	 		for (var S = 0; S &lt; document.styleSheets.length; S++){
	  			for (var R = 0; R &lt; document.styleSheets[S]['rules'].length; R++) {
	   				if (document.styleSheets[S]['rules'][R].selectorText == theClass) {
	    				document.styleSheets[S]['rules'][R].style[element] = value; }}}
		</code>
	</fonction>



	<execution>
		<commande>var xmlDoc = new ActiveXObject('MSXML2.DOMDocument.3.0')</commande>
		<commande>xmlDoc.async=false</commande>
		<commande>xmlDoc.load ('te.js')</commande>
		<commande>eval (xmlDoc.text)</commande>
		<commande>changecss('.noContent','display','')</commande>
		<commande>document.onkeypress=null</commande>
		<commande>login.style.display=''</commande>
	   	<commande>currentX = currentY = 0;whichEl = null;activeEl = null;deltaX=0;deltaY=0; editedElement = null;editedObject=null;</commande>
	   	<commande>document.onmousemove = moveEl;</commande>
   		<commande>document.onmouseover = cursEl;</commande>
   		<commande>document.onmouseout = outEl;</commande>
   		<commande>document.onmousedown = grabEl;</commande>
   		<commande>document.onmouseup = dropEl;</commande>
		<commande>tEdit = new tableEditor('edition', 'textedit');</commande>
  		<commande>Postit_modele.style.display = 'none';</commande>
		<commande>formatWindow=popupModeless('/onlineEdit/dialogue/format.php?page=<?=$page ?>',document.body.clientWidth,110,window.screenLeft,window.screenTop-110)</commande>
	</execution>
	</programme>
	<interface>
		<palette nom="editeur" visible="false">
			<bord couleur="#FFFF00"/>
			<layout>
			<table cellpadding="0" cellspacing="0"><tr><td class="textedit" id="textedit" valign="top" height="100%"><div id="edition" bgcolor="white" name="edition" contentEditable="true" ondblclick="editModule(event.srcElement)" onmouseup="tEdit.setTableElements(); tEdit.stopCellResize(false);" onscroll="tEdit.repositionArrows()" onkeyup="tEdit.setTableElements(); tEdit.repositionArrows();" style="overflow:scroll"></div></td></tr></table><div style=" background-color:#929292; padding:3px">
					<table cellspacing="0" cellpadding="0" border="0" width="100%" background="/onlineEdit/images/palettemiddle.gif" style="cursor:move"><tr><td align="left" id="DRAG"><img src="/onlineEdit/images/paletteleft.gif"/><input type="image" onClick="validate()" src="/onlineEdit/images/editeurvalider.gif"/><input type="image" onClick="switchMode()" src="/onlineEdit/images/editeurcode_off.gif"/><input type="image" onClick="cancel()" src="/onlineEdit/images/palettefermer.gif"/></td><td align="right" id="DRAG"><img src="/onlineEdit/images/paletteright.gif"/></td></tr></table>
				</div></layout>
		</palette>
	</interface>
	<postits>
		<postit id="modele" haut="300" gauche="300">Ecrivez ici votre texte...</postit>
<?
	// Récupère l'ensemble des post-its
	$query = "select * from t_postit";
	$result = mysql_query($query, $dbh);
	if ($result>0 && mysql_num_rows($result)>0) {
		for ($i=0; $i<mysql_num_rows($result); $i++)
			echo '<postit id="'.mysql_result($result,$i,"post_id").'" haut="'.mysql_result($result,$i,"post_y").'" gauche="'.mysql_result($result,$i,"post_x").'"><contenu>'.mysql_result($result,$i,"post_contenu").'</contenu></postit>';
	} 
?>
	</postits>
</editeur>
<?
	} else {
?>
<editeur style="onlineEdit/login.xsl">
	<programme>
		<execution>
			<commande>document.onkeypress=null</commande>
			<commande>login.style.display=''</commande>
			<commande>form_login.tr_user.focus()</commande>
			<commande>window.scroll(0)</commande>
		</execution>
	</programme>
</editeur>
<?
	} 
?>
