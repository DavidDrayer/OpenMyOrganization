var CLIPBOARD = null;
//Projets
var STATUT_RUN = 1;
var STATUT_WAIT = 2;
var STATUT_DONE = 4;
var STATUT_ONEDAY = 8;
var STATUT_NOTES = "";
var EXTRACLASS_RUN = "projet-class-run";
var EXTRACLASS_WAIT = "projet-class-wait";
var EXTRACLASS_DONE = "projet-class-done";
var EXTRACLASS_ONEDAY = "projet-class-oneday";
//Actions
var STATUT_TODO = 3;
var STATUT_BLOCK = 5;
var STATUT_TRIGGER = 6;
var EXTRACLASS_TODO = "action-class-run";
var EXTRACLASS_BLOCK = "action-class-wait";
var EXTRACLASS_TRIGGER = "action-class-trigger";
var STATUT_EFFORT = "normal";
var STATUT_TIME = "";
//les contextes seront à récupérer depuis une TAB ds la BDD associé à chaque ORG
var CONTEXTE_TAGS = ["@Web","@PC","@Téléphone","@KevinD","@JulienG","@DavidD","@Maison","@Bureau"];

var usermoi = '<?php echo "/json/gtd-user".$_GET["id"].".json"; ?>' ; 

$(function(){

  $("#myMOI").fancytree({
    checkbox: true,
	generateIds: true,
    titlesTabbable: true,     // Add all node titles to TAB chain
    source: {
		
      url: usermoi
		
      },
    lazyLoad: function(event, data) {
      data.result = {url: "gtd-user1.json"};
    },
    extensions: ["edit", "dnd", "table", "gridnav", "themeroller", "childcounter", "filter"],
	childcounter: {  //Compteur action par projet
        deep: true,
        hideZeros: true,
        hideExpanded: true
      }, 
	  filter: {
      mode: "hide"
      },
	 select: function(event, data) { //Select = quand on coche la checkbox
	 var selNodes = data.tree.getSelectedNodes();
	 var selkeys = $.map(selNodes, function(node){return node.key;}); //renvoi un tableau
	 var selkey = selkeys[0]; //la key qui nous interesse 
	 var tabselkey = selkey.split('-');
	 var typesel = 0;
	 for(var v=0;v<tabselkey.length;v++){
        typesel++; 
	 }
	 switch( typesel ) {	 
	 case 2: //Un item ou une action unique

	 var namekeysel = tabselkey[1]; 
	 var lettresel = namekeysel.substring(0,1);
		 switch( lettresel ){
		 case "i" :
		 var iditem = namekeysel.substring(1);
		 data.node.remove(); //suppression ds l'arbre
		 alert("Fonction Ajax pour supprimer l'item => ID : "+iditem);
		 break;
		 
		 case "a" :
		 var idaction = namekeysel.substring(1);
		 data.node.remove(); //suppression ds l'arbre
		 alert("Fonction Ajax pour acquitter (statut close) l'action unique => ID : "+idaction);
		 //Idee d'une fonction pour nettoyer tous les 1 mois l'ensemble des actions "close", du genre suppression de toutes les actions "close" de la base de donnée qui ont été terminé il y a plus d'1 mois (vide la table afin de parcourir moins d'actions déjà closé)
		 break;		 
		 }
	 break;
	 
	 case 3: //Une action lie a un projet
	 var namekeysel = tabselkey[2]; 
	 var idaction = namekeysel.substring(1);
	 data.node.remove(); //suppression ds l'arbre
	 alert("Fonction Ajax pour acquitter (statut close) l'action => ID : "+idaction+" lie a un projet");
	 break;
	 }
	 
	 },
	 
    dnd: {
      preventVoidMoves: true,
      preventRecursiveMoves: true,
      autoExpandMS: 400,
      dragStart: function(node, data) { //Permet de définir ce qui peut être dragé
      var keydrag = node.key;
	  var tabdrag = keydrag.split('-'); //On split la KEY au niveau du -
	  var typedrag = 0;
	  for(var j=0;j<tabdrag.length;j++){
        typedrag++;
	  }
	  //alert(typedrag);
	  if(typedrag == 1){ //si c'est un dossier parent Inbox, rôle ou user
	  return false; //on autorise par le drag and drop
	  }
	  else{		
        return true; //dans les autres cas on autorise
	  }
	  
      },
      dragEnter: function(node, data) { //La phase ou on peut dropé
	  //On traite la variable qu'on veut dropé
	  var oldkey = data.otherNode.key; //variable qu'on veut dropé
	  var taboldkey = oldkey.split('-'); //On split la KEY au niveau du -
	  var typeold = 0;
	  for(var g=0;g<taboldkey.length;g++){
        typeold++; 
	  }
	  
	  //On traite la variable ou on est :
	  var dropkey = node.key;  //variable sur laquelle on va drop
	  var tabdropkey = dropkey.split('-');
	  var typedrop = 0;
	  for(var h=0;h<tabdropkey.length;h++){
        typedrop++; 
	  }
	  switch( typedrop ) {
	  case 1: //on va drop au niveau d'un rôle, inbox, ou user
	  var namekeydrop = tabdropkey[0]; 
	  var lettredrop = namekeydrop.substring(0,1);
	  break;
	  
	  case 2: //on va drop au niveau d'un projet, item, ou action unique
	  var namekeydrop = tabdropkey[1];
	  var lettredrop = namekeydrop.substring(0,1);
	  break;
	  
	  case 3: //on va drop au niveau d'une action rattaché à un projet
	  var namekeydrop = tabdropkey[2];
	  var lettredrop = namekeydrop.substring(0,1);
	  break;
	  }
	    
	  //On fait les actions requises en fonction de la variable a dropé
	   switch( typeold ) {
		 case 2:  //si le typeold est 2 on est donc en presence d' : 1 item ou 1 projet ou 1 action unique
		 var nameoldkey = taboldkey[1];
		 var letteroldkey = nameoldkey.substring(0,1);
				switch( letteroldkey ){
				case "p": //Un projet peut être drop seulement dans un rôle et rien d'autre
				if(lettredrop == "r" || lettredrop == "u" || lettredrop == "o"){  
				return ["over"];
				}
				else{
				return false;
				}
				break;
				
				case "i": //Un item peut être dropé dans un projet (il se transforme en action) ou dropé dans un role (il se transforme en projet)
				//alert("on va deplacer "+nameoldkey+" dans "+namekeydrop);
				break;
				
				case "a": //Une action unique peut être dropé dans un rôle ou rattaché à un projet
				if(lettredrop == "r" || lettredrop == "u" || lettredrop == "p"){
				return ["over"];
				}
				else{
				return false;
				}
				break;
				}
		 break;
		 case 3: //si le typeold est 2, on a à faire à une action lié à un projet
		 var nameoldkey = taboldkey[1];
		 if(lettredrop == "r" || lettredrop == "u" || lettredrop == "p"){  
		 return ["over"];
		 }
		 else{
		return false;
		 }
		 break;		 
		 }
      },
      dragDrop: function(node, data) {
		//node.key = l'endroit ou on hit pour drop
		var tabdhitkey = node.key.split('-');
		var typehit = 0;
		var typenamehit = "null";
		for(var e=0;e<tabdhitkey.length;e++){
		typehit++; 
		}
		switch( typehit ) { 
		case 1 :  //rôle ou user
		var namekeyhit = tabdhitkey[0];
		var lettrehit = namekeyhit.substring(0,1);
			switch( lettrehit ) { 
			case "u" : //user
			var idhit = namekeyhit.substring(1); //ID du user
			typenamehit = "user";
			break;
			
			case "r" :
			var idhit = namekeyhit.substring(1); //ID du role
			typenamehit = "role";
			break;
			}
		break;
		
		case 2 : // ID du projet ou dossier one day
		var namekeyhit = tabdhitkey[1];
		var lettrehit = namekeyhit.substring(0,1);
		switch( lettrehit ) { 
			case "p" : //user
			var idhit = namekeyhit.substring(1); //ID du projet
			typenamehit = "projet";
			break;
			
			case "o" :
			var idhit = namekeyhit.substring(1); //ID du dossier oneday
			typenamehit = "oneday";
			break;
			}
		break;
		}
		
		//data.otherNode.key = l'objet qu'on deplace
		var pastekey = data.otherNode.key;
		var tabdpastekey = pastekey.split('-');
		var typepaste = 0;
		for(var k=0;k<tabdpastekey.length;k++){
		typepaste++; 
		}
		switch( typepaste ) { 
		case 2 :
		var namekeypaste = tabdpastekey[1];
		var lettrepaste = namekeypaste.substring(0,1);
		  switch( lettrepaste ){
		  //ID du projet pour un hit dans un role ou user
		  case "p":
		  var idprojet = namekeypaste.substring(1); 
				switch( typenamehit ){
				case "role":
				alert("on envoi le projet : "+idprojet+" dans le role : "+idhit);
				//on fait le drop
				data.otherNode.moveTo(node,data.hitMode);
				//on fait la modification des Keys
				var oldnode = data.otherNode;
				oldnode.key = node.key+"-p"+idprojet;
				oldnode.editEnd();
				//on fait la modification des Keys si il y a des actions pour le projet
				if(oldnode.hasChildren() == true){ 
				var tabactions = oldnode.getChildren(); //on recupere les actions ds un tab		
					for(var m=0;m<tabactions.length;m++){ 
					var nodeaction = tabactions[m]; //le node action
					var idaction = nodeaction.data.ID; //l'ID de l'action
					nodeaction.key = oldnode.key+"-a"+idaction; //sa nouvelle key
					nodeaction.editEnd(); //on enregistre
					}
				}
				break;
				
				case "user":		
				alert("on envoi le projet : "+idprojet+" dans le user : "+idhit+" et on supprime les associations à un projet");
				//on fait le drop
				data.otherNode.moveTo(node,data.hitMode);
				//on fait la modification des Keys
				var oldnode = data.otherNode;
				oldnode.key = node.key+"-p"+idprojet;
				oldnode.editEnd();
				//on fait la modification des Keys si il y a des actions pour le projet
				if(oldnode.hasChildren() == true){ 
				var tabactions = oldnode.getChildren(); //on recupere les actions ds un tab		
					for(var m=0;m<tabactions.length;m++){ 
					var nodeaction = tabactions[m]; //le node action
					var idaction = nodeaction.data.ID; //l'ID de l'action
					nodeaction.key = oldnode.key+"-a"+idaction; //sa nouvelle key
					nodeaction.editEnd(); //on enregistre
					}
				}
				break;	

				case "oneday":	
				data.otherNode.data.statut = STATUT_ONEDAY; //on change le statut
				data.otherNode.extraClasses = EXTRACLASS_ONEDAY; //on change la class
				//suppression des actions filles
				data.otherNode.moveTo(node, data.hitMode);
				alert("on envoi le projet : "+idprojet+" dans le dossieroneday du role : "+idhit);
				
				break;
				}
		  break;
		  //ID de l'action pour hit dans un projet, role ou user
		  case "a":
		  var idaction = namekeypaste.substring(1); 
			  switch( typenamehit ){
					case "role":
					alert("on envoi l'action : "+idaction+" dans le role : "+idhit);
					data.otherNode.moveTo(node, data.hitMode);
					//on fait la modification de la Key
					var oldnode = data.otherNode;
					oldnode.key = node.key+"-a"+idaction;
					oldnode.editEnd();				
					break;
					
					case "user":
					alert("on envoi l'action : "+idaction+" dans le user : "+idhit);
					data.otherNode.moveTo(node, data.hitMode);
					//on fait la modification de la Key
					var oldnode = data.otherNode;
					oldnode.key = node.key+"-a"+idaction;
					oldnode.editEnd();	
					break;	

					case "projet":
					alert("on envoi l'action : "+idaction+" dans le projet : "+idhit);
					data.otherNode.moveTo(node, data.hitMode);
					//on fait la modification de la Key
					var oldnode = data.otherNode;
					oldnode.key = node.key+"-a"+idaction;
					oldnode.editEnd();	
					break;	
					}
		  break;
		  }
		break;
		
		case 3 :
		var namekeypaste = tabdpastekey[2];
		var idaction = namekeypaste.substring(1);
		switch( typenamehit ){
					case "role":
					alert("on envoi l'action : "+idaction+" dans le role : "+idhit);
					data.otherNode.moveTo(node, data.hitMode);
					//on fait la modification de la Key
					var oldnode = data.otherNode;
					oldnode.key = node.key+"-a"+idaction;
					oldnode.editEnd();	
					break;
					
					case "user":
					alert("on envoi l'action : "+idaction+" dans le user : "+idhit);
					data.otherNode.moveTo(node, data.hitMode);
					//on fait la modification de la Key
					var oldnode = data.otherNode;
					oldnode.key = node.key+"-a"+idaction;
					oldnode.editEnd();		
					break;	

					case "projet":
					alert("on envoi l'action : "+idaction+" dans le projet : "+idhit);
					data.otherNode.moveTo(node, data.hitMode);
					//on fait la modification de la Key
					var oldnode = data.otherNode;
					oldnode.key = node.key+"-a"+idaction;
					oldnode.editEnd();	
					break;	
					}
		break;		
		}
		
		//node.key = l'endroit ou on drop
		//alert("ds dragdrop on bouge :"+data.otherNode.key+" vers le hit :"+node.key);
      }
    },
    edit: {	
    },
    gridnav: {
      autofocusInput: false,
      handleCursorKeys: true
    },
	
	activate: function(e, data) {
	  var node = data.node;
	  var key = node.key;
	  var contextes = node.data.contexte; //recuperation des contextes pour la node
	  var notes = node.data.notes; //recuperation des notes pour la node
	  var roletension = node.data.role; //recuperation du role pour la node tension
	  var priority = node.data.priority; //recuperation de la priorite pour la node
	  var ID = node.data.ID; //recuperation pour l'ID
	  var statut = node.data.statut; //recuperation pour statut
	  var time = node.data.time; //recuperation pour les actions du Time
	  var effort = node.data.effort; //recuperation pour les actions de l'effort
	  $(".titleactive").html(node.title); //on affiche le titre de la valeur actuel
	  
	  //Gestion de la partie ROLES TENSION
	  if(roletension != null){ 
	  var lettreuser = roletension.substring(0,1); //on recup la 1er lettre
	  var tabroletension = roletension.split('-'); //on split
		  if(lettreuser == "u"){ //on est en presence d'une tension affecté au user directement
		  var namerole = tabroletension[1];
		  var iconerole = "<span class='user-class'></span>"
		  }
		  else{ //c'est une tension pour un role
		  var namerole = tabroletension[1];
		  var iconerole = "<span class='role-class'></span>"
		  }  
	  $('#rolestension').removeClass("tensionhide"); //on enleve le css qui cache
	  txttension = "<strong>Affect&eacute; &agrave; :</strong> "+iconerole+namerole+"<button id='editroletension' onclick='$(this).ClickEditRole(\""+ID+"\");' class='ui-button ui-widget ui-state-default ui-corner-all ui-button-text-onlyui-button ui-widget ui-state-default ui-corner-all ui-button-text-only'>Editer</button>";
	  $("#rolestension").html(txttension);} //on affiche les notes de l'objet selectionné
			else{ $('#rolestension').addClass("tensionhide"); }
			
	  //Gestion de la partie NOTES
	  if(notes != null){ 
	  var type = "p";
	  $('#notes').removeClass("noteshide");
	  node = $("#myMOI").fancytree("getActiveNode"); //On recupère la node qui est actuellement selectionne et on l'associe au textarea
	  var ID = node.data.ID;
	  if(time != null){ type = "a";} //Si il y a du time c'est une action 
	  if(roletension != null){ type = "t";} //Si il y a du roletension c'est une tension 
	  var typeID = type+""+ID;
	  var fcttypeID = "\""+typeID+"\"";
	  key = "\""+key+"\"";
	  txtnotes = "<textarea id='"+typeID+"'class='areanotes' onblur='$(this).UpdateNotes(this.value,"+fcttypeID+","+key+");'>"+notes+"</textarea>";
	  $("#notes").html(txtnotes);} //on affiche les notes de l'objet selectionné
	  else{ $('#notes').addClass("noteshide"); }
	  
	  //Gestion de la partie TIME
	  if(time != null){ //Si on a time qui est pas null
		  switch (time){
		  case "<30min":
		  $("#time").html("<strong>Temps : </strong><select onchange='$(this).ChangeTime(this.value);'><option  value='-'>-</option><option selected value='<30min'><30min</option><option value='<60min' ><60min</option><option value='<2h'><2h</option><option value='<4h'><4h</option><option value='<1j'><1j</option></select>");	
		  break;
		  case "<60min":
		  $("#time").html("<strong>Temps : </strong><select onchange='$(this).ChangeTime(this.value);'><option value='-'>-</option><option value='<30min' ><30min</option><option selected value='<60min' ><60min</option><option value='<2h'><2h</option><option  value='<4h'><4h</option><option value='<1j'><1j</option></select>");
		  break;
		  case "<2h":
		  $("#time").html("<strong>Temps : </strong><select onchange='$(this).ChangeTime(this.value);'><option value='-'>-</option><option value='<30min' ><30min</option><option  value='<60min' ><60min</option><option selected value='<2h'><2h</option><option value='<4h'><4h</option><option value='<1j'><1j</option></select>");
		  break;
		  case "<4h":
		  $("#time").html("<strong>Temps : </strong><select onchange='$(this).ChangeTime(this.value);'><option value='-'>-</option><option value='<30min' ><30min</option><option value='<60min' ><60min</option><option value='<2h'><2h</option><option selected value='<4h'><4h</option><option value='<1j'><1j</option></select>");
		  break;
		  case "<1j":
		  $("#time").html("<strong>Temps : </strong><select onchange='$(this).ChangeTime(this.value);'><option value='-'>-</option><option value='<30min' ><30min</option><option value='<60min' ><60min</option><option value='<2h'><2h</option><option value='<4h'><4h</option><option selected value='<1j'><1j</option></select>");
		  break;
		  default:
		  $("#time").html("<strong>Temps : </strong><select onchange='$(this).ChangeTime(this.value);'><option selected value='-'>-</option><option  value='<30min' ><30min</option><option value='<60min' ><60min</option><option value='<2h'><2h</option><option value='<4h'><4h</option><option value='<1j'><1j</option></select>");
		  break;
		  }	
	 }
	 else{ $("#time").html(""); } //On affiche rien si il y a pas de time
	 
	 //Gestion de la partie EFFORT
	  if(effort != null){ //Si on a effort qui est pas null		  
		  switch (effort){
		  case "faible":
		  $("#effort").html("<strong>Effort : </strong><select onchange='$(this).ChangeEffort(this.value);'><option selected value='faible'>faible</option><option value='normal'>normal</option><option value='elevee'>elevee</option><select>");	
		  break;
		  case "elevee":
		  $("#effort").html("<strong>Effort : </strong><select onchange='$(this).ChangeEffort(this.value);'><option value='faible'>faible</option><option value='normal'>normal</option><option selected value='elevee'>elevee</option><select>");	
		  break;
		  default:
		  $("#effort").html("<strong>Effort : </strong><select onchange='$(this).ChangeEffort(this.value);'><option  value='faible'>faible</option><option selected value='normal'>normal</option><option value='elevee'>elevee</option><select>");	
		  break;
		  }
	  }
	else{ $("#effort").html(""); } //On affiche rien si il y a pas de effort
	
	  //Gestion de la partie STATUT / PRIORITAIRE et EFFORT / TIME
	  if(statut != null){ //Si on a un statut
		  //Cas pour une action
		  if(statut == 3 || statut == 5 || statut == 6){
		  if(statut == 3){var statutext = "A Faire"; var inputtodo = "<input type='radio' name='statutradio' id='todo' value='todo' onclick='$(this).ChangeStatutAction(\"3\");' checked>A Faire ";} else {var inputtodo = "<input type='radio' name='statutradio' id='todo' value='todo' onclick='$(this).ChangeStatutAction(\"3\");'>A Faire ";}
		  if(statut == 5){ var statutext = "Bloqu&eacute;"; var inputblock = "<input type='radio' name='statutradio' id='block' value='block' onclick='$(this).ChangeStatutAction(\"5\");' checked>Bloqu&eacute; ";} else {var inputblock = "<input type='radio' onclick='$(this).ChangeStatutAction(\"5\");' name='statutradio' id='block' value='block'>Bloqu&eacute; ";}
		  if(statut == 6){ var statutext = "D&eacute;clench&eacute;"; var inputtrigger = "<input type='radio' name='statutradio' id='trigger' value='trigger' onclick='$(this).ChangeStatutAction(\"6\");' checked>D&eacute;clench&eacute; <br/>Champ pour rentrer la date";} else {var inputtrigger = "<input type='radio' onclick='$(this).ChangeStatutAction(\"6\");' name='statutradio' id='trigger' value='trigger'>D&eacute;clench&eacute; ";}
		  $("#statut").html("<form class='statutradio'><div class='statuttitle'><strong>Statut :</strong> "+statutext+"</div>"+inputtodo+inputblock+inputtrigger);	
		  }
	  
		  //Cas pour un projet et priorite du projet
		  if(statut == 1 || statut == 2 || statut == 4 || statut == 8){
		  //on definit la priorite
		  if(priority != null){ 
		  var txtpriority = "<input checked onclick='$(this).ChangePriorityProjet(\"false\");' type='checkbox'/> Prioritaire"; //Projet prioritaire
		  } else{var txtpriority = "<input onclick='$(this).ChangePriorityProjet(\"true\");' type='checkbox'/> Prioritaire";}
		  //On cree les boutons
		  if(statut == 1){var statutext = "En Cours"; var inputrun = "<input type='radio' id='run' onclick='$(this).ChangeStatutProjet(\"1\");' name='statutradio' value='run' checked>En Cours ";} else {var inputrun = "<input type='radio' name='statutradio' onclick='$(this).ChangeStatutProjet(\"1\");' id='run' value='run'>En Cours ";}
		  if(statut == 2){var statutext = "En Attente"; var inputwait = "<input type='radio' id='wait' name='statutradio' value='wait' onclick='$(this).MessageBox('msg');' onclick='$(this).ChangeStatutProjetProjet(\"2\");' checked>En Attente ";} else {var inputwait = "<input type='radio' name='statutradio' id='wait' value='wait' onclick='$(this).ChangeStatutProjet(\"2\");'>En Attente ";}
		  if(statut == 4){var statutext = "Termin&eacute;"; var inputdone = "<input type='radio' name='statutradio' id='done' onclick='$(this).ChangeStatutProjet(\"4\");' value='done' checked>Termin&eacute; ";} else {var inputdone = "<input type='radio' name='statutradio' id='done' value='done' onclick='$(this).ChangeStatutProjet(\"4\");'>Termin&eacute; ";}
		  if(statut == 8){var statutext = "Un jour peut etre"; var inputoneday = "<input type='radio' name='statutradio' id='oneday' onclick='$(this).ChangeStatutProjet(\"8\");' value='oneday' checked>Un jour peut etre ";} else {var inputoneday = "<input type='radio' name='statutradio' id='oneday' onclick='$(this).ChangeStatutProjet(\"8\");' value='oneday'>Un jour peut etre ";}
		  //on affiche le formulaire avec les statuts
		  $("#statut").html("<form class='statutradio'><div class='statuttitle'><strong>Statut :</strong> "+statutext+"<div id='priority' style='margin-right:40px;float:right;'>"+txtpriority+"</div></div>"+inputrun+inputwait+"<br/>"+inputdone+inputoneday+"</form>");
		  }
	  } 
	  else{$("#statut").html(""); } //On affiche rien si c'est pas une action ou un projet
	   
	  //Gestion de la partie CONTEXTE
	  if(contextes != null){
	  var tabcontexte = contextes.split(" "); //Split des contextes
	  var nb = 0; 
	  var txtcontextold = "";
	  for(var nb=0;nb<tabcontexte.length;nb++){  //on determine le type de la key
	  var contexte = tabcontexte[nb]; //@web 
	  var imgcontexte = "\""+contexte+"\"";
	  var idcontexte = contexte.substring(1);
	  var txtcontext = "<div id='"+idcontexte+"'class='contxt'>"+contexte+"<img src='plugins/fancytree/skin-themeroller/delete.png' style='vertical-align:middle;cursor:pointer;margin-top:-1px;' onclick='$(this).DeleteContexte("+imgcontexte+");'></div>"; //@web x 
	  var txtcontextfinal = txtcontextold+""+txtcontext;  
	  txtcontextold = txtcontextfinal;
	  }
	  //contextesactuels
	  $('#contexteinput').removeClass("hidecontexte"); //on add la class
	  $("#contextesactuels").html(txtcontextfinal);
	  }
	  else{$('#contexteinput').addClass("hidecontexte"); $("#contextesactuels").html(""); } //On affiche rien si c'est pas une action ou un projet 
	  
	  
      },
    renderColumns: function(event, data) {
      var node = data.node,
        $tdList = $(node.tr).find(">td");
      $tdList.eq(1).text(node.data.contexte);
	  $tdList.eq(2).text(node.data.effort);
	  $tdList.eq(3).text(node.key);
	  $tdList.eq(4).text(node.data.ID);
    }
  }).on("nodeCommand", function(event, data){
    // Custom event handler that is triggered by keydown-handler and
    // context menu:
    var refNode, moveMode,
      tree = $(this).fancytree("getTree"),
      node = tree.getActiveNode();

    switch( data.cmd ) {
    case "moveUp":
      node.moveTo(node.getPrevSibling(), "before");
      node.setActive();
      break;
    case "moveDown":
      node.moveTo(node.getNextSibling(), "after");
      node.setActive();
      break;
    case "indent":
      refNode = node.getPrevSibling();
      node.moveTo(refNode, "child");
      refNode.setExpanded();
      node.setActive();
      break;
    case "outdent":
      node.moveTo(node.getParent(), "after");
      node.setActive();
      break;
    case "rename":
      node.editStart();
      break;
    case "remove":
	  var deletekey = node.key //on recupere la key
	  var tabdelkey = deletekey.split('-');
	  var typedelete = 0; 
	  for(var f=0;f<tabdelkey.length;f++){  //on determine le type de la key
      typedelete++; 
	  }
	  switch( typedelete ) {
	  case 1:
	  alert("Vous n'avez pas l'autorisation pour supprimer ce dossier");
	  break;
	  
	  case 2:
	  var namekeydel = tabdelkey[1];
	  var lettresdel = namekeydel.substring(0,1);
		  switch( lettresdel ){
		  case "p": //Supprimer le projet et ses actions filles
				var idprojet = namekeydel.substring(1);
				alert("Fonction Ajax pour supprimer le projet (statut delete) => ID : "+idprojet+" ainsi que ses actions filles");
				//on fait la modification des Keys si il y a des actions pour le projet
				if(node.hasChildren() == true){ 
				var tabfillesactions = node.getChildren(); //on recupere les actions ds un tab		
					for(var b=0;b<tabfillesactions.length;b++){ 
					var nodefille = tabfillesactions[b]; //le node action
					var idaction = nodefille.data.ID; //l'ID de l'action
					alert("Fonction Ajax pour supprimer l'action fille => ID : "+idaction+" associe au projet => ID : "+idprojet);
					}
				}
				node.remove();				
				break;
		  case "i": //Supprime un item
				var iditem = namekeydel.substring(1);
				node.remove();
				alert("Fonction Ajax pour supprimer l'item => ID : "+iditem);
				break;
				
		  case "a": //Supprime l'action unique
				var idaction = namekeydel.substring(1);
				node.remove();
				alert("Fonction Ajax pour supprimer (statut delete) l'action unique => ID : "+idaction);
				break;
		  }
	  break;	
	  
	  case 3: //Supprime l'action d'un projet
	  var namekeydel = tabdelkey[2];
	  var idaction = namekeydel.substring(1);
	  alert("Fonction Ajax pour supprimer l'action (statut delete) => ID : "+idaction+" associe à un projet");
	  node.remove();
	  break;	  
	  }	  
      break;
    case "newaction":
	  var role = node.key;
	   var chars = "ABCDEFGHIJKLMNOPQRSTUVWXTZ";
	  var string_length = 2;
	  var randomstring = '';
	  for (var i=0; i<string_length; i++) {
		var rnum = Math.floor(Math.random() * chars.length);
		randomstring += chars.substring(rnum,rnum+1);
	  }
	  var newidaction =  event.timeStamp+""+randomstring;
      refNode = node.addChildren({
        title: "Nouvel action",
		key: role+"-a"+newidaction,
		extraClasses: EXTRACLASS_TODO,	
		statut: STATUT_TODO,
		effort:STATUT_EFFORT,
		time:STATUT_TIME,
		notes:STATUT_NOTES,
		ID: newidaction,
        isNew: true
      });
      node.setExpanded();
      refNode.editStart();
	  alert("Nouveau projet : "+title+" ID : "+newidaction+ " affecte au role : "+role+" et statut A faire par defaut");
      break;
	 case "newitem":
      refNode = node.addChildren({
        title: "Nouvel item",
        isNew: true
      });
      node.setExpanded();
      refNode.editStart();
      break; 
	case "newprojet":
	  var role = node.key;
	  var chars = "ABCDEFGHIJKLMNOPQRSTUVWXTZ";
	  var string_length = 2;
	  var randomstring = '';
	  for (var i=0; i<string_length; i++) {
		var rnum = Math.floor(Math.random() * chars.length);
		randomstring += chars.substring(rnum,rnum+1);
	  }
	  var newidprojet =  event.timeStamp+""+randomstring;
      refNode = node.addChildren({
        title: "Nouveau projet",
		key: role+"-p"+newidprojet,
		extraClasses: EXTRACLASS_RUN,	
		statut: STATUT_RUN,
		notes:STATUT_NOTES,
		ID: newidprojet,
        isNew: true
      });
      node.setExpanded();
      refNode.editStart();
	  var newtitle = refNode.title;
	  alert("Nouveau projet : "+newtitle+" ID : "+newidprojet+ " affecte au role : "+role+" et statut "+STATUT_RUN+" par defaut");
      break;
	case "newprojetoneday":
	 var role = node.key;
	 var tabone = role.split('-');
	 role = tabone[0]; //on recupere juste rXX
	  var chars = "ABCDEFGHIJKLMNOPQRSTUVWXTZ";
	  var string_length = 2;
	  var randomstring = '';
	  for (var i=0; i<string_length; i++) {
		var rnum = Math.floor(Math.random() * chars.length);
		randomstring += chars.substring(rnum,rnum+1);
	  }
	  var newidprojet =  event.timeStamp+""+randomstring;
	  
      refNode = node.addChildren({
        title: "Nouveau projet Un jour peut être",
		key: role+"-p"+newidprojet,
		extraClasses: "projet-class-oneday",	
		statut: "Un jour peut etre",
		statut: STATUT_ONEDAY,
		notes:STATUT_NOTES,
		ID: newidprojet,
        isNew: true
      });
      node.setExpanded();
      refNode.editStart();
      break;
    case "cut":
      CLIPBOARD = {mode: data.cmd, data: node};
      break;
    case "copy":
      CLIPBOARD = {
        mode: data.cmd,
        data: node.toDict(function(n){
          delete n.key;
        })
      };
      break;
    case "clear":
      CLIPBOARD = null;
      break;
    case "paste":
      if( CLIPBOARD.mode === "cut" ) {
        // refNode = node.getPrevSibling();
        CLIPBOARD.data.moveTo(node, "child");
        CLIPBOARD.data.setActive();
      } else if( CLIPBOARD.mode === "copy" ) {
        node.addChildren(CLIPBOARD.data).setActive();
      }
      break;
    default:
      alert("Unhandled command: " + data.cmd);
      return;
    }

  }).on("keydown", function(e){
    var c = String.fromCharCode(e.which),
      cmd = null;

    if( c === "N" && e.ctrlKey && e.shiftKey) {
      cmd = "addChild";
    } else if( c === "C" && e.ctrlKey ) {
      cmd = "copy";
    } else if( c === "V" && e.ctrlKey ) {
      cmd = "paste";
    } else if( c === "X" && e.ctrlKey ) {
      cmd = "cut";
    } else if( c === "N" && e.ctrlKey ) {
      cmd = "addSibling";
    } else if( e.which === $.ui.keyCode.DELETE ) {
      cmd = "remove";
    } else if( e.which === $.ui.keyCode.F2 ) {
      cmd = "rename";
    } else if( e.which === $.ui.keyCode.UP && e.ctrlKey ) {
      cmd = "moveUp";
    } else if( e.which === $.ui.keyCode.DOWN && e.ctrlKey ) {
      cmd = "moveDown";
    } else if( e.which === $.ui.keyCode.RIGHT && e.ctrlKey ) {
      cmd = "indent";
    } else if( e.which === $.ui.keyCode.LEFT && e.ctrlKey ) {
      cmd = "outdent";
    }
    if( cmd ){
      $(this).trigger("nodeCommand", {cmd: cmd});
      return false;
    }
  });

  /*
   * Context menu (https://github.com/mar10/jquery-ui-contextmenu)
   */ 
	    

   
  $("#myMOI").contextmenu({
      select: "span.fancytree-title",
    menu: [
      {title: "Nouveau projet", cmd: "newprojet", uiIcon: "ui-icon-plus" },
	  {title: "Nouveau projet Un jour peut etre", cmd: "newprojetoneday", uiIcon: "ui-icon-plus" },
	  {title: "Nouvel item", cmd: "newitem", uiIcon: "ui-icon-plus" },
      {title: "Nouvelle action", cmd: "newaction", uiIcon: "ui-icon-arrowreturn-1-e" },
	  {title: "Renomer", cmd: "rename", uiIcon: "ui-icon-pencil" },
	  {title: "Supprimer", cmd: "remove", uiIcon: "ui-icon-trash" },
      {title: "Cut", cmd: "cut", uiIcon: "ui-icon-scissors"},
      {title: "Copy", cmd: "copy", uiIcon: "ui-icon-copy"},
      {title: "Paste", cmd: "paste", uiIcon: "ui-icon-clipboard", disabled: true }
      ],
	   beforeOpen: function(event, ui) {
	   var node = $.ui.fancytree.getNode(ui.target);
	   node.setActive(); //met la classe fancytree-active
	   var key = node.key;
	   var tab = key.split('-'); //On split la KEY au niveau du -
	   var type = 0;
	   for(var i=0;i<tab.length;i++){
        type++;
		}
		 switch( type ) {
		 case 1:
		 //alert("ds role");
		 var projetouaction = tab[0];
		 var lettre = projetouaction.substring(0,1);
		 if( key == "box"){ //la box
		 $("#myMOI").contextmenu("showEntry", "remove", false); //cache le remove
		 $("#myMOI").contextmenu("showEntry", "newprojetoneday", false); //cache le remove
			 $("#myMOI").contextmenu("showEntry", "rename", false); //cache le rename
			 $("#myMOI").contextmenu("showEntry", "copy",  false); //cache le rename
			 $("#myMOI").contextmenu("showEntry", "cut", false); //cache le rename
			 $("#myMOI").contextmenu("showEntry", "paste", false); //cache le rename
			 $("#myMOI").contextmenu("showEntry", "newprojet", false); //cache le remove
			 $("#myMOI").contextmenu("showEntry", "newaction", false); //cache le rename
			  $("#myMOI").contextmenu("showEntry", "newitem", true); //cache le rename
		 }
			 else{ //role
			 $("#myMOI").contextmenu("showEntry", "remove", false); //cache le remove
			 $("#myMOI").contextmenu("showEntry", "newprojetoneday", false); //cache le remove
			 $("#myMOI").contextmenu("showEntry", "rename", false); //cache le rename
			 $("#myMOI").contextmenu("showEntry", "copy",  false); //cache le rename
			 $("#myMOI").contextmenu("showEntry", "cut", false); //cache le rename
			 $("#myMOI").contextmenu("showEntry", "paste", false); //cache le rename
			  $("#myMOI").contextmenu("showEntry", "newprojet", true); //cache le remove
		 $("#myMOI").contextmenu("showEntry", "newaction", true); //cache le remove
		 $("#myMOI").contextmenu("showEntry", "newitem", false); //cache le rename
			 }
		 break;
		 case 2:
		 
		 var projetouaction = tab[1];
		 var lettre = projetouaction.substring(0,1);
		 if(lettre == "a" || lettre == "i" ){ //Action unique
		  $("#myMOI").contextmenu("showEntry", "remove", true); //cache le remove
		  $("#myMOI").contextmenu("showEntry", "newprojetoneday", false); //cache le remove
		 $("#myMOI").contextmenu("showEntry", "rename", true); //cache le remove
		 $("#myMOI").contextmenu("showEntry", "newprojet", false); //cache le remove
			 $("#myMOI").contextmenu("showEntry", "copy",  false); //cache le rename
			 $("#myMOI").contextmenu("showEntry", "cut", false); //cache le rename
			 $("#myMOI").contextmenu("showEntry", "paste", false); //cache le rename
			  $("#myMOI").contextmenu("showEntry", "newitem", false); //cache le rename
			  $("#myMOI").contextmenu("showEntry", "newaction", false); //cache le remove
		 }	 
		 
		else{ 
			//Projet ou Dossier "oneday"
			if(lettre == "p"){
			 $("#myMOI").contextmenu("showEntry", "remove", true); //cache le remove
			 $("#myMOI").contextmenu("showEntry", "newprojetoneday", false); //cache le remove
			 $("#myMOI").contextmenu("showEntry", "rename", true); //cache le remove
			 $("#myMOI").contextmenu("showEntry", "newprojet", false); //cache le remove
			 $("#myMOI").contextmenu("showEntry", "copy",  false); //cache le rename
			 $("#myMOI").contextmenu("showEntry", "cut", false); //cache le rename
			 $("#myMOI").contextmenu("showEntry", "paste", false); //cache le rename
			 $("#myMOI").contextmenu("showEntry", "newitem", false); //cache le rename
			 $("#myMOI").contextmenu("showEntry", "newaction", true); //cache le remove
			 }
			 if(lettre == "o"){
			 $("#myMOI").contextmenu("showEntry", "remove", false); //cache le remove
			 $("#myMOI").contextmenu("showEntry", "newprojetoneday", true); //cache le remove
			 $("#myMOI").contextmenu("showEntry", "rename", false); //cache le remove
			 $("#myMOI").contextmenu("showEntry", "newprojet", false); //cache le remove
			 $("#myMOI").contextmenu("showEntry", "copy",  false); //cache le rename
			 $("#myMOI").contextmenu("showEntry", "cut", false); //cache le rename
			 $("#myMOI").contextmenu("showEntry", "paste", false); //cache le rename
			 $("#myMOI").contextmenu("showEntry", "newitem", false); //cache le rename
			 $("#myMOI").contextmenu("showEntry", "newaction", false); //cache le remove
			 }
		}	 
		 break;
		 case 3:
		 //Action ds un projet
		 $("#myMOI").contextmenu("showEntry", "remove", true); //cache le remove
		 $("#myMOI").contextmenu("showEntry", "newprojetoneday", false); //cache le remove
		 $("#myMOI").contextmenu("showEntry", "rename", true); //cache le remove
		 $("#myMOI").contextmenu("showEntry", "newprojet", false); //cache le remove
			 $("#myMOI").contextmenu("showEntry", "copy",  false); //cache le rename
			 $("#myMOI").contextmenu("showEntry", "cut", false); //cache le rename
			 $("#myMOI").contextmenu("showEntry", "paste", false); //cache le rename
			  $("#myMOI").contextmenu("showEntry", "newitem", false); //cache le rename
			  $("#myMOI").contextmenu("showEntry", "newaction", false); //cache le remove
		 break;
		 }
        },
		select: function(event, ui) {
      var that = this;
      // delay the event, so the menu can close and the click event does
      // not interfere with the edit control
      setTimeout(function(){
        $(that).trigger("nodeCommand", {cmd: ui.cmd});
      }, 100);
    }
  }); 
  
  //Look des bouttons fun
  $("button").button();
  
  //Fonction bouton pour reload de la page 
  $("button#reload").click(function(event){
  alert("lancement de la fonction en Ajax pour re-construire le fichier JSON du user connecte");
  location.reload(true);
  });
  
  //Fonction bouton pour editer la tension associe à un role
  $.fn.ClickEditRole = function(ID){
  alert("Formulaire pour changer le role affecte a la tension "+ID);
  }
  
  //Fonction pour changer le statut d'une action
  $.fn.ChangeStatutAction = function(statut){		
  node = $("#myMOI").fancytree("getActiveNode"); //On recupère la node qui est actuellement selectionne
  var ID = node.data.ID;
  alert("on va changer ds la BDD le statut de l'action : "+ID+" avec le statut : "+statut);
  switch(statut){
  case "3":
  node.data.statut = STATUT_TODO;
  node.extraClasses = EXTRACLASS_TODO;
  node.editEnd(); //on fait une save
  node.setActive();
  //on efface le statut pour mettre à jour le nouveau
  $("#statut .statuttitle").empty();
  $("#statut .statuttitle").append("<strong>Statut :</strong> A Faire");
  break;
  case "5":
  node.data.statut = STATUT_BLOCK;
  node.extraClasses = EXTRACLASS_BLOCK;
  node.editEnd(); //on fait une save
  node.setActive();
  //on efface le statut pour mettre à jour le nouveau
  $("#statut .statuttitle").empty();
  $("#statut .statuttitle").append("<strong>Statut :</strong> Bloqu&eacute;");
  break; 
  case "6":
  node.data.statut = STATUT_TRIGGER;
  node.extraClasses = EXTRACLASS_TRIGGER;
  node.editEnd(); //on fait une save
  node.setActive();
  //on efface le statut pour mettre à jour le nouveau
  $("#statut .statuttitle").empty();
  $("#statut .statuttitle").append("<strong>Statut :</strong> D&eacute;clench&eacute;");
  $("#statut .dateaction").empty();
  $("#statut .dateaction").append("Champ pour la date");
  alert("faire apparaitre le mini calendrier pour saisir une date");
  
  break; 
  }	
  }
  
  //Fonction pour changer le statut d'un projet
  $.fn.ChangeStatutProjet = function(statut){	
  node = $("#myMOI").fancytree("getActiveNode"); //On recupère la node qui est actuellement selectionne
  var ID = node.data.ID;
  alert("on va changer ds la BDD le statut du projet : "+ID+" avec le statut : "+statut);
  switch(statut){
  case "1":
  node.data.statut = STATUT_RUN;
  node.extraClasses = EXTRACLASS_RUN;
  node.editEnd(); //on fait une save
  node.setActive();
  //on efface le statut pour mettre à jour le nouveau
  $("#statut .statuttitle").empty();
  $("#statut .statuttitle").append("<strong>Statut :</strong> En Cours");
  break;
  case "2":
  node.data.statut = STATUT_WAIT;
  node.extraClasses = EXTRACLASS_WAIT;
  node.editEnd(); //on fait une save
  node.setActive();
    //on efface le statut pour mettre à jour le nouveau
  $("#statut .statuttitle").empty();
  $("#statut .statuttitle").append("<strong>Statut :</strong> En Attente");
  break;
  case "4":
  node.data.statut = STATUT_DONE;
  node.extraClasses = EXTRACLASS_DONE;
  node.editEnd(); //on fait une save
  node.setActive();
    //on efface le statut pour mettre à jour le nouveau
  $("#statut .statuttitle").empty();
  $("#statut .statuttitle").append("S<strong>Statut :</strong> Termin&eacute;");
  break;
  case "8":
  node.data.statut = STATUT_ONEDAY;
  node.extraClasses = EXTRACLASS_ONEDAY;
  node.editEnd(); //on fait une save
  node.setActive();
    //on efface le statut pour mettre à jour le nouveau
  $("#statut .statuttitle").empty();
  $("#statut .statuttitle").append("<strong>Statut :</strong> Un jour peut etre");
  break;
  }
  }
  
  //Fonction pour sauvegarder les notes
  $.fn.UpdateNotes = function(txtnotes,typeID,keynode){ 
  var ID = typeID.substring(1); //ID de l'action ou du projet
  var typenotes = typeID.substring(0,1); //a pour action , p pour projet
  node = $("#myMOI").fancytree("getNodeByKey",keynode); //On recupère la node via la Key
  node.data.notes = txtnotes;
  node.editEnd(); //on fait une save
  alert("on va changer ds la BDD les notes du "+typenotes+" : "+ID+" avec le texte suivant : <br/>"+txtnotes);
  }
  
  //Fonction pour changer la priorité d'un projet
  $.fn.ChangePriorityProjet = function(prioritystatut){
  node = $("#myMOI").fancytree("getActiveNode"); //On recupère la node qui est actuellement selectionne
  var ID = node.data.ID;
  if(prioritystatut == "true"){ //Si le projet était prioritaire, on décoche pour le rendre non prioritaire
  alert("Statut prioritaire a mettre true ds la BDD pour le projet : "+ID);
  node.data.priority = "true";
  node.editEnd(); //on fait une save
  //on efface le statut pour mettre à jour le nouveau
  alert("pensez a ajouter la classe pour les fils");
  $("#statut #priority").empty();
  $("#statut #priority").append("<input checked onclick='$(this).ChangePriorityProjet(\"false\");' type='checkbox'/> Prioritaire");
  }
  else{ //On va mettre le projet en prioritaire
  alert("Statut prioritaire a mettre false ds la BDD pour le projet : "+ID);
  node.data.priority = "false";
  node.editEnd(); //on fait une save	
  //on efface le statut pour mettre à jour le nouveau
  alert("pensez a effacer la classe pour les fils");
  $("#statut #priority").empty();
  $("#statut #priority").append("<input onclick='$(this).ChangePriorityProjet(\"true\");' type='checkbox'/> Prioritaire");
  }
  }
  
  //Fonction pour changer le time d'une action
  $.fn.ChangeTime = function(timedata){
  node = $("#myMOI").fancytree("getActiveNode"); //On recupère la node qui est actuellement selectionne
  var ID = node.data.ID;
  alert("valeur saisi : "+timedata+" pour l'action ID : "+ID);
  node.data.time = timedata;
  node.editEnd(); //on fait une save	
  }
  
  //Fonction pour changer l'effort d'une action
  $.fn.ChangeEffort = function(effortdata){
  node = $("#myMOI").fancytree("getActiveNode"); //On recupère la node qui est actuellement selectionne
  var ID = node.data.ID;
  alert("valeur saisi : "+effortdata+" pour l'action ID : "+ID);
  node.data.effort = effortdata;
  node.editEnd(); //on fait une save	
  }
  
  //Fonctions pour le filtre de l'arbre
  var treefilter = $("#myMOI").fancytree("getTree"); //on cree l'arbre pour le filtre
  $("input[name=search]").keyup(function(e){
      var match = $(this).val();
      if(e && e.which === $.ui.keyCode.ESCAPE || $.trim(match) === ""){
        $("button#btnResetSearch").click();
        return;
      }
      // Pass text as filter string (will be matched as substring in the node title)
      var n = treefilter.applyFilter(match);
      $("button#btnResetSearch").attr("disabled", false);
      $("span#matches").text(+n+ " trouvé(s)");
    }).focus();

	$("button#btnResetSearch").click(function(e){
      $("input[name=search]").val("");
      $("span#matches").text("");
      treefilter.clearFilter();
    }).attr("disabled", true);
	
	
  //Fonction pour changer l'effort d'une action
  $.fn.DeleteContexte = function(contextetodelete){
  node = $("#myMOI").fancytree("getActiveNode"); //On recupère la node qui est actuellement selectionne
  var ID = node.data.ID;
  var oldcontexte = node.data.contexte;
  var idcontextetodelete = contextetodelete.substring(1);
  
  var newcontexte = "";
  var cmpt = 0;
  var taboldcontexte = oldcontexte.split(' ');
  for(var j=0;j<taboldcontexte.length;j++){
  if(taboldcontexte[j] == contextetodelete){ 
  //on fait rien
  } 
	else{  //on poursuit la chaîne
		if(cmpt == 0){ //si c'est le premier
		newcontexte = taboldcontexte[j];
		cmpt++;
		}
		else{newcontexte = newcontexte+" "+taboldcontexte[j];} //sinon on fait ça
	} 
  }
  alert("contexte a effacer : "+contextetodelete+" pour l'action ID : "+ID+" edit de la BDD avec le new contexte :"+newcontexte);
  node.data.contexte = newcontexte;
  $("#contextesactuels #"+idcontextetodelete+"").empty();
  node.editEnd(); //on fait une save	
  }
	
  //Fonction pour chercher les contextes en autocomplete
    $( "#contextesearch" ).autocomplete({
      source: CONTEXTE_TAGS
    });
	
	//Fonction pour ajouter un contexte à une action
    $("button#btnAddContexte").click(function(e){
	var contexteadd = $("#contextesearch").val();
	var testcontext = "false"; //si contexte existe pas => False
	for(var i= 0; i < CONTEXTE_TAGS.length; i++)
	{if(contexteadd ==CONTEXTE_TAGS[i]){testcontext = "true";}} //On test pour voir si le contexte 
	if(testcontext == "true"){ //Si le contexte existe on peut voir pour le rajouter ou non
	node = $("#myMOI").fancytree("getActiveNode"); //On recupère la node qui est actuellement selectionne
	var ID = node.data.ID;
	var oldcontexte = node.data.contexte;
	var existeornot = oldcontexte.indexOf(contexteadd);  //Si -1 il existe pas ds les contextes actuels
		if(existeornot == -1){ //On ajoute le contexte
		var newcontexte = oldcontexte+" "+contexteadd;
		node.data.contexte = newcontexte;
		node.editEnd(); //on fait une save
		//ajouter le champ pour ensuite supprimer le nouveau contexte apparu
		var htmlcontextesactuels = $("#contextesactuels").html();
		var idcontexte = contexteadd.substring(1);
		var imgcontexte = "\""+contexteadd+"\"";
		var htmlcontexteadd = "<div id='"+idcontexte+"'class='contxt'>"+contexteadd+"<img src='src/skin-themeroller/delete.png' style='vertical-align:middle;cursor:pointer;margin-top:-1px;' onclick='$(this).DeleteContexte("+imgcontexte+");'></div>"; 
		var txtcontextfinal = htmlcontextesactuels+""+htmlcontexteadd;
		$("#contextesactuels").html(txtcontextfinal);
		alert("contexte a ajouter : "+contexteadd+" pour l'action ID : "+ID+" edit de la BDD avec le new contexte :"+newcontexte);
		}
	}
	$("input[id=contextesearch]").val(""); //on efface le input 
	});
});