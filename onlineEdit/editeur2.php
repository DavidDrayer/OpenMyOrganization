<?
	header('Content-Type: text/html; charset=iso-8859-1',true);

	session_start();

	if (preg_match ("/page=([0-9]*)/", getenv("HTTP_REFERER"), $regs)) {
		$page=$regs[1];
	} else {
		$page=$_SESSION["currentPage"];
	}


	$user="";
	$password="";
	@$user=$_POST["user"];
	@$password=$_POST["password"];
	include_once ($_SERVER["DOCUMENT_ROOT"]."/onlineEdit/db.php");
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
<html>

<div id="displayPlace" style="display:none; position:absolute; z-index:1000; top:0; left:0; height:100%; width:100%; background-color:#FFFF00;opacity:0.2;   filter:alpha(opacity=20);"></div>
<div id="popupdiv" style="display:none; overflow:hidden">
<div id="popupshadow" style=" position:fixed; top:0; width:100%; right:0px; height:100%; z-index:10002; background-color:#000000;opacity:0.7;   filter:alpha(opacity=70);"></div>
<iframe id ="popupcontent" src="/onlineEdit/loading.php" style="position:fixed; left:50%; top:50%; margin-top: -200px; margin-left: -200px; z-index:10003; padding:0px; border:0px;">  <p>Your browser does not support iframes.</p></iframe>
</div>
<div id="masque" style="display:none;z-index: 9999; position:fixed; top:0; left:0; height:100%; width:100%; background-color:#000000;opacity:0.2;   filter:alpha(opacity=20);"></div>
<div id="editeur" style="display:none; background-color:#FFFFFF; position:absolute; border:3px solid #FFFF00; z-index: 10000;filter:progid:DXImageTransform.Microsoft.Shadow(color='#000000', Direction=135, Strength=3);">
<table cellpadding="0" cellspacing="0"><tr><td class="textedit" id="textedit" valign="top" height="100%" style="">
<div id="edition" bgcolor="#FFFF00" name="edition" contentEditable="true" ondblclick="editModule((document.all?event.srcElement:event.target))" onmouseup="tEdit.setTableElements(); tEdit.stopCellResize(false);" onscroll=" tEdit.repositionArrows();" onkeyup="tEdit.setTableElements(); tEdit.repositionArrows();" style="overflow:scroll; min-height:100px; max-height:100%;"></div>
</td></tr></table>
<div style=" background-color:#929292; padding:3px">
	<table cellspacing="0" cellpadding="0" border="0" width="100%" background="/onlineEdit/images/palettemiddle.gif" style="cursor:move"><tr><td align="left" id="DRAG">
		<img src="/onlineEdit/images/paletteleft.gif"/><input type="image" onClick="validate()" src="/onlineEdit/images/editeurvalider.gif"/><input type="image" onClick="switchMode()" src="/onlineEdit/images/editeurcode_off.gif"/><input type="image" onClick="cancel()" src="/onlineEdit/images/palettefermer.gif"/>
	</td><td align="right" id="DRAG">
		<img src="/onlineEdit/images/paletteright.gif"/>
	</td></tr></table>

</div>
</div>

<style>
a.menuTools {text-decoration:none; display:block; margin-bottom:2px; margin-top:2px; background-color:#7DC0F2; color:#FFFFFF; font-size:11px; font-weight:bold; font-family:Arial, Helvetica, sans-serif; height:25px; padding:5px; padding-top:9px;}
.titreRubrique {margin:2px; font-size:11px; font-weight:bold; font-family:Arial, Helvetica, sans-serif;color:#FFFFFF;border:2px solid #76B6E5; padding:3px;background:url(../images/bkg_points.gif)}
a.menuTools:hover {background-color:#FFFF99; color:#000000;}
.menuTools img {
	float:left;
	vertical-align:middle;
	border:0px;
	margin-right:3px;
	margin-top:-4px;
}
</style>




<div style="background-color:#679FC8;position: fixed; z-index: 10001; top:82px; left:0px; border:2px solid black;">
				<div class="titreRubrique">Site</div>
				<a class="menuTools" href="#" title="Gérer les pages du site (atteindre une page, en ajouter ou en supprimer)."  onClick="popupModal('/onlineEdit/dialogue/page.php',500,485); return false;"/><img src="/onlineEdit/images/btn_pages.gif">Gérer les pages</a>
				<div class="titreRubrique">Page</div>
				<a class="menuTools" href="#" title="Modifier les propriétés de la page."  onClick="popupModal('/onlineEdit/dialogue/newPage.php?pageId=<?=$page ?>',500,485); return false;"/><img src="/onlineEdit/images/btn_pageProperty.gif">Propriétés de la page</a>
				<a class="menuTools" href="#" title="Affiche les statistiques de fréquentation de la page."  onClick="popupModal('/onlineEdit/dialogue/viewStat.php?pageId=<?=$page ?>',500,533); return false;"/><img src="/onlineEdit/images/btn_stat.gif">Statistiques de la page</a>
				<div class="titreRubrique">Insérer</div>
				<a class="menuTools" href="#" title="Insérer un module de fonction."  onClick="if (checkActive()) {popupModal('/onlineEdit/dialogue/insertModule.php',597,510);}; return false;"/><img src="/onlineEdit/images/btn_insertImage.gif">Insérer un module</a>
				<a class="menuTools" href="#" title="Insérer une image ou en télécharger une nouvelle."  onClick="if (checkActive()) {popupModal('/onlineEdit/dialogue/selectImage.php',597,510);}; return false;"/><img src="/onlineEdit/images/btn_insertImage.gif">Insérer une image</a>
				<a class="menuTools" href="#" title="Insère un lien sur une image, un fichier ou une page."  onClick="if (checkActive() &amp;&amp; checkSelection()) {popupModal('/onlineEdit/dialogue/selectLink.php',500,533);}; return false;"/><img src="/onlineEdit/images/btn_insertLink.gif">Insérer un lien</a>
				<a class="menuTools" href="#" title="Insère un tableau."  onClick="if (checkActive()) {popupModal('/onlineEdit/dialogue/insertTab.php',597,510);}; return false;"/><img src="/onlineEdit/images/btn_insertTab.gif">Insérer un tableau</a>
				<a class="menuTools" href="#" title="Insère une barre de sépaation horizontale."  onClick="if (checkActive()) {insertHtml('<hr/>')}; return false;"/><img src="/onlineEdit/images/btn_insertHR.gif">Insérer une séparation</a>
				<div class="titreRubrique">Supprimer</div>
				<a class="menuTools" href="#" title="Supprime le formattage de la sélection."  onClick="if (checkActive()) {document.execCommand('removeFormat');}; return false;"/><img src="/onlineEdit/images/btn_clearFormating.gif">Supprime le formatage</a>
				<a class="menuTools" href="#" title="Supprime tous les liens de la sélection."  onClick="document.execCommand('Unlink'); return false;"/><img src="/onlineEdit/images/btn_clearLink.gif">Supprime les liens</a>
</div>

<div style="height:80px; width:100%; overflow:hidden; position: fixed; z-index: 10001; top:0; left:0;right:0px; background:url(/onlineEdit/images/logo_lwe.gif) no-repeat left top #679FC8; border:2px solid black;" onLoad="openOtherWindows(); setInterval('checkOtherWindows()',100)">
<table align="right"><tr><td nowrap="">
				  <input title="Gras" type="image" src="/onlineEdit/images/btn_gras.gif" onClick="if (checkActive()) {document.execCommand('Bold');}"/>
				  <input title="Italique" type="image" src="/onlineEdit/images/btn_italique.gif" onClick="if (checkActive()) {document.execCommand('Italic');}"/>
				  <img src="/onlineEdit/images/btn_separation.gif"/>
				   <input title="Aligné à gauche" type="image" src="/onlineEdit/images/btn_alignerGauche.gif" onClick="if (checkActive()) {document.execCommand('justifyLeft');}"/>
				   <input title="Aligné au centre" type="image" src="/onlineEdit/images/btn_alignerCentre.gif" onClick="if (checkActive()) {document.execCommand('justifyCenter');}"/>
				   <input title="Aligné à droite" type="image" src="/onlineEdit/images/btn_alignerDroit.gif" onClick="if (checkActive()) {document.execCommand('justifyRight');}"/>
				   <input title="Justifié" type="image" src="/onlineEdit/images/btn_justifier.gif" onClick="if (checkActive()) {document.execCommand('justifyFull');}"/>
				   <img src="/onlineEdit/images/btn_separation.gif"/></td><td nowrap=""><select name="lcm_style" onChange="if (checkActive()) {applyStyle(this.value); this.selectedIndex=0;}">
				     <option value="">-- Choisissez le style --</option>
				     <option value="<p>">Normal</option>
				     <option value="<H1>">Titre</option>
				     <option value="<H2>">Sous titre</option>
				     <option value="<H3>">Chapeau</option>
				     <option value="<H4>">Encadré</option>
				     <option value="<H5>">Remarque</option>
				     <option value="<CITE>">Citation</option>
				     <option value="<BLOCKQUOTE>">Extrait</option>
				     <option value="<MARK>">En évidence</option>
				     <option value="<CODE>">Code</option>

				   </select>
				  </td><td nowrap=""><input  title="Efface le formattage de la sélection" type="image" src="/onlineEdit/images/btn_clearFormating.gif" onClick="if (checkActive()) {document.execCommand('removeFormat');}"/></td><td><img src="/onlineEdit/images/btn_separation.gif"/>
				    <input name="image3" type="image" title="Insérer une liste numérotée" onClick="if (checkActive()) {document.execCommand('InsertOrderedList');}" src="/onlineEdit/images/btn_listeNo.gif"/>
				    <input name="image4" type="image" title="Insérer une liste à puce" onClick="if (checkActive()) {document.execCommand('InsertUnorderedList');}" src="/onlineEdit/images/btn_listePuce.gif"/>
				    <input name="image5" type="image" title="Indenter négativement" onClick="if (checkActive()) {document.execCommand('Outdent');}" src="/onlineEdit/images/btn_indentMoins.gif"/>
				    <input name="image6" type="image" title="Indener positivement" onClick="if (checkActive()) {document.execCommand('Indent');}" src="/onlineEdit/images/btn_indentPlus.gif"/></td>
				<td align="right" nowrap=""><input name="image" type="image" onClick="popupModal('/onlineEdit/dialogue/aide.php?pageId=<?=$page ?>&title=Aide en ligne',700,585)" alt="Manuel utilisateur de l'outils d'édition en ligne" src="/onlineEdit/images/btn_aide.gif"/>
			    <input name="image2" type="image" onClick="document.location='/index.php?page=<?=$page?>'; " alt="Fermer l'éditeur de contenu" src="/onlineEdit/images/btn_fermer.gif"/></td></tr>
  <tr>
    <td nowrap=""><div id="hideTable" style="background-color:#679FC8; filter: alpha(opacity:60) ; opacity:0.6; width:400px; height:50px; position:absolute; "></div>
	<input name="image7" type="image" title="Supprimer la ligne" onClick="if (checkActive()) {execMyCommand('removeRow');}" src="/onlineEdit/images/btn_ligneMoins.gif"/>
    <input name="image8" type="image" title="Ajouter une ligne après" onClick="if (checkActive()) {execMyCommand('addRow')}" src="/onlineEdit/images/btn_lignePlus.gif"/>
    <input name="image10" type="image" title="Supprimer la colonne" onClick="if (checkActive()) {execMyCommand('removeColumn');}" src="/onlineEdit/images/btn_colMoins.gif"/>
    <input name="image9" type="image" title="Ajouter une colonne après" onClick="if (checkActive()) {execMyCommand('addColumn');}" src="/onlineEdit/images/btn_colPlus.gif"/>
<!--    <input name="image11" type="image" title="Fusionner horizontalement" onClick="if (checkActive()) {window.dialogArguments.execCommand('mergeHorizontal');}" src="../images/btn_gras.gif"/>
    <input name="image12" type="image" title="Fusionner verticalement" onClick="if (checkActive()) {window.dialogArguments.execCommand('mergeVertical');}" src="../images/btn_gras.gif"/>
-->  </td>
    <td nowrap="">&nbsp;</td>
    <td nowrap="">&nbsp;</td>
    <td>	<img src="/onlineEdit/images/btn_separation.gif"/> <div id="hideTools" style="background-color:#679FC8; opacity:0.6; filter: alpha(opacity:60) ; width:32px; height:50px; position:absolute; " ></div><input name="image8" type="image" title="Ouvrir la palette d'outils" onClick="if (toolWindow.closed) showTools();" src="/onlineEdit/images/btn_windowTools.gif"/> <div id="hideStyle" style="background-color:#679FC8; filter: alpha(opacity:60); width:32px; height:50px; position:absolute; " ></div><input name="image7" type="image" title="Ouvrir la palette de styles" onClick="if (styleWindow.closed) showStyle();" src="/onlineEdit/images/btn_windowStyle.gif"/>
    
</td>
    <td align="right" nowrap="">&nbsp;</td>
  </tr>
</table>
</div>
<script>

// Code pour gérer la sélection sous FF

       if (!document.selection && document.getSelection) {
        // SelectionObject
        function SelectionObject(Window) { 
            this.window=(Window?Window:window);
            this.document=this.window.document;
        }
        SelectionObject.prototype={
          "clear":function() {
              try {
                  var sel = this.window.getSelection();
                  sel.collapse(true);
                  sel.dettach();
              } catch (ex) {}
          },
          "createRange":function() {
              if (this.type=="none") {
                  return "no selection";
              }
              var txt = this.document.getSelection()
              var sel = {};
              try { sel=this.window.getSelection().getRangeAt(0); } catch (ex) {}
              var html = getHTMLOfSelection(this.window, this.document);
              var range = null;
 
              range = new ControlRangeObject();
              range._text=(""+txt+"");
              range._htmlText=html;
              range._range=sel;
              range.base=sel.commonAncestorContainer?sel.commonAncestorContainer:this.document.body
              range.items=new Array();
              range.addElement=range.add;
 
              try {
                  while (range.base.nodeName.substr(0,1)=="#") {
                      range.base=range.base.parentNode;
                  }
                  var index = 0; var started;
                  var current = range.base.childNodes[0];
                  while (current) {
                      if (started || current==sel.startContainer || current==sel.commonAncestorContainer) {
                          started = true;
                          range.items.push(current);
                      }
                      if (current == sel.endContainer || current==sel.commonAncestorContainer) {
                          break;
                      }
                      index++;
                      current = range.base.childNodes[index];
                  }
                  range.length=range.items.length;
              } catch (ex) {}
 
              return range;
            }
        }
 
        SelectionObject.prototype.empty=SelectionObject.prototype.clear;
        SelectionObject.prototype.createRangeControl=SelectionObject.prototype.createRange;
        SelectionObject.prototype.__defineGetter__("type", function() {
          try {
              var sel = this.window.getSelection().getRangeAt(0);
              if (sel.commonAncestorContainer.nodeName.substr(0,1)=="#") {
                  return "text";
              } else {
                  return "control";
              }
          } catch (ex) {}
          return "none";
        })
        SelectionObject.prototype.__defineSetter__("type", function() {
          // Do nothing
        })
 
        // ControlRangeObject
        function ControlRangeObject() {}
        ControlRangeObject.prototype={
            "_text":"",
            "_htmlText":"",
            "_range":null,
            "parentElement":function() {
                return this.base;
            },
            "item":function(i) {
                return this.items[i];
            },
            "add":function(node) {
                try {
                    this._range.insertNode(node);
                } catch (ex) {}
            },
            "execCommand":function(a1,a2,a3,a4) {
                var mode = document.designMode;
                document.designMode="on";
                document.execCommand(a1,a2,a3,a4);
                document.designMode=mode;
            }
        }
        // Properties
        ControlRangeObject.prototype.__defineGetter__("text",function() {
            return this._text;
        });
        ControlRangeObject.prototype.__defineSetter__("text",function(value) {
            var range = this._range;
            var p=document.createTextNode(value);
            range.deleteContents();
            range.insertNode(p)
        });
 
        ControlRangeObject.prototype.__defineGetter__("htmlText",function() {
            return this._htmlText
        });
        ControlRangeObject.prototype.__defineSetter__("htmlText",function(value) {
            var range = this._range;
            var p=document.createElement("htmlSection");
            p.innerHTML=value;
            range.deleteContents();
            range.insertNode(p)
        });
 
        document.selection=new SelectionObject();
 
        function getHTMLOfSelection (window, document) {
          var range;
          if (window.ActiveXObject && document.selection && document.selection.createRange) {
            range = document.selection.createRange();
            return range.htmlText;
          }
          else if (window.getSelection) {
            var selection = window.getSelection();
            if (selection.rangeCount > 0) {
              range = selection.getRangeAt(0);
              var clonedSelection = range.cloneContents();
              var div = document.createElement('div');
              div.appendChild(clonedSelection);
              return div.innerHTML;
            }
            else {
              return '';
            }
          }
          else {
            return '';
          }
        }
    }
   
    

// Ex editeur.php
	findPosY=function(obj) {
		var curtop = 0;
		if (obj.offsetParent) {
			while (obj.offsetParent) {	
				curtop += obj.offsetTop
				obj = obj.offsetParent;	}
		} else if (obj.y) {curtop+= obj.y}
	
		return curtop;
	}
		
	
	findPosX=function(obj) {
		var curleft = 0;
		if (obj.offsetParent) {
			while (obj.offsetParent) {
				curleft += obj.offsetLeft;
				obj = obj.offsetParent;}
				} else if (obj.x) {curleft+= obj.x;}
		return curleft;
	}
		
		cancel=function() { 
			editeur.style.display='none'; editedObject=null;
			masque.style.display='none';
		}
		
		cursEl=function(event) {
				
              whichEl2 = event.srcElement || event.target;
			  if (editedObject==null) {
            	while (whichEl2.id.indexOf('EDIT') == -1 && whichEl2.id.indexOf('MOD') == -1) { whichEl2 = whichEl2.parentElement; if (whichEl2 == null) { return }}
				if (whichEl2.id.indexOf('EDIT') !=-1 || whichEl2.id.indexOf('MOD') != -1) {
					displayPlace.style.height=(whichEl2.offsetHeight+2)+'px'
					displayPlace.style.width=whichEl2.offsetWidth+'px'
					displayPlace.style.top=findPosY(whichEl2)+'px'
					displayPlace.style.left=findPosX(whichEl2)+'px'
					displayPlace.style.display=''		
					editedElement=whichEl2;	
				}}; 
		}
		
		outEl=function(event) {
		 whichEl2 = event.srcElement || event.target;    
				if (whichEl2.id.indexOf('displayPlace') !=-1) {
					displayPlace.style.display='none'
				}
		}
		
		dropEl=function(event) {
		 	if (whichEl!=null) {whichEl.style.visibility='hidden'
				whichEl.style.visibility='visible'
				whichEl = null}
		}
		
		
		validate=function() {

				if (editHTML==false) {
					code=edition.innerHTML
				} else {
					if (document.all)  
						edition.innerHTML=edition.innerText
					 else 
						edition.innerHTML=edition.textContent;
					edition.className=oldClass;
					code=edition.innerHTML
				}
				noZone=editedObject.id.substring(8,editedObject.id.length)
				var xmlMessage = '<zone id=\"' + noZone + '\">' + code + '</zone>'
				var xmlHttp = getXhr ();
				xmlHttp.open ('POST', 'onlineEdit/posterMessage.php', false)
				//xmlHttp.setRequestHeader ('Content-type', 'text/html; charset=ISO-8859-1')
				xmlHttp.send (xmlMessage)
				
				alert(xmlHttp.responseText)
				
				var xmlMessage = '<zone id=\"' + noZone + '\">' + 'converti' + '</zone>'
				var xmlHttp = getXhr ()
				xmlHttp.open ('POST', 'onlineEdit/getZone.php', false)
				xmlHttp.setRequestHeader ('Content-type', 'text/xml; charset=ISO-8859-1')
				xmlHttp.send (xmlMessage)
				editedObject.innerHTML=xmlHttp.responseText
				editeur.style.display='none'
				masque.style.display='none'
				editedObject=null
		}
		
		formatHTML=function(code) {
			
			code = code.replace(/(<\s*\/\s*(?:p|h1|h2|h3|h4|h5|h6|table|tr|div|td)\s*.*?>|<\s*br\s*>|<\s*hr\s*>)(.)/g,"$1\n$2");
			code = code.replace(/(<\s*\s*(?:table|tbody|tr)\s*.*?>|<\s*br\s*>|<\s*hr\s*>)(.)/g,"$1\n$2");
			code = code.replace(/</g,"&lt;");
			code = code.replace(/>/g,"&gt;");
			// Mise en gras du tout
			code="<span style='font-weight:bold'>"+code+"</span>";
			// Mise en gras des tag
			code = code.replace(/(&lt;\s*\w.*?&gt;|&lt;\s*\/\s*\w.*?&gt;)/g,"<span style='font-weight:normal; color:#0000FF'>$1</span>");
			//Mise en forme des attributs
			code = code.replace(/(&lt;\s*\w*\s)(.*?)(&gt;)/g,"$1<span style='font-style:italic; color:#FF0000'>$2</span>$3");
			
			return code;
		}
		
		switchMode=function() {
				
				if(document.all){
					if (!editHTML) { 
						oldClass=edition.className
						edition.className="codeEditor";
						
						edition.innerHTML = formatHTML(edition.innerHTML)
					} else {
						edition.className=oldClass;
						edition.innerHTML = edition.innerText	
					}; 
				     
				} else{
					if (!editHTML) { 
						oldClass=edition.className
						edition.className="codeEditor";
						edition.innerHTML = formatHTML(edition.innerHTML)
					} else {
						edition.className=oldClass;
						edition.innerHTML = edition.textContent	
					}; 
				    
				}

				editHTML = !editHTML;
		}

		edit= function(objDiv, objSrc) {

				if (objSrc==null) objSrc=objDiv;
				editHTML = false
				editedObject = objDiv
				sourceObject = objSrc
				edition.style.width=(objSrc.offsetWidth+18)+'px'
				edition.style.minHeight=(objSrc.offsetHeight+2+18)+'px'
				editeur.style.width=(objSrc.offsetWidth+18)+'px'
				edition.className = objSrc.className
		
				noZone=editedObject.id.substring(8,editedObject.id.length)
				
				var xmlMessage = '<zone id=\"' + noZone + '\">' + '</zone>'
				var xmlHttp = getXhr ()
				xmlHttp.open ('POST', 'onlineEdit/getZone.php', false)
				xmlHttp.setRequestHeader ('Content-type', 'text/xml; charset=ISO-8859-1')
				xmlHttp.send (xmlMessage)
				edition.innerHTML = xmlHttp.responseText
	
				editeur.style.top=(findPosY(objSrc)-3)+'px'
				editeur.style.left=(findPosX(objSrc)-3)+'px'
				editeur.style.display=''		
				masque.style.display=''		
		}


	
	execMyCommand=function(cmd) {
			if (cmd=='addColumn') tEdit.processColumn('add');
			if (cmd=='removeColumn') tEdit.processColumn('remove');
			if (cmd=='addRow') tEdit.processRow('add');
			if (cmd=='removeRow') tEdit.processRow('remove');
			if (cmd=='mergeHorizontal') tEdit.processColumn('add');
			if (cmd=='mergeVertical') tEdit.processColumn('add');
	}
	
	moveEl=function(event) {
			
			if (whichEl == null) { if (tEdit) { tEdit.changePos(); tEdit.resizeCell() }; return;}
			newX = (event.clientX + document.body.scrollLeft)
			newY = (event.clientY + document.body.scrollTop)
			distanceX = (newX - currentX)
			distanceY = (newY - currentY)
			currentX = newX
			currentY = newY
		
			
			whichEl.style.left = (parseInt(whichEl.style.left.replace("px",""))+distanceX) +"px"
			whichEl.style.top = (parseInt(whichEl.style.top.replace("px",""))+distanceY)+"px"
			//whichEl.style.pixelLeft += distanceX
			//whichEl.style.pixelTop += distanceY
			event.returnValue = false
	}
	removeLink=function() {
		document.execCommand('unlink',false,false);
	}
	replaceSelection=function(htmlCode) {
		if (document.all) {
			htmlCode=htmlCode.replace("[-selection-]",selBeforePopup.htmlText);
			selBeforePopup.pasteHTML(htmlCode);
		} else {
			htmlCode=htmlCode.replace("[-selection-]",selBeforePopup.htmlText);
			document.execCommand('insertHTML', false, htmlCode);
			//selBeforePopup.text="toto";
		}
	}
	closePopup=function() {
		document.getElementById("popupdiv").style.display="none";
		document.getElementById("popupcontent").src="/onlineEdit/load.php";
		parent.parent.srcElemBeforPopup=null;
		selBeforePopup=null;
	}
	
	popupModal=function(url, tailleX, tailleY) {
			try {selBeforePopup = document.selection.createRange();} catch(e) {}
			document.getElementById("popupdiv").style.display="";
			document.getElementById("popupcontent").src=url;
			document.getElementById("popupcontent").style.width=tailleX+"px";
			document.getElementById("popupcontent").style.height=tailleY+"px";
			document.getElementById("popupcontent").style.marginLeft=(0-(tailleX/2))+"px";
			document.getElementById("popupcontent").style.marginTop=(0-(tailleY/2))+"px";

			//window.showModalDialog(url, document,'dialogHeight:' + tailleY + 'px; dialogWidth:' + tailleX + 'px; center:true; edge:raised; scroll:no; status:no');
	}
	
	popupModeless=function (url, tailleX, tailleY, posX,posY) {

			return window.showModelessDialog(url, document,'dialogLeft:'+posX+'px; dialogTop:'+posY+'px; dialogHeight:' + tailleY + 'px; help:no; dialogWidth:' + tailleX + 'px; center:true; edge:raised; scroll:no; status:no');
	}
	
	editModule=function(elem) {

			if (elem.className=='module') {
				var re = new RegExp('(name=.*)$'); 
			  	var m = re.exec(elem.src);
			  	if (m != null) {
			  		srcElemBeforPopup = elem;
			  		popupModal('/onlineEdit/editModule.php?'+m[1]+'&page=<?=$page?>',600,490);
					//window.showModalDialog('/onlineEdit/dialogue/popup.php?title=Paramètres&url=../editModule.php?'+escape(m[1])+escape('&page=<?=$page?>'), elem,'dialogHeight:490px; dialogWidth:600px; center:true; edge:raised; scroll:no; status:no')
			  }} else {
			  
			  	if (elem.nodeName=="TD") {
			  		// Récupère les infos sur le tableau
	
			  		for (tab=elem;tab.nodeName!="TABLE";tab=tab.parentNode) {
			  		
			  		}
			  		// Crée un range de sélection autour du tableau
			  		var range = document.createRange();
    				range.selectNode(tab);
   					
    			

			  		
			  		// Récupère la structure du tableau
			  		width=(tab.attributes.getNamedItem("width")!=null?tab.attributes.getNamedItem("width").value:"0");
			  		height=(tab.attributes.getNamedItem("height")!=null?tab.attributes.getNamedItem("height").value:"0");
			  		
					  
					for (i=0; i<tab.childNodes.length; i++){
			  			if (tab.childNodes[i].nodeName=="TBODY") {
							nbRows=tab.childNodes[i].childNodes.length;
						}	
					}
					
					// Modifie le tableau avec la nouvelle version
					var sel = window.getSelection();
   				 	sel.removeAllRanges();
    				sel.addRange(range);
			  		document.selection.createRange().pasteHTML("<b>PASTED : "+width+" "+height+" " + nbRows +"</b>");
			  		
			  		
			  		
			  		
				}
			  	
			  	}
	}
	

	grabEl=function(event) {

			actuEvent=event.srcElement || event.target;
			if (editedObject==null && whichEl==null) {
			if (actuEvent.id == 'displayPlace') {
				displayPlace.style.display='none';
				if (editedElement.id.indexOf('EDIT') !=-1) edit(editedElement);
				if (editedElement.id.indexOf('MOD') !=-1) edit(eval('EDIT_'+editedElement.id.substr(4)),eval(editedElement.id));
				
				return;
			}
			} else {
			whichEl = event.srcElement || event.target;
           	while (whichEl.id == '') { whichEl = whichEl.parentElement; if (whichEl == null || whichEl.id == null) { return }}
			
			if (whichEl.id.indexOf('DRAG') !=-1) {
           		while (whichEl.style.position != 'absolute') { whichEl = whichEl.parentElement; if (whichEl == null) { return }}
				
				if (whichEl != activeEl) {
				if (activeEl==null) {maxZ=101}
				if (whichEl.style.zIndex>10 && whichEl.style.zIndex<10000) {	whichEl.style.zIndex = maxZ+1; maxZ+=1 ;	activeEl = whichEl;
				} else {activeEl = whichEl;}}
				whichEl.style.pixelLeft = whichEl.offsetLeft;
				whichEl.style.pixelTop = whichEl.offsetTop;
				currentX = (event.clientX + document.body.scrollLeft);
				currentY = (event.clientY + document.body.scrollTop); 
			} else {whichEl=null} }

	}
	
	changecss=function(theClass, element, value) {
			alert(theClass);
	 		for (var S = 0; S < document.styleSheets.length; S++){
	 			if (document.styleSheets[S].cssRules) {
					crossrule=document.styleSheets[S].cssRules
				} else {
					crossrule=document.styleSheets[S].rules
				}
	 		
	  			for (var R = 0; R < crossrule.length; R++) {
	   				if (crossrule[R].selectorText == theClass) {
	    				crossrule[R].style[element] = value; }}}
	}

// Ex editeur.php
	var toolWindow, styleWindow; 
	showTable=function() {
		document.getElementById("hideTable").style.display="none";
	}
	hideTable=function() {
		document.getElementById("hideTable").style.display="";
	}
	showStyle=function() {
		styleWindow = popupModeless('style.php?page=<?=$page ?>',170,350,window.screenLeft+window.dialogArguments.body.clientWidth-173, window.screenTop+80, window.dialogArguments);
	}
	showTools=function() {
		toolWindow = popupModeless('tools.php?page=<?=$page ?>',160,550,window.screenLeft-3, window.screenTop+80, window.dialogArguments);
	}
	openOtherWindows=function() {
		showStyle();
		showTools();
	}
	checkOtherWindows=function() {
		if (toolWindow.closed) {document.getElementById("hideTools").style.display="none"} else {document.getElementById("hideTools").style.display=""};
		if (styleWindow.closed) {document.getElementById("hideStyle").style.display="none"} else {document.getElementById("hideStyle").style.display=""};
	}

// Ex Palette.js
	popupModeless=function (url,tailleX,tailleY, posX,posY, mainWindow) {
		if (mainWindow==null) {mainWindow=document;}
		return window.showModelessDialog(url, mainWindow,'dialogLeft:'+posX+'px; dialogTop:'+posY+'px; dialogHeight:' + tailleY + 'px; help:no; dialogWidth:' + tailleX + 'px; center:true; edge:raised; scroll:no; status:no');
	}
		
		
	getSelectionHtml=function() {
			var html = "";
			if (typeof window.getSelection != "undefined") {
				var sel = window.getSelection();
				if (sel.rangeCount) {
					var container = document.createElement("div");
					for (var i = 0, len = sel.rangeCount; i < len; ++i) {
						container.appendChild(sel.getRangeAt(i).cloneContents());
					}
					html = container.innerHTML;
				}
			} else if (typeof document.selection != "undefined") {
				if (document.selection.type == "Text") {
					html = document.selection.createRange().htmlText;
				}
			}
			return html;
		}
	checkSelection=function() {
		//selText=(document.all?document.selection.createRange().text:document.getSelection().getRangeAt(0))
		if (getSelectionHtml()=='') {alert ('Sélectionner une zone de texte ou une image'); return false;} 
		return true;
	}

	checkActive=function() {
		if (document.getElementById('editeur').style.display=='none') {alert ('Sélectionnez une zone à éditer'); return false;}
		document.getElementById('edition').focus();
		return true;
	}
	insertHtml=function(html) {
		//document.getElementById('edition').focus(); 
	   	if (document.selection.type == 'Control') { 
			return; 
		} 
		selBeforePopup = document.selection.createRange(); 

		replaceSelection(html); 
	}
	applyStyle=function(format) {
		document.execCommand('FormatBlock', false, format);
	}
	
// Ex te.js
function tableEditor(docID, pntCell) {


   this.docID = docID;        // ID of editable portion of document
   this.pntCell = pntCell;    // TD contentarea is contained in if any
   this.tableCell = null;     // cell currently selected
   this.tableElem = null;     // table currently selected
   this.cellResizeObj = null; // object that user clicks on to resize cell
   this.cellWidth = null;     // selected cell's current width
   this.cellHeight = null;    // selected cell's current height
   this.cellX = null;         // x coord of selected cell's bottom right 
   this.cellY = null;         // y coord of selected cell's bottom right
   this.moveable = null;      // moveable div

   // define methods only once
   if (typeof(_tableEditor_prototype_called) == 'undefined') {
      _tableEditor_prototype_called = true;

      // public methods
      tableEditor.prototype.mergeDown = mergeDown;
      tableEditor.prototype.unMergeDown = unMergeDown;
      tableEditor.prototype.mergeRight = mergeRight;
      tableEditor.prototype.splitCell = splitCell;
      tableEditor.prototype.addCell = addCell;
      tableEditor.prototype.removeCell = removeCell;
      tableEditor.prototype.processRow = processRow;
      tableEditor.prototype.processColumn = processColumn;
      tableEditor.prototype.buildTable = buildTable;
      tableEditor.prototype.setTableElements = setTableElements;
      tableEditor.prototype.unSetTableElements = unSetTableElements;
      tableEditor.prototype.setDrag = setDrag;
      tableEditor.prototype.stopCellResize = stopCellResize;
      tableEditor.prototype.markCellResize = markCellResize;
      tableEditor.prototype.resizeCell = resizeCell;
      tableEditor.prototype.changePos = changePos;
      tableEditor.prototype.resizeColumn = resizeColumn;
      tableEditor.prototype.resizeRow = resizeRow;
      tableEditor.prototype.repositionArrows = repositionArrows;
      tableEditor.prototype.explore = explore;

      // private methods
      tableEditor.prototype.__addOrRemoveCols = __addOrRemoveCols;
      tableEditor.prototype.__findParentTable = __findParentTable;
      tableEditor.prototype.__hideArrows = __hideArrows;
      tableEditor.prototype.__showArrows = __showArrows;
      tableEditor.prototype.__resizeColumn = __resizeColumn;
   }

   // create divs for editing cell width and height



   ////////////////////////////////////////////////////////////////
   //  method: setTableElements
   //    args: none
   // purpose: look to see if the cursor is inside a TD or TABLE and
   //          if so assign the TD to this.tableCell or the TABLE to
   //          this.tableElem
   //
   function setTableElements(){
      // stop resizing cell if already resizing one
;
      this.stopCellResize(true);
      this.tableCell = null;
      //try {
      cursorPos=document.selection.createRange();

	//alert(document.selection.type);
      if (document.selection.type == 'text' || document.selection.type == 'Text'|| document.selection.type == 'control') {
         var elt = cursorPos.parentElement(); 
         while (elt) {
            if (elt.tagName == "TD") {
  
               break;
            }
            elt = elt.parentElement;
         }


         if (elt) {
            // don't select document area
            if (elt.id == this.docID)
               return;

            // don't select parent TD
            if (this.pntCell)
               if (this.pntCell == elt.id)
                  return;

            this.tableCell = elt;

            // set width and height as globals for 
            // resizing
            this.cellWidth = this.tableCell.offsetWidth;
            this.cellHeight = this.tableCell.offsetHeight;
            this.__showArrows();
			
	  		showTable();

         }
      } else {
         if (cursorPos.length == 1) {
            if (cursorPos.item(0).tagName == "TABLE") {
               this.tableElem = cursorPos.item(0);
               this.__hideArrows();
               this.tableCell = null;
            }
         }
      }
  //    } catch(ex) {}
   }

   ////////////////////////////////////////////////////////////////
   //  method: unSetTableElements
   //    args: none
   // purpose: unset references to currently selected cell or table 
   //          
   function unSetTableElements(){

	       this.tableCell = null;
      this.tableElem = null;
      return;
      
   }

   ////////////////////////////////////////////////////////////////
   //  method: mergeDown
   //    args: none
   // purpose: merge the currently selected cell with the one below it
   //
   function mergeDown() {

      if (!this.tableCell) {
         return;
        }
      
      if (!this.tableCell.parentNode.nextSibling) {
         alert("There is not a cell below this one to merge with.");
         return;
      }

      var topRowIndex = this.tableCell.parentNode.rowIndex;

      //               [  TD   ] [  TR    ] [  TBODY ] [                   TR                      ] [            TD                 ]
      var bottomCell = this.tableCell.parentNode.parentNode.childNodes[ topRowIndex + this.tableCell.rowSpan ].childNodes[ this.tableCell.cellIndex ];

      if (!bottomCell) {
         alert("There is not a cell below this one to merge with.");
         return;
      }

      // don't allow merging rows with different colspans
      if (this.tableCell.colSpan != bottomCell.colSpan) {
         alert("Can't merge cells with different colSpans."); 
         return;
      }

      // do the merge
      this.tableCell.innerHTML += bottomCell.innerHTML;
      this.tableCell.rowSpan += bottomCell.rowSpan;
      bottomCell.removeNode(true); 
      this.repositionArrows();
      
   }

   ////////////////////////////////////////////////////////////////
   //  method: unMergeDown
   //    args: none
   // purpose: merge the currently selected cell with the one below it
   //
   function unMergeDown() {

      if (!this.tableCell) {
         return;
        }
      
      if (this.tableCell.rowSpan <= 1) {
         alert("RowSpan is already set to 1.");
         return;
      }

      var topRowIndex = this.tableCell.parentNode.rowIndex;

      // add a cell to the beginning of the next row
      this.tableCell.parentNode.parentNode.childNodes[ topRowIndex + this.tableCell.rowSpan - 1 ].appendChild( document.createElement("TD") );

      this.tableCell.rowSpan -= 1;

   }

   ////////////////////////////////////////////////////////////////
   //  method: mergeRight
   //    args: none
   // purpose: merge the currently selected cell with the one to 
   //          the immediate right.  Won't allow user to merge cells
   //          with different rowspans.
   //
   function mergeRight() {

	       if (!this.tableCell) {
         return;
        }
      if (!this.tableCell.nextSibling) {
         return;
        }

      // don't allow user to merge rows with different rowspans
      if (this.tableCell.rowSpan != this.tableCell.nextSibling.rowSpan) {
         alert("Can't merge cells with different rowSpans.");
         return;
      }

      this.tableCell.innerHTML += this.tableCell.nextSibling.innerHTML;
      this.tableCell.colSpan += this.tableCell.nextSibling.colSpan;
      this.tableCell.nextSibling.removeNode(true);
       

      this.repositionArrows();
      this.__hideArrows();
      this.tableCell = null;
      
   }

   ////////////////////////////////////////////////////////////////
   //  method: splitCell 
   //    args: none
   // purpose: split the currently selected cell back into two cells 
   //          it the cell has a colspan > 1.
   //
   function splitCell() {

      if (!this.tableCell) {
         return;
         }
      if (this.tableCell.colSpan < 2) {
         alert("Cell can't be divided.  Add another cell instead");
         return;
      }

      this.tableCell.colSpan = this.tableCell.colSpan - 1;
      var newCell = this.tableCell.parentNode.insertBefore( document.createElement("TD"), this.tableCell);
      newCell.rowSpan = this.tableCell.rowSpan;
      this.repositionArrows();
      
   }

   ////////////////////////////////////////////////////////////////
   //  method: removeCell 
   //    args: none
   // purpose: remove the currently selected cell
   //
   function removeCell() {

      if (!this.tableCell) {
         return;
}
      // can't remove all cells for a row
      if (!this.tableCell.previousSibling && !this.tableCell.nextSibling) {
         alert("You can't remove the only remaining cell in a row.");
         return;
      }

      this.tableCell.removeNode(false);

      this.repositionArrows();
      this.tableCell = null;

   } 
 
   ////////////////////////////////////////////////////////////////
   //  method: addCell 
   //    args: none
   // purpose: add a cell to the right of the selected cell
   //
   function addCell() {

      if (!this.tableCell)
         return;

      this.tableCell.parentElement.insertBefore(document.createElement("TD"), this.tableCell.nextSibling);
   }

   ////////////////////////////////////////////////////////////////
   //  method: processRow 
   //    args: (string)action = "add" or "remove"
   // purpose: add a row above the row that 
   //          contains the currently selected cell or
   //          remove the row containing the selected cell
   //
   function processRow(action) {

      if (!this.tableCell)
        return;

      // go back to TABLE def and keep track of cell index
      var idx = 0;
      var rowidx = -1;
      var tr = this.tableCell.parentNode;
      var numcells = tr.childNodes.length;
     

      while (tr) {
         if (tr.tagName == "TR")
            rowidx++;
         tr = tr.previousSibling;
      }
      // now we should have a row index indicating where the
      // row should be added / removed

      var tbl = this.__findParentTable(this.tableCell);
  
      if (!tbl) {
         alert("Could not " + action + " row.");
         return;
      }
     
      if (action == "add") {
         var r = tbl.insertRow(rowidx+1);
         for (var i = 0; i < numcells; i++) {
			if (this.tableCell.parentNode.childNodes[i].tagName == "TH") {
            	var c = r.appendChild( document.createElement("TH") );
			} else {
				var c = r.appendChild( document.createElement("TD") );
			}
            if (this.tableCell.parentNode.childNodes[i].colSpan)
               c.colSpan = this.tableCell.parentNode.childNodes[i].colSpan;
         }
      } else {
         tbl.deleteRow(rowidx);
         this.stopCellResize(true);
         this.tableCell = null;
      }
   }

   ////////////////////////////////////////////////////////////////
   //  method: processColumn
   //    args: (string)action = "add" or "remove"
   // purpose: add a column to the right column containing
   //          the selected cell
   //
   function processColumn(action) {

      if (!this.tableCell)
        return;

      // store cell index in a var because the cell will be
      // deleted when processing the first row
      var cellidx = this.tableCell.cellIndex;
      
      var tbl = this.__findParentTable(this.tableCell);
  
      if (!tbl) {
         alert("Could not " + action + " column.");
         return;
      }
         
      // now we have the table containing the cell
      this.__addOrRemoveCols(tbl, cellidx, action);

      // clear out global this.tableCell value for remove
      if (action == 'remove') {
         this.stopCellResize(true);
         this.tableCell = null;
      } else {
         this.repositionArrows();
      }
   }

   ////////////////////////////////////////////////////////////////
   //  method: __addOrRemoveCols
   //    args: (table object)tbl, (int)cellidx, (string)action
   //          tbl = the table containing the selected cell
   //          cellidx = the index of the selected cell in its row
   //          action = "add" or "remove" the column
   //
   // purpose: add or remove the column at the cell index
   //
   function __addOrRemoveCols(tbl, cellidx, action) {

      if (!tbl.childNodes.length)
         return;
      var i;
      for (i = 0; i < tbl.childNodes.length; i++) {
         if (tbl.childNodes[i].tagName == "TR") {
            var cell = tbl.childNodes[i].childNodes[ cellidx ];
            if (!cell)
               break; // can't add cell after cell that doesn't exist
            if (action == "add") {
            
            	var newElem = document.createElement("TD");
              
			   cell.parentNode.insertBefore(newElem, cell.nextSibling);
			  
			   //existingElement.insertAdjacentElement("AfterEnd", newElem);
               //cell.insertAdjacentElement("AfterEnd",  document.createElement("TD") );
            } else {
               // don't delete too many cells because or a rowspan setting
                 
               if (cell.rowSpan > 1) {
                  i += (cell.rowSpan - 1);
               }
               cell.parentNode.removeChild( cell );
               //cell.removeNode(true);
            }
         } else {
            // keep looking for a "TR"
            this.__addOrRemoveCols(tbl.childNodes[i], cellidx, action); 
         }
      }
   }

   ////////////////////////////////////////////////////////////////
   //  method: __findParentTable 
   //    args: (TD object)cell
   //          cell = the selected cell object
   //
   // purpose: locate the table object that contains the
   //          cell object passed in
   //
   function __findParentTable(cell) {

      var tbl = cell.parentElement
      while (tbl) {
         if (tbl.tagName == "TABLE") {
            return tbl;
         }
         tbl = tbl.parentElement;
      }
      return false;
      
   }

   ////////////////////////////////////////////////////////////////
   //  method: exploreTree 
   //    args: (obj)obj, (obj)pnt
   //          obj = object to explore
   //          pnt = object to append output to
   //
   // purpose: traverse the dom tree printing out all properties
   //          of the object, its children.....recursive.  helpful
   //          when looking for object properties.
   //
   function exploreTree(obj, pnt) {

      if (!obj.childNodes.length)
         return;
      var i;
      var ul = pnt.appendChild( document.createElement("UL") );
      for (i = 0; i < obj.childNodes.length; i++) {
         var li = document.createElement("LI");
         explore(obj.childNodes[i], li);
         ul.appendChild(li);
         exploreTree(obj.childNodes[i], li); 
         /*
         var n = document.createTextNode(obj.childNodes[i].tagName);
         li.appendChild(n);
         */
      }
   }

   ////////////////////////////////////////////////////////////////
   //  method: explore
   //    args: (obj)obj, (obj)pnt
   //          obj = object to explore
   //          pnt = object to append output to
   //
   // purpose: show all properties for the object "obj"
   //
   function explore(obj, pnt) {

      var i;
      for (i in obj) {
         var n = document.createTextNode(i +"="+obj[i]);
         pnt.appendChild(n);
         pnt.appendChild( document.createElement("BR") );
      }

   }

   ////////////////////////////////////////////////////////////////
   //  method: buildTable 
   //    args: pnt = parent to append table to
   //
   // purpose: build a test table for debugging
   //
   function buildTable(pnt) {

      var t = pnt.appendChild( document.createElement("TABLE") );
      t.border=1;
      t.cellPadding=2;
      t.cellSpacing=0;
      var tb = t.appendChild( document.createElement("TBODY") );
      for(var r = 0; r < 10; r++) {
         var tr = tb.appendChild( document.createElement("TR") );
         for(var c = 0; c < 10; c++) {
            var cell = tr.appendChild( document.createElement("TD") );
            cell.appendChild( document.createTextNode(r+"-"+c) );
         }
      }

   }

   ////////////////////////////////////////////////////////////////
   //  method: setDrag
   //    args: obj = object (DIV) that is currently draggable
   //
   // purpose: set the object to be moved with the mouse
   //
   function setDrag(obj) {

     if (this.moveable) 
       this.moveable = null;
     else 
       this.moveable = obj; 

   }


   ////////////////////////////////////////////////////////////////
   //  method: changePos
   //    args: none
   //          mouse pointer appear inside the object set by "setDrag"
   //          function above.
   //
   // purpose: move the object selected in the "setDrag" function defined
   //          above.
   //
   function changePos() {

      if (!this.moveable) 
         return;

      this.moveable.style.posTop = event.clientY - 10;
      this.moveable.style.posLeft = event.clientX - 25;

   }


   ////////////////////////////////////////////////////////////////
   //  method: markCellResize
   //    args: (object)obj = the square table div object that
   //          was clicked on by the user to resize a cell
   //
   // purpose: store the object in "this.cellResizeObj" to be referenced
   //          in the "resizeCell" function.
   //          
   //
   function markCellResize(obj) {
    	if (document.all) {
      if (this.cellResizeObj) {
         this.cellResizeObj = null;
      } else {
         this.cellResizeObj = obj;
      }
      }
   }

   ////////////////////////////////////////////////////////////////
   //  method: stopCellResize
   //    args: (bool)hideArrows
   //
   // purpose: stop changing cell width and height
   //
   function stopCellResize(hidearrows) {

      this.cellResizeObj = null;
      if (hidearrows) 
         this.__hideArrows();

   }

   ////////////////////////////////////////////////////////////////
   //  method: __hideArrows()
   //    args: none
   //
   // purpose: hide editing tabs that are positioned in the selected
   //          cell
   //
   function __hideArrows() {
 

      document.getElementById("rArrow").style.visibility = 'hidden';
      document.getElementById("dArrow").style.visibility = 'hidden';
	  hideTable();

   }

   ////////////////////////////////////////////////////////////////
   //  method: __showArrows()
   //    args: none
   //
   // purpose: position editing tabs in the middle or the right cell
   //          wall and middle of the bottom wall to be used to drag
   //          the cell's width and height dimensions
   //
   function __showArrows() {
    	if (document.all) {
      if (!this.tableCell)
         return;

      var cell_hgt = this.tableCell.offsetTop;
      var cell_wdt = this.tableCell.offsetLeft;
      var par = this.tableCell.offsetParent;
      while (par) {
         cell_hgt = cell_hgt + par.offsetTop;
         cell_wdt = cell_wdt + par.offsetLeft;
         current_obj = par;
         par = current_obj.offsetParent;
      }
      this.cellX = cell_wdt + this.tableCell.offsetWidth; //bottom right X
      this.cellY = cell_hgt + this.tableCell.offsetHeight; // bottom right Y

      var scrollTop = document.getElementById(this.docID).scrollTop;
      var scrollLeft = document.getElementById(this.docID).scrollLeft;

      document.getElementById("rArrow").style.posLeft = cell_wdt + this.tableCell.offsetWidth - 6 - scrollLeft;
      document.getElementById("rArrow").style.posTop = cell_hgt + (this.tableCell.offsetHeight / 2) - 2 - scrollTop;

      document.getElementById("dArrow").style.posLeft = cell_wdt + (this.tableCell.offsetWidth / 2) - 2 - scrollLeft;
      document.getElementById("dArrow").style.posTop = cell_hgt + this.tableCell.offsetHeight - 6 - scrollTop;

      document.getElementById("rArrow").style.visibility = 'visible';
      }
      //document.getElementById("dArrow").style.visibility = 'visible';
   }

   ////////////////////////////////////////////////////////////////
   //  method: repositionArrows()
   //    args: none
   //
   // purpose: reposition editing tabs in the middle or the right cell
   //          wall and middle of the bottom wall to be used to drag
   //          the cell's width and height dimensions.  this must be
   //          run while changing the cell's dimensions.
   //
   function repositionArrows() {
    	if (document.all) {

      if (!this.tableCell)
         return;

      var cell_hgt = this.tableCell.offsetTop;
      var cell_wdt = this.tableCell.offsetLeft;
      var par = this.tableCell.offsetParent;
      while (par) {
         cell_hgt = cell_hgt + par.offsetTop;
         cell_wdt = cell_wdt + par.offsetLeft;
         current_obj = par;
         par = current_obj.offsetParent;
      }

      var scrollTop = document.getElementById(this.docID).scrollTop;
      var scrollLeft = document.getElementById(this.docID).scrollLeft;

      document.getElementById("rArrow").style.posLeft = cell_wdt + this.tableCell.offsetWidth - 6 - scrollLeft;
      document.getElementById("rArrow").style.posTop = cell_hgt + (this.tableCell.offsetHeight / 2) - 2 - scrollTop;

      document.getElementById("dArrow").style.posLeft = cell_wdt + (this.tableCell.offsetWidth / 2) - 2 - scrollLeft; 
      document.getElementById("dArrow").style.posTop = cell_hgt + this.tableCell.offsetHeight - 6 - scrollTop;
   }}

   ////////////////////////////////////////////////////////////////
   //  method: resizeCell()
   //    args: none
   //
   // purpose: resize the selected cell based on the direction of the mouse
   //
   function resizeCell() {
    	if (document.all) {
      if (!this.cellResizeObj)
         return;

      if (this.cellResizeObj.id == 'dArrow') {
         var scrollTop = document.getElementById(this.docID).scrollTop;
         var newHeight = (event.clientY - (this.cellY - scrollTop) ) + this.cellHeight;

         if (newHeight > 0)
            // don't resize entire row if rowspan > 1
            if (this.tableCell.rowSpan > 1) 
               this.tableCell.style.height = newHeight;
            else 
               this.resizeRow(newHeight);

         this.repositionArrows();

      } else if (this.cellResizeObj.id == 'rArrow') {
         var scrollLeft = document.getElementById(this.docID).scrollLeft;
         var newWidth = (event.clientX - (this.cellX - scrollLeft) ) + this.cellWidth;

         if (newWidth > 0) 
            // don't resize entire column if colspan > 1
            if (this.tableCell.colSpan > 1)
               this.tableCell.style.width = newWidth;
            else
               this.resizeColumn(newWidth);

         this.repositionArrows();

      } else {
         // do nothing
      }
      }
   }

   ////////////////////////////////////////////////////////////////
   //  method: resizeRow 
   //    args: (int)size
   // purpose: set cell.style.height on all cells in a row that
   //          have rowspan = 1 
   //
   function resizeRow(size) {
    	if (document.all) {
      if (!this.tableCell)
        return;

      // go back to TABLE def and keep track of cell index
      var idx = 0;
      var rowidx = -1;
      var tr = this.tableCell.parentNode;
      var numcells = tr.childNodes.length;

      while (tr) {
         if (tr.tagName == "TR")
            rowidx++;
         tr = tr.previousSibling;
      }
      // now we should have a row index indicating where the
      // row should be added / removed

      var tbl = this.__findParentTable(this.tableCell);
  
      if (!tbl) {
         return;
      }
     
      // resize cells in the row
      for (var j = 0; j < tbl.rows(rowidx).cells.length; j++) {
         if (tbl.rows(rowidx).cells(j).rowSpan == 1)
            tbl.rows(rowidx).cells(j).style.height = size;
      }
      }
   }


   ////////////////////////////////////////////////////////////////
   //  method: resizeColumn 
   //    args: (int)size = size in pixels
   // purpose: set column width
   //
   function resizeColumn(size) {
    	if (document.all) {
      if (!this.tableCell)
        return;

      // store cell index in a var because the cell will be
      // deleted when processing the first row
      var cellidx = this.tableCell.cellIndex;
      
      var tbl = this.__findParentTable(this.tableCell);
  
      if (!tbl) {
         alert("Could not resize  column.");
         return;
      }
         
      // now we have the table containing the cell
      this.__resizeColumn(tbl, cellidx, size);
      }
   }

   ////////////////////////////////////////////////////////////////
   //  method: __resizeColumn
   //    args: (table object)tbl, (int)cellidx, (int)size
   //          tbl = the table containing the selected cell
   //          cellidx = the index of the selected cell in its row
   //          size = size in pixels
   //
   // purpose: resize all cells in the a column
   //
   function __resizeColumn(tbl, cellidx, size) {
    	if (document.all) {
      if (!tbl.childNodes.length)
         return;
      var i;
      for (i = 0; i < tbl.childNodes.length; i++) {
         if (tbl.childNodes[i].tagName == "TR") {
            var cell = tbl.childNodes[i].childNodes[ cellidx ];
            if (!cell)
               break; // can't add cell after cell that doesn't exist

            if (cell.colSpan == 1)
               cell.style.width = size;
         } else {
            // keep looking for a "TR"
            this.__resizeColumn(tbl.childNodes[i], cellidx, size); 
         }
      }
      }
   }
} 
	document.onmousemove = moveEl;
	document.onmouseover = cursEl;
	document.onmouseout = outEl;
	document.onmousedown = grabEl;
	document.onmouseup = dropEl;
	
	var selBeforePopup = null;
	var srcElemBeforPopup =null;

	
	$(".noContent").css("display","block");
	window.document.body.style.marginTop='90px';
	var currentX = 0;
	var currentY = 0;
	var whichEl = null;
	var whichEl2 = null;
	var activeEl = null;
	var deltaX=0; var deltaY=0; 
	var editedElement = null;
	var editedObject=null;
	window.loadEditor=function() {alert ('Liquid Web Edition ver 2.0');}
		var tEdit = new tableEditor('edition', 'textedit');
	document.onkeypress=null;
</script>
</html>
<?
	} else {
?>
<html>
<div style="position: fixed; top:0; left:0;right:0px; z-index:1000">
<div style="opacity:0.7;   filter:alpha(opacity=70); width:100%; height:100%; background:#000000; top:0px; left:0px; position:fixed">test</div>
<div style="width:100%; height:100%; top:0px; left:0px; position:fixed">
	<table style="width:100%; height:100%;"><tr><td style="text-align:center; vertical-align:middle;"><form id="form_login" onsubmit="loadEditor(this.tr_user.value,this.tr_password.value); return false" style="text-align:center"><center><table style="background-color:#FFFF99; color:#000000; border:2px solid black;"><tr><td>Utilisateur</td></tr><tr><td><input type="text" id="tr_user"/></td></tr><tr><td>Mot de passe</td></tr><tr><td><input type="password" id="tr_password"/></td></tr><tr><td style="text-align:right"><input type="submit" value="Login"/></td></tr></table></center></form></td></tr></table>
</div>
</div>
<script>
	document.onkeypress=null;
	form_login.tr_user.focus();
</script>
</html>

<?
	} 
?>	
