<?php
    	// ********************************************************************/
    	// *********** OUTLINE ET TODO MOI                         ************/
     	// ********************************************************************/  
	
?>

<script>
			
	    // *****************************   
        // ******* DECLARATIONS ********
	    // *****************************
		
var CLIPBOARD = null;
var USERID = '<?php echo $_SESSION["currentUser"]->getID();?>';

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
var STATUT_TODO = 1;
var STATUT_BLOCK = 2;
var STATUT_TRIGGER = 8;
var STATUT_DELETE = 16;
var EXTRACLASS_TODO = "action-class-run";
var EXTRACLASS_BLOCK = "action-class-wait";
var EXTRACLASS_TRIGGER = "action-class-trigger";
var EXTRACLASS_DELETE = "action-class-delete";
var STATUT_EFFORT = "normal";
var STATUT_TIME = "";
//les contextes seront à récupérer depuis une TAB ds la BDD associé à chaque ORG
var CONTEXTE_TAGS = ["@Web","@PC","@Téléphone","@KevinD","@JulienG","@DavidD","@Maison","@Bureau"];

var usermoi = '<?php echo "/json/gtd-user".$_SESSION["currentUser"]->getID().".json"; ?>' ; 
var usermoitodo = '<?php echo "/json/gtdtodo-user".$_SESSION["currentUser"]->getID().".json"; ?>' ; 

$(function(){

	// *****************************   
    // ****** OUTLINE TABLE ********
	// *****************************
	
  $("#myMOI").fancytree({
    checkbox: true,
	generateIds: true,
    titlesTabbable: true,     // Add all node titles to TAB chain
    source: {
		
      url: usermoi //Usermoi de l'internaute
		
      },
    extensions: ["edit", "dnd", "table", "gridnav", "themeroller", "childcounter", "filter"],
	childcounter: {  //Compteur action par projet
        deep: true,
        hideZeros: false,
        hideExpanded: true
      }, 
	  filter: {
      mode: "hide"
      },
	  beforeSelect: function(event, data) { //pour la partie decheck
	  //test = data.node.isSelected();
	  //if(test == true){ alert("on annule"); }
      	

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
		 var nodeoutline = data.node;
		 var dataTab = {
				"action": "EditAction",
				"ID": idaction,
				"statut": STATUT_DELETE
				};
				//On fait la MAJ en Ajax
				$.ajax({
					       url : "../../../ajax/moi.php",
					       type : "POST",
					       data : dataTab,
							success: function(data){
							//on fait ça si succes
							nodeoutline.extraClasses = EXTRACLASS_DELETE;
							nodeoutline.data.statut = STATUT_DELETE;
							
							//Recuperation de l'action dans TODO
							var treetodo = $("#myMOITODO").fancytree("getTree");
							nodetodo = treetodo.getNodeByKey(idaction);
							nodetodo.extraClasses = EXTRACLASS_DELETE+" class"+nodetodo.data.IDcircle+" all";
							nodetodo.data.statut = STATUT_DELETE;
							nodetodo.setActive();
							nodetodo.setSelected(false); //On deselectionne
							
							nodeoutline.setSelected(false); //On deselectionne le nodeoutline
						  },
						  error:function(){
							  alert("Erreur envoi AJAX")
						  } 
					    }); 
		 break;		 
		 }
	 break;
	 
	 case 3: //Une action lie a un projet
	 var namekeysel = tabselkey[2]; 
	 var idaction = namekeysel.substring(1);
	 var nodeoutline = data.node;

	 var dataTab = {
				"action": "EditAction",
				"ID": idaction,
				"statut": STATUT_DELETE
				};
				//On fait la MAJ en Ajax
				$.ajax({
					       url : "../../../ajax/moi.php",
					       type : "POST",
					       data : dataTab,
							success: function(data){
							//on fait ça si succes
							nodeoutline.extraClasses = EXTRACLASS_DELETE;
							nodeoutline.data.statut = STATUT_DELETE;
							
							//Recuperation de l'action dans TODO
							var treetodo = $("#myMOITODO").fancytree("getTree");
							nodetodo = treetodo.getNodeByKey(idaction);
							nodetodo.extraClasses = EXTRACLASS_DELETE+" class"+nodetodo.data.IDcircle+" all";
							nodetodo.data.statut = STATUT_DELETE;
							nodetodo.setActive();
							nodetodo.setSelected(false); //On deselectionne

							nodeoutline.setSelected(false); //On deselectionne le nodeoutline
						  },
						  error:function(){
							  alert("Erreur envoi AJAX")
						  } 
					    });
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
				var dataTab = {
				"action": "EditProject",
				"ID": idprojet,
				"role": idhit
				};
				//On fait la MAJ en Ajax
				$.ajax({
					       url : "../../../ajax/moi.php",
					       type : "POST",
					       data : dataTab,
							success: function(data){
							//on fait rien si succes
						  },
						  error:function(){
							  alert("Erreur envoi AJAX")
						  } 
					    }); 
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
				var dataTab = {
					"action": "EditProject",
					"ID": idprojet,
					"statut": STATUT_ONEDAY			
					};
					//On fait la MAJ en Ajax
					$.ajax({
							   url : "../../../ajax/moi.php",
							   type : "POST",
							   data : dataTab,
								success: function(data){
								//on fait rien si succes
							  },
							  error:function(){
								  alert("Erreur envoi AJAX")
							  } 
							}); 				
				break;
				}
		  break;
		  //ID de l'action pour hit dans un projet, role ou user
		  case "a":
		  var idaction = namekeypaste.substring(1);  
			  switch( typenamehit ){
					case "role":
					var dataTab = {
					"action": "EditAction",
					"ID": idaction,
					"role": idhit
					};
					//On fait la MAJ en Ajax
					$.ajax({
							   url : "../../../ajax/moi.php",
							   type : "POST",
							   data : dataTab,
								success: function(data){
								//on fait rien si succes
							  },
							  error:function(){
								  alert("Erreur envoi AJAX")
							  } 
							}); 
					data.otherNode.moveTo(node, data.hitMode);
					//on fait la modification de la Key
					var oldnode = data.otherNode;
					oldnode.key = node.key+"-a"+idaction;
					IDcircle = node.data.IDcircle;
					classold = oldnode.extraClasses;
					oldnode.editEnd();		
					//Faire la modification dans l'arbre TODO
					var treetodo = $("#myMOITODO").fancytree("getTree");
				    nodetodo = treetodo.getNodeByKey(idaction);
					nodetodo.extraClasses = classold+' class'+IDcircle+' all';
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
					var dataTab = {
					"action": "TransformActionSoloInActionProject",
					"ID": idaction,
					"PROJET": idhit					
					};
					//On fait la MAJ en Ajax
					$.ajax({
							   url : "../../../ajax/moi.php",
							   type : "POST",
							   data : dataTab,
								success: function(data){
								//on fait rien si succes
							  },
							  error:function(){
								  alert("Erreur envoi AJAX")
							  } 
							}); 
					data.otherNode.moveTo(node, data.hitMode);
					//on fait la modification de la Key
					var oldnode = data.otherNode;
					oldnode.key = node.key+"-a"+idaction;					
					IDcircle = node.data.IDcircle;
					classold = oldnode.extraClasses;
					oldnode.editEnd();		
					//Faire la modification dans l'arbre TODO
					var treetodo = $("#myMOITODO").fancytree("getTree");
				    nodetodo = treetodo.getNodeByKey(idaction);
					nodetodo.extraClasses = classold+' class'+IDcircle+' all';
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
					var dataTab = {
					"action": "TransformActionProjectInActionSolo",
					"ID": idaction,
					"ROLE": idhit
					};
					//On fait la MAJ en Ajax
					$.ajax({
							   url : "../../../ajax/moi.php",
							   type : "POST",
							   data : dataTab,
								success: function(data){
								//on fait rien si succes
							  },
							  error:function(){
								  alert("Erreur envoi AJAX")
							  } 
							}); 
					data.otherNode.moveTo(node, data.hitMode);
					//on fait la modification de la Key
					var oldnode = data.otherNode;
					oldnode.key = node.key+"-a"+idaction;
					IDcircle = node.data.IDcircle;
					classold = oldnode.extraClasses;
					oldnode.editEnd();		
					//Faire la modification dans l'arbre TODO
					var treetodo = $("#myMOITODO").fancytree("getTree");
				    nodetodo = treetodo.getNodeByKey(idaction);
					nodetodo.extraClasses = classold+' class'+IDcircle+' all';
					break;
					
					case "user":
					alert("on envoi l'action : "+idaction+" d'un projet dans le user : "+idhit);
					data.otherNode.moveTo(node, data.hitMode);
					//on fait la modification de la Key
					var oldnode = data.otherNode;
					oldnode.key = node.key+"-a"+idaction;
					oldnode.editEnd();		
					break;	

					case "projet":
					var dataTab = {
					"action": "EditAction",
					"ID": idaction,
					"project": idhit
					};
					//On fait la MAJ en Ajax
					$.ajax({
							   url : "../../../ajax/moi.php",
							   type : "POST",
							   data : dataTab,
								success: function(data){
								//on fait rien si succes
							  },
							  error:function(){
								  alert("Erreur envoi AJAX")
							  } 
							}); 
					data.otherNode.moveTo(node, data.hitMode);
					//on fait la modification de la Key
					var oldnode = data.otherNode;
					oldnode.key = node.key+"-a"+idaction;
					IDcircle = node.data.IDcircle;
					classold = oldnode.extraClasses;
					oldnode.editEnd();		
					//Faire la modification dans l'arbre TODO
					var treetodo = $("#myMOITODO").fancytree("getTree");
				    nodetodo = treetodo.getNodeByKey(idaction);
					nodetodo.extraClasses = classold+' class'+IDcircle+' all';
					break;	
					}
		break;		
		}
      }
    },
    edit: {	
		edit: function(event, data){
        // Editor was opened (available as data.input)
		},
		close: function(event, data){
        // Editor was removed
        if( data.save ) {
		node = data.node;
		idtype = node.data.type;
			if(idtype == 2){
			title = node.title;
			idaction = node.data.ID;
			var treetodo = $("#myMOITODO").fancytree("getTree");
			nodetodo = treetodo.getNodeByKey(idaction);
			nodetodo.setTitle( title );
			}						
		}
		}
    },
	
    gridnav: {
      autofocusInput: false,
      handleCursorKeys: true
    },
	
	activate: function(e, data) {
	  var node = data.node;
	  var key = node.key;
	  var typeprojetoraction = node.data.type; //type projet ou action ?
	  var contextes = node.data.contexte; //recuperation des contextes pour la node
	  var purpose = node.data.purpose; //raison d'etre d'un role
	  var accountabilities = node.data.accountabilities; //redevabilites
	  var domains = node.data.scope; //domaines
	  var notes = node.data.notes; //recuperation des notes pour la node
	  var roletension = node.data.role; //recuperation du role pour la node tension
	  var priority = node.data.priority; //recuperation de la priorite pour la node
	  var ID = node.data.ID; //recuperation pour l'ID
	  var statut = node.data.statut; //recuperation pour statut
	  var time = node.data.time; //recuperation pour les actions du Time
	  var effort = node.data.effort; //recuperation pour les actions de l'effort
	  var father = node.data.father; //recuperation pour les actions de l'effort
	  var treeoutline = "outline"; //Arbre outline
	  
	  $(".titleactive").html(node.title); //on affiche le titre de la valeur actuel
	  
	  if(father == null){$(".father").html(""); //on efface le father	  
	  }
	  
	  //Gestion pour l'affichage de la GOUV d'un rôle (purpose, redevabilite
	  if(purpose != null){ 
	   $(".purpose").html("<span class='minicirclemoi'><strong>"+purpose+"</strong></span>");	
	  }else{$(".purpose").html(""); }
	  if(accountabilities != null){ 
	   $("#accountabilities").html(accountabilities);	
	  }else{$("#accountabilities").html(""); }
	   if(domains != null){ 
	   $("#domains").html(domains);	
	  }else{$("#domains").html(""); }
	  
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
	  switch (typeprojetoraction){	  
	  case "0":  //Item
	  var type = "i";
	  break;
	  
	  case "1": //Projet
	  var type = "p";
	  break;
	  
	  case "2": //Action
	  var type = "a";
	  break;
	  
	  case "3": //Tension
	  var type = "t";
	  break;
	  }
	  $('#notes').removeClass("noteshide");
	  if(statut == 16){var disabled = 'disabled';} else{ var disabled = '';} //Les notes sont desactivés pour une action terminé
	  notes = notes.replace(/<br\s*[\/]?>/gi, "\n"); //Pour afficher saut de ligne ds textarea
	  txtnotes = "<textarea id='"+ID+"'class='areanotes' onblur='$(this).UpdateNotes(this.value,\""+typeprojetoraction+"\",\""+ID+"\",\""+key+"\",\""+treeoutline+"\");'"+disabled+">"+notes+"</textarea>";
	  $("#notes").html(txtnotes);} //on affiche les notes de l'objet selectionné
	  else{ $('#notes').addClass("noteshide"); }
	  
	  //Gestion de la partie TIME
	  /*if(time != null){ //Si on a time qui est pas null
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
	 */
	 
	 //Gestion de la partie EFFORT
	  /*if(effort != null){ //Si on a effort qui est pas null		  
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
	*/
	
	  //Gestion de la partie STATUT / PRIORITAIRE et EFFORT / TIME
	  if(statut != null){ //Si on a un statut

		  //Cas pour une action
		  if(typeprojetoraction == 2){
		  if(statut == 1){var statutext = "A Faire"; var inputtodo = "<input type='radio' name='statutradio' id='todo' value='todo' onclick='$(this).ChangeStatutAction(\""+STATUT_TODO+"\",\""+ID+"\",\""+key+"\",\""+treeoutline+"\");' checked>A Faire ";} else {var inputtodo = "<input type='radio' name='statutradio' id='todo' value='todo' onclick='$(this).ChangeStatutAction(\""+STATUT_TODO+"\",\""+ID+"\",\""+key+"\",\""+treeoutline+"\");'>A Faire ";}
		  if(statut == 2){ var statutext = "Bloqu&eacute;"; var inputblock = "<input type='radio' name='statutradio' id='block' value='block' onclick='$(this).ChangeStatutAction(\""+STATUT_BLOCK+"\",\""+ID+"\",\""+key+"\",\""+treeoutline+"\");' checked>Bloqu&eacute; ";} else {var inputblock = "<input type='radio' onclick='$(this).ChangeStatutAction(\""+STATUT_BLOCK+"\",\""+ID+"\",\""+key+"\",\""+treeoutline+"\");' name='statutradio' id='block' value='block'>Bloqu&eacute; ";}
		  if(statut == 8){ var statutext = "D&eacute;clench&eacute;"; var inputtrigger = "<input type='radio' name='statutradio' id='trigger' value='trigger' onclick='$(this).ChangeStatutAction(\""+STATUT_TRIGGER+"\",\""+ID+"\",\""+key+"\",\""+treeoutline+"\");' checked>D&eacute;clench&eacute; <br/>Champ pour rentrer la date";} else {var inputtrigger = "<input type='radio' onclick='$(this).ChangeStatutAction(\""+STATUT_TRIGGER+"\",\""+ID+"\",\""+key+"\",\""+treeoutline+"\");' name='statutradio' id='trigger' value='trigger'>D&eacute;clench&eacute; ";} 
		  if(statut == 16){ var statutext = "Termin&eacute;"; var inputtodo = ""; var inputblock = "";}
		  $("#statut").html("<form class='statutradio'><div class='statuttitle'><strong>Statut :</strong> "+statutext+"</div>"+inputtodo+inputblock/*+inputtrigger*/);	
		  }
	  
		  //Cas pour un projet et priorite du projet
		  if(typeprojetoraction == 1){
		  //On cree les boutons
		  if(statut == 1){var statutext = "En Cours"; var inputarchive="";  var inputrun = "<input type='radio' id='run' onclick='$(this).ChangeStatutProjet(\""+STATUT_RUN+"\");' name='statutradio' value='run' checked>En Cours ";} else {var inputrun = "<input type='radio' name='statutradio' onclick='$(this).ChangeStatutProjet(\""+STATUT_RUN+"\");' id='run' value='run'>En Cours "; var inputarchive=""; }
		  if(statut == 2){var statutext = "En Attente"; var inputarchive=""; var inputwait = "<input type='radio' id='wait' name='statutradio' value='wait' onclick='$(this).MessageBox('msg');' onclick='$(this).ChangeStatutProjetProjet(\""+STATUT_WAIT+"\");' checked>En Attente <br/>";} else { var inputarchive=""; var inputwait = "<input type='radio' name='statutradio' id='wait' value='wait' onclick='$(this).ChangeStatutProjet(\""+STATUT_WAIT+"\");'>En Attente <br/>";}
		  if(statut == 4){var statutext = "Termin&eacute;"; var inputarchive="Archive moi ça";  var inputdone = "<input type='radio' name='statutradio' id='done' onclick='$(this).ChangeStatutProjet(\""+STATUT_DONE+"\");' value='done' checked>Termin&eacute; ";} else {var inputarchive=""; var inputdone = "<input type='radio' name='statutradio' id='done' value='done' onclick='$(this).ChangeStatutProjet(\""+STATUT_DONE+"\");'>Termin&eacute; ";}
		  if(statut == 8){var statutext = "Un jour peut etre"; var inputarchive=""; var inputoneday = " <input type='radio' name='statutradio' id='done' onclick='$(this).ChangeStatutProjet(\""+STATUT_ONEDAY+"\");' value='done' checked>Un jour peut etre";} else {var inputarchive=""; var inputoneday = " <input type='radio' name='statutradio' id='done' onclick='$(this).ChangeStatutProjet(\""+STATUT_ONEDAY+"\");' value='done'>Un jour peut etre";}
		  //on affiche le formulaire avec les statuts
		  $("#statut").html("<form class='statutradio'><div class='statuttitle'><strong>Statut :</strong> "+statutext+"</div>"+inputrun+inputwait+inputdone+inputoneday+inputarchive+"</form>");
		  }
	  } 
	  else{$("#statut").html(""); } //On affiche rien si c'est pas une action ou un projet
	   
	  //Gestion de la partie CONTEXTE
	  /*if(contextes != null){
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
	  */
	  
	  
      },
    renderColumns: function(event, data) {
     var node = data.node,
      $tdList = $(node.tr).find(">td"); 

      //$tdList.eq(1).text(node.data.contexte);
	  //$tdList.eq(2).text(node.data.effort);
	  //$tdList.eq(3).text(node.key);
	  //$tdList.eq(4).text(node.data.ID);
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
	case "reactivate":
		var dataTab = {
					"action": "EditAction",
					"ID": node.data.ID,
					"statut": STATUT_TODO
					};
					//On fait la MAJ en Ajax
					$.ajax({
							   url : "../../../ajax/moi.php",
							   type : "POST",
							   data : dataTab,
								success: function(data){
								//on fait ça si succes
								node.extraClasses = EXTRACLASS_TODO;
								node.data.statut = STATUT_TODO;
								node.setActive();
								node.setSelected(false); //On deselectionne

								//Reactive dans TODO
								var treetodo = $("#myMOITODO").fancytree("getTree");
								nodetodo = treetodo.getNodeByKey(node.data.ID);
								nodetodo.extraClasses = EXTRACLASS_TODO+" class"+nodetodo.data.IDcircle+" all";
								nodetodo.data.statut = STATUT_TODO;
								nodetodo.setActive();
							  },
							  error:function(){
								  alert("Erreur envoi AJAX")
							  } 
							}); 
      break;
    case "remove":
	  var treeoutline = $("#myMOI").fancytree("getTree");
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
		  case "i": //Supprime un item
				var iditem = namekeydel.substring(1);
				node.remove();
				alert("Fonction Ajax pour supprimer l'item => ID : "+iditem);
				break;
				
		  case "a": //Supprime l'action unique
				var idaction = namekeydel.substring(1);  
				var dataTab = {
					"action": "DeleteAction",
					"ID": idaction,
					};
					//On fait la MAJ en Ajax
					$.ajax({
							   url : "../../../ajax/moi.php",
							   type : "POST",
							   data : dataTab,
								success: function(data){
								//on fait rien si succes
								node.remove();
								var nodeoutline = treeoutline.getNodeByKey(node.data.ID);
								nodeoutline.remove();
							  },
							  error:function(){
								  alert("Erreur envoi AJAX")
							  } 
							}); 
				break;
		  }
	  break;	
	  
	  case 3: //Supprime l'action d'un projet
	  var namekeydel = tabdelkey[2];
	  var idaction = namekeydel.substring(1);
	  var dataTab = {
		"action": "DeleteAction",
		"ID": idaction,
		};
		//On fait la MAJ en Ajax
		$.ajax({
				   url : "../../../ajax/moi.php",
				   type : "POST",
				   data : dataTab,
					success: function(data){
					//on fait rien si succes
					node.remove();
					var nodeoutline = treeoutline.getNodeByKey(node.data.ID);
					nodeoutline.remove();
				  },
				  error:function(){
					  alert("Erreur envoi AJAX")
				  } 
				}); 
	  break;	  
	  }	  
      break;
    case "newaction":
	  var roleproject = node.key;
	  var titleproject = node.title;
	  var circleID = node.data.IDcircle;
	  var roleorproject = roleproject.search("-p");
	  if(roleorproject == -1){
	  tabrole = roleproject.split('r');
	  role = tabrole[1];
	  projet = 0;
	  }
	  else{
	  tabprojet = roleproject.split('-p');
	  projet = tabprojet[1];
	  role = 0;
	  }
		refNode = node.addChildren({
        title: "Nouvelle action",
		extraClasses: EXTRACLASS_TODO,	
		statut: STATUT_TODO,
		type:2,
		effort:STATUT_EFFORT,
		time:STATUT_TIME,
		notes:STATUT_NOTES,
        isNew: true
      });
      node.setExpanded();
	  //On envoi en Ajax pour crée
	   var dataTab = {
		"action": "AddAction",
		"proj_id": projet,
		"role_id": role,
		"acst_id": STATUT_TODO,
		"act_effort": STATUT_EFFORT,
		"act_time": STATUT_TIME,
		"act_description": STATUT_NOTES
		};
	  $.ajax({
			   url : "../../../ajax/moi.php",
			   type : "POST",
			   data : dataTab,
				success: function(data){
				var idaction = data;
				refNode.key = roleproject+"-a"+idaction;		
				refNode.data.ID = idaction;
				//Addchildren dans TODO
				  var treetodo = $("#myMOITODO").fancytree("getTree");
				  nodetodo = treetodo.getNodeByKey("root_2");
				  refNodetodo = nodetodo.addChildren({
					title: "Nouvel action",
					extraClasses: EXTRACLASS_TODO+' class'+circleID+' all',	
					statut: STATUT_TODO,
					effort:STATUT_EFFORT,
					father:titleproject,
					key:idaction,
					type:2,
					ID:refNode.key,
					time:STATUT_TIME,
					notes:STATUT_NOTES,
					isNew: true
				  });
			  },
			  error:function(){
				  alert("Erreur envoi AJAX")
			  } 
			}); 
      refNode.editStart();
	  
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
	  var tabrole = role.split('r');
	  roleid = tabrole[1];
      refNode = node.addChildren({
        title: "Nouveau projet",
		extraClasses: EXTRACLASS_RUN,	
		statut: STATUT_RUN,
		notes:STATUT_NOTES,
        isNew: true
      });
      node.setExpanded();
	  //On envoi en Ajax pour crée
	   var dataTab = {
		"action": "AddProject",
		"proj_description": STATUT_NOTES,
		"role_id": roleid,
		"user_id": USERID,
		"acst_id": STATUT_RUN,
		"typr_id": 1
		};
	  $.ajax({
			   url : "../../../ajax/moi.php",
			   type : "POST",
			   data : dataTab,
				success: function(data){
				var idproject = data;
				refNode.key = role+"-p"+idproject;
				refNode.data.ID = idproject;
			  },
			  error:function(){
				  alert("Erreur envoi AJAX")
			  } 
			}); 
      refNode.editStart();
      break;
	case "newprojetoneday":
	var role = node.key;
	var tabrole = role.split('-');
	tabroleid = tabrole[0];
	troleid = tabroleid.split('r');
	roleid = troleid[1];
    refNode = node.addChildren({
        title: "Nouveau projet",
		extraClasses: EXTRACLASS_ONEDAY,	
		statut: STATUT_ONEDAY,
		notes:STATUT_NOTES,
        isNew: true
    });
    node.setExpanded();
	//On envoi en Ajax pour crée
	var dataTab = {
		"action": "AddProject",
		"proj_description": STATUT_NOTES,
		"role_id": roleid,
		"user_id": USERID,
		"acst_id": STATUT_ONEDAY,
		"typr_id": 1
	};
	$.ajax({
			   url : "../../../ajax/moi.php",
			   type : "POST",
			   data : dataTab,
				success: function(data){
				var idproject = data;
				refNode.key = tabroleid+"-p"+idproject;
				refNode.data.ID = idproject;
			  },
			  error:function(){
				  alert("Erreur envoi AJAX")
			  } 
			});
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
    } /*else if( e.which === $.ui.keyCode.DELETE ) {
      cmd = "remove";
    }*/ else if( e.which === $.ui.keyCode.F2 ) {
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

	// *****************************   
    // ******** TODO TABLE *********  
	// *****************************

	$("#myMOITODO").fancytree({
	checkbox: true,
	generateIds: true,
	titlesTabbable: true,     // Add all node titles to TAB chain
	source: {
	  url: usermoitodo //Usermoi de l'internaute		
	 },
	 extensions: ["edit", "table", "gridnav", "themeroller"],
    gridnav: {
      autofocusInput: false,
      handleCursorKeys: true
    },
	 renderColumns: function(event, data) {
     var node = data.node,
      $tdList = $(node.tr).find(">td"); 
	  //$tdList.eq(1).text(node.data.contexte);
	  //$tdList.eq(2).text(node.data.effort);
	  //$tdList.eq(3).text(node.key);
	  //$tdList.eq(4).text(node.data.ID);
    },
	select: function(event, data) { //Select = quand on coche la checkbox	
	var nodetodo = data.node;
	//var idaction = data.node.ID;
	var keyoutline = nodetodo.data.ID; //recuperation pour l'ID
	var idaction = nodetodo.key;
		 var dataTab = {
				"action": "EditAction",
				"ID": idaction,
				"statut": STATUT_DELETE
				};
				//On fait la MAJ en Ajax
				$.ajax({
					       url : "../../../ajax/moi.php",
					       type : "POST",
					       data : dataTab,
							success: function(data){				
							//on fait ça si succes
							nodetodo.extraClasses = EXTRACLASS_DELETE+" class"+nodetodo.data.IDcircle+" all";
							nodetodo.data.statut = STATUT_DELETE;
							nodetodo.setActive();
							nodetodo.setSelected(false);
							
							//Recuperation de l'action dans TODO
							var treeoutline = $("#myMOI").fancytree("getTree");
							var nodeoutline = treeoutline.getNodeByKey(keyoutline);
							nodeoutline.extraClasses = EXTRACLASS_DELETE;	
							nodeoutline.data.statut = STATUT_DELETE;
							nodeoutline.setActive();
							nodeoutline.setSelected(false); //On deselectionne le nodeoutline
							
						  },
						  error:function(){
							  alert("Erreur envoi AJAX")
						  } 
					    }); 
	},
	edit: {	
		edit: function(event, data){
        // Editor was opened (available as data.input)
		},
		close: function(event, data){
        // Editor was removed
        if( data.save ) {
		node = data.node;
		idtype = node.data.type;
			if(idtype == 2){
			title = node.title;
			idaction = node.data.ID;
			var treetodo = $("#myMOI").fancytree("getTree");
			nodetodo = treetodo.getNodeByKey(idaction);
			nodetodo.setTitle( title );
			}						
		}
		}
    },
	activate: function(e, data) {
	  var node = data.node;
	  var key = node.data.ID;
	  var typeprojetoraction = node.data.type; //type projet ou action ?
	  var contextes = node.data.contexte; //recuperation des contextes pour la node
	  var purpose = node.data.purpose; //raison d'etre d'un role
	  var accountabilities = node.data.accountabilities; //redevabilites
	  var domains = node.data.scope; //domaines
	  var notes = node.data.notes; //recuperation des notes pour la node
	  var roletension = node.data.role; //recuperation du role pour la node tension
	  var priority = node.data.priority; //recuperation de la priorite pour la node
	  var ID = node.key; //recuperation pour l'ID
	  var statut = node.data.statut; //recuperation pour statut
	  var time = node.data.time; //recuperation pour les actions du Time
	  var effort = node.data.effort; //recuperation pour les actions de l'effort
	  var father = node.data.father; //recuperation du father (projet)
	  var treetodo = "todo";
	  
	  $(".titleactive").html(node.title); //on affiche le titre de la valeur actuel
	  
	  if(father != null){ $(".father").html(father); //on affiche le titre de la valeur actuel
	  } else{ $(".father").html("");}
	  
	  $(".purpose").html("");
	  $("#accountabilities").html("");
	  $("#domains").html("");
	  
	  //Gestion de la partie NOTES
	  if(notes != null){ 
	  $('#notes').removeClass("noteshide");	  
	  if(statut == 16){var disabled = 'disabled';} else{ var disabled = '';} //Les notes sont desactivés pour une action terminé
	  notes = notes.replace(/<br\s*[\/]?>/gi, "\n"); //Pour afficher saut de ligne ds textarea
	  txtnotes = "<textarea id='"+key+"'class='areanotes' onblur='$(this).UpdateNotes(this.value,\""+typeprojetoraction+"\",\""+key+"\",\""+ID+"\",\""+treetodo+"\");'"+disabled+">"+notes+"</textarea>";
	  $("#notes").html(txtnotes);} //on affiche les notes de l'objet selectionné
	  else{ $('#notes').addClass("noteshide"); }

	  if(statut != null){ //Si on a un statut
	  //Cas pour une action 
	  if(statut == 1){var statutext = "A Faire"; var inputtodo = "<input type='radio' name='statutradio' id='todo' value='todo' onclick='$(this).ChangeStatutAction(\""+STATUT_TODO+"\",\""+key+"\",\""+ID+"\",\""+treetodo+"\");' checked>A Faire ";} else {var inputtodo = "<input type='radio' name='statutradio' id='todo' value='todo' onclick='$(this).ChangeStatutAction(\""+STATUT_TODO+"\",\""+key+"\",\""+ID+"\",\""+treetodo+"\");'>A Faire ";}
	  if(statut == 2){ var statutext = "Bloqu&eacute;"; var inputblock = "<input type='radio' name='statutradio' id='block' value='block' onclick='$(this).ChangeStatutAction(\""+STATUT_BLOCK+"\",\""+key+"\",\""+ID+"\",\""+treetodo+"\");' checked>Bloqu&eacute; ";} else {var inputblock = "<input type='radio' onclick='$(this).ChangeStatutAction(\""+STATUT_BLOCK+"\",\""+key+"\",\""+ID+"\",\""+treetodo+"\");' name='statutradio' id='block' value='block'>Bloqu&eacute; ";}
	  if(statut == 8){ var statutext = "D&eacute;clench&eacute;"; var inputtrigger = "<input type='radio' name='statutradio' id='trigger' value='trigger' onclick='$(this).ChangeStatutAction(\""+STATUT_TRIGGER+"\",\""+key+"\",\""+ID+"\",\""+treetodo+"\");' checked>D&eacute;clench&eacute; <br/>Champ pour rentrer la date";} else {var inputtrigger = "<input type='radio' onclick='$(this).ChangeStatutAction(\""+STATUT_TRIGGER+"\",\""+key+"\",\""+ID+"\",\""+treetodo+"\");' name='statutradio' id='trigger' value='trigger'>D&eacute;clench&eacute; ";} 
	  if(statut == 16){ var statutext = "Termin&eacute;"; var inputtodo = ""; var inputblock = "";}
	  $("#statut").html("<form class='statutradio'><div class='statuttitle'><strong>Statut :</strong> "+statutext+"</div>"+inputtodo+inputblock/*+inputtrigger*/);	
	  }
	}
	 })/*.on("nodeCommand", function(event, data){
    // Custom event handler that is triggered by keydown-handler and
    // context menu:
	  var refNode, moveMode,
      tree = $(this).fancytree("getTree"),
      node = tree.getActiveNode();

      switch( data.cmd ) {
		case "rename":
		node.editStart();
		break;
	  }
	})*/;

	// *****************************   
    // ******** FONCTIONS **********
	// *****************************
   
  $("#myMOI").contextmenu({
      select: "span.fancytree-title",
    menu: [
      {title: "Nouveau projet", cmd: "newprojet", uiIcon: "ui-icon-plus" },
	  {title: "Nouveau projet Un jour peut etre", cmd: "newprojetoneday", uiIcon: "ui-icon-plus" },
	  {title: "Nouvel item", cmd: "newitem", uiIcon: "ui-icon-plus" },
      {title: "Nouvelle action", cmd: "newaction", uiIcon: "ui-icon-arrowreturn-1-e" },
	  {title: "Renommer", cmd: "rename", uiIcon: "ui-icon-pencil" },
	  {title: "Supprimer", cmd: "remove", uiIcon: "ui-icon-trash" },
      {title: "Cut", cmd: "cut", uiIcon: "ui-icon-scissors"},
      {title: "Copy", cmd: "copy", uiIcon: "ui-icon-copy"},
	  {title: "Réactivé", cmd: "reactivate", uiIcon: "ui-icon-plus"},
      {title: "Paste", cmd: "paste", uiIcon: "ui-icon-clipboard", disabled: true }
      ],
	   beforeOpen: function(event, ui) {
	   var node = $.ui.fancytree.getNode(ui.target);
	   node.setActive(); //met la classe fancytree-active
	   var statut = node.data.statut; //recuperation pour statut
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
			  $("#myMOI").contextmenu("showEntry", "reactivate", false); //cache le remove
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
		 $("#myMOI").contextmenu("showEntry", "reactivate", false); //cache le remove
			 }
		 break;
		 case 2:
		 
		 var projetouaction = tab[1];
		 var lettre = projetouaction.substring(0,1);
		 if(lettre == "a" || lettre == "i" ){ //Action unique
			 if(statut == 16){ //Si c'est une action terminée
			$("#myMOI").contextmenu("showEntry", "remove", false); //cache le remove
			$("#myMOI").contextmenu("showEntry", "rename", false); //cache le remove
			$("#myMOI").contextmenu("showEntry", "reactivate", true); //cache le remove
			}else{
			$("#myMOI").contextmenu("showEntry", "remove", true); //cache le remove
			$("#myMOI").contextmenu("showEntry", "rename", true); //cache le remove
			$("#myMOI").contextmenu("showEntry", "reactivate", false); //cache le remove
			}
		   $("#myMOI").contextmenu("showEntry", "newprojetoneday", false); //cache le remove
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
			 $("#myMOI").contextmenu("showEntry", "remove", false); //cache le remove
			 $("#myMOI").contextmenu("showEntry", "newprojetoneday", false); //cache le remove
			 $("#myMOI").contextmenu("showEntry", "rename", true); //cache le remove
			 $("#myMOI").contextmenu("showEntry", "newprojet", false); //cache le remove
			 $("#myMOI").contextmenu("showEntry", "copy",  false); //cache le rename
			 $("#myMOI").contextmenu("showEntry", "cut", false); //cache le rename
			 $("#myMOI").contextmenu("showEntry", "paste", false); //cache le rename
			 $("#myMOI").contextmenu("showEntry", "newitem", false); //cache le rename
			 $("#myMOI").contextmenu("showEntry", "newaction", true); //cache le remove
			 $("#myMOI").contextmenu("showEntry", "reactivate", false); //cache le remove
			 }
			 if(lettre == "o"){
			 $("#myMOI").contextmenu("showEntry", "remove", false); //cache le remove
			 $("#myMOI").contextmenu("showEntry", "newprojetoneday", true); //cache le remove
			 $("#myMOI").contextmenu("showEntry", "rename", false); //cache le remove
			 $("#myMOI").contextmenu("showEntry", "newprojet", false); //cache le remove
			 $("#myMOI").contextmenu("showEntry", "copy",  false); //cache le rename
			 $("#myMOI").contextmenu("showEntry", "reactivate", false); //cache le remove
			 $("#myMOI").contextmenu("showEntry", "cut", false); //cache le rename
			 $("#myMOI").contextmenu("showEntry", "paste", false); //cache le rename
			 $("#myMOI").contextmenu("showEntry", "newitem", false); //cache le rename
			 $("#myMOI").contextmenu("showEntry", "newaction", false); //cache le remove
			 }
		}	 
		 break;
		 case 3:
		 //Action ds un projet
		if(statut == 16){ //Si c'est une action terminée
		$("#myMOI").contextmenu("showEntry", "remove", false); //cache le remove
		$("#myMOI").contextmenu("showEntry", "rename", false); //cache le remove
		$("#myMOI").contextmenu("showEntry", "reactivate", true); //cache le remove
		}else{
		$("#myMOI").contextmenu("showEntry", "remove", true); //cache le remove
		$("#myMOI").contextmenu("showEntry", "rename", true); //cache le remove
		$("#myMOI").contextmenu("showEntry", "reactivate", false); //cache le remove
		}
		$("#myMOI").contextmenu("showEntry", "newprojetoneday", false); //cache le remove
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
  
  /*$("#myMOITODO").contextmenu({
      select: "span.fancytree-title",
    menu: [
	  {title: "Renommer", cmd: "rename", uiIcon: "ui-icon-pencil" }
      ],
	   beforeOpen: function(event, ui) {
	   var node = $.ui.fancytree.getNode(ui.target);
	   node.setActive(); //met la classe fancytree-active
	   $("#myMOITODO").contextmenu("showEntry", "rename", true); //cache le remove
	   },
		select: function(event, ui) {
      var that = this;
      // delay the event, so the menu can close and the click event does
      // not interfere with the edit control
      setTimeout(function(){
        $(that).trigger("nodeCommand", {cmd: ui.cmd});
      }, 100);
    }
   }); */
  
  //Look des bouttons fun
  $("button").button();
  
  //Fonction bouton pour reload de la page 
  $("button#reload").click(function(event){
  location.reload(true);
  });
  
  //Fonction pour afficher ou cacher les Todo
  $('#todoinput').click(function(event) {
  selectoption = $("#selectcircle").val();
  if($('#todoinput').is(':checked')) { $( "#myMOITODO tr.action-class-run."+selectoption ).show(); }
  else{ $( "#myMOITODO tr.action-class-run."+selectoption ).hide(); }});
  
  //Fonction pour afficher ou cacher les Block
  $('#blockinput').click(function(event) {
  selectoption = $("#selectcircle").val();
  if($('#blockinput').is(':checked')) { $( "#myMOITODO tr.action-class-wait."+selectoption ).show(); }
  else{ $( "#myMOITODO tr.action-class-wait."+selectoption ).hide(); }});
  
  //Fonction pour afficher ce qui est terminé
  $('#finishedinput').click(function(event) {
  selectoption = $("#selectcircle").val();
  if($('#finishedinput').is(':checked')) { $( "#myMOITODO tr.action-class-delete."+selectoption ).show(); }
  else{ $( "#myMOITODO tr.action-class-delete."+selectoption ).hide(); }});
  
   //Fonction pour afficher ou cacher les Actions
   $('#selectcircle').change(function(){  
   var $this = $(this),
   selectoption = $this.val();
   block = $('#blockinput').is(':checked');
   todo = $('#todoinput').is(':checked');
   finished = $('#finishedinput').is(':checked');
		//verifier le select actif/block
		if(block == true && todo == true && finished == true){ //Cas vue pour tous
		$( "#myMOITODO tr.all").hide();
		$( "#myMOITODO tr.action-class-wait."+selectoption ).show();
		$( "#myMOITODO tr.action-class-delete."+selectoption ).show();			
		$( "#myMOITODO tr.action-class-run."+selectoption ).show();
		}
		
		if(block == true && todo == true && finished == false){ //Cas vue pour actif et block
		$( "#myMOITODO tr.all").hide();
		$( "#myMOITODO tr.action-class-run."+selectoption ).show();	
		$( "#myMOITODO tr.action-class-wait."+selectoption ).show();	
		}
		
		if(block == false && todo == true && finished == true){ //Cas vue pour actif et termine
		$( "#myMOITODO tr.all").hide();
		$( "#myMOITODO tr.action-class-run."+selectoption ).show();	
		$( "#myMOITODO tr.action-class-delete."+selectoption ).show();	
		}
		
		if(block == false && todo == false && finished == true){ //Cas vue pour terminé seulement
		$( "#myMOITODO tr.all").hide();
		$( "#myMOITODO tr.action-class-delete."+selectoption ).show();	
		}
		
		if(block == true && todo == false && finished == false){ //Cas vue pour block seulement	
		$( "#myMOITODO tr.all").hide();
		$( "#myMOITODO tr.action-class-wait."+selectoption ).show();	
		}
		
		if(block == false && todo == false && finished == false){ //Cas vue pour rien
		$( "#myMOITODO tr."+selectoption ).hide();
		}	
   }); 
   

 
  
    
  //Fonction bouton pour editer la tension associe à un role
  $.fn.ClickEditRole = function(ID){
  alert("Formulaire pour changer le role affecte a la tension "+ID);
  }
  
  //Fonction pour changer le statut d'une action
  $.fn.ChangeStatutAction = function(statut,ID,key,fromtree){
  if(fromtree == "outline"){idaction = ID;} else{idaction = key;}
  	//envoi ajax pour sauvegarde
 var dataTab = {
	"action": "EditAction",
	"ID": idaction,
	"statut": statut
	};
  $.ajax({
		   url : "../../../ajax/moi.php",
		   type : "POST",
		   data : dataTab,
			success: function(data){
			var treetodo = $("#myMOITODO").fancytree("getTree");
			var treeoutline = $("#myMOI").fancytree("getTree");
			switch(fromtree){
			case "todo":	
			nodeoutline = treeoutline.getNodeByKey(ID);
			nodeoutline.data.statut = statut;
			nodetodo = treetodo.getNodeByKey(key);
			nodetodo.data.statut = statut;
			IDcircle = nodetodo.data.IDcircle;
					switch(statut){
				case "1":
				nodetodo.extraClasses = EXTRACLASS_TODO+' class'+IDcircle+' all';
				nodeoutline.extraClasses = EXTRACLASS_TODO;
				$("#statut .statuttitle").empty();
				$("#statut .statuttitle").append("<strong>Statut :</strong> A Faire");
				break;
				case "2":
				nodetodo.extraClasses = EXTRACLASS_BLOCK+' class'+IDcircle+' all';
				nodeoutline.extraClasses = EXTRACLASS_BLOCK;
				//on efface le statut pour mettre à jour le nouveau
				$("#statut .statuttitle").empty();
				$("#statut .statuttitle").append("<strong>Statut :</strong> Bloqu&eacute;");
				break;
				case "8":
				nodetodo.extraClasses = EXTRACLASS_TRIGGER+' class'+IDcircle+' all';
				nodeoutline.extraClasses = EXTRACLASS_TRIGGER;
				//on efface le statut pour mettre à jour le nouveau
				$("#statut .statuttitle").empty();
				$("#statut .statuttitle").append("<strong>Statut :</strong> D&eacute;clench&eacute;");
				$("#statut .dateaction").empty();
				$("#statut .dateaction").append("Champ pour la date");
				alert("faire apparaitre le mini calendrier pour saisir une date");
				break;
				} 
			break;
			
			case "outline":
			nodeoutline = treeoutline.getNodeByKey(key);
			nodeoutline.data.statut = statut;
			nodetodo = treetodo.getNodeByKey(ID);
			nodetodo.data.statut = statut;
			IDcircle = nodetodo.data.IDcircle;
					switch(statut){
				case "1":
				nodetodo.extraClasses = EXTRACLASS_TODO+' class'+IDcircle+' all';
				nodeoutline.extraClasses = EXTRACLASS_TODO;
				$("#statut .statuttitle").empty();
				$("#statut .statuttitle").append("<strong>Statut :</strong> A Faire");
				break;
				case "2":
				nodetodo.extraClasses = EXTRACLASS_BLOCK+' class'+IDcircle+' all';
				nodeoutline.extraClasses = EXTRACLASS_BLOCK;
				//on efface le statut pour mettre à jour le nouveau
				$("#statut .statuttitle").empty();
				$("#statut .statuttitle").append("<strong>Statut :</strong> Bloqu&eacute;");
				break;
				case "8":
				nodetodo.extraClasses = EXTRACLASS_TRIGGER+' class'+IDcircle+' all';
				nodeoutline.extraClasses = EXTRACLASS_TRIGGER;
				//on efface le statut pour mettre à jour le nouveau
				$("#statut .statuttitle").empty();
				$("#statut .statuttitle").append("<strong>Statut :</strong> D&eacute;clench&eacute;");
				$("#statut .dateaction").empty();
				$("#statut .dateaction").append("Champ pour la date");
				alert("faire apparaitre le mini calendrier pour saisir une date");
				break;
				} 
			break;
			}
			nodetodo.editEnd(); //on fait une save
			nodeoutline.editEnd(); //on fait une save
		  },
		  error:function(){
			  alert("Erreur envoi AJAX")
		  } 
		}); 
  }
  
  //Fonction pour changer le statut d'un projet
  $.fn.ChangeStatutProjet = function(statut){	
  node = $("#myMOI").fancytree("getActiveNode"); //On recupère la node qui est actuellement selectionne
  var ID = node.data.ID;
  var dataTab = {
	"action": "EditProject",
	"ID": ID,
	"statut": statut
	};
	//On fait la MAJ en Ajax
	$.ajax({
			   url : "../../../ajax/moi.php",
			   type : "POST",
			   data : dataTab,
				success: function(data){
				//on fait rien si succes
			  },
			  error:function(){
				  alert("Erreur envoi AJAX")
			  } 
			}); 
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
  $.fn.UpdateNotes = function(txtnotes,type,ID,key,fromtree){ 
  switch (type){
  case "1": //projet uniquement pour Outline
  var dataTab = {
	"action": "EditProject",
	"ID": ID,
	"notes": txtnotes
	};
	//On fait la MAJ en Ajax
	$.ajax({
			   url : "../../../ajax/moi.php",
			   type : "POST",
			   data : dataTab,
				success: function(data){
				//on fait rien si succes
				nodeoutline = $("#myMOI").fancytree("getNodeByKey",key); //On recupère la node via la Key
				nodeoutline.data.notes = txtnotes;
				nodeoutline.editEnd(); //on fait une save
			  },
			  error:function(){
				  alert("Erreur envoi AJAX")
			  } 
			});
  break;
  
  case "2": //Action
	  if(fromtree == "outline"){
	  var dataTab = {
		"action": "EditAction",
		"ID": ID,
		"notes": txtnotes
		};
		//On fait la MAJ en Ajax
		$.ajax({
				   url : "../../../ajax/moi.php",
				   type : "POST",
				   data : dataTab,
					success: function(data){
					//on fait rien si succes
					nodeoutline = $("#myMOI").fancytree("getNodeByKey",key); //On recupère la node via la Key
					nodeoutline.data.notes = txtnotes;
					nodeoutline.editEnd(); //on fait une save
					
					nodetodo = $("#myMOITODO").fancytree("getNodeByKey",ID); //On recupère la node via la Key
					nodetodo.data.notes = txtnotes;
					nodetodo.editEnd(); //on fait une save
				  },
				  error:function(){
					  alert("Erreur envoi AJAX")
				  } 
				}); 
	  
	  }
	  if(fromtree == "todo"){
	  var dataTab = {
		"action": "EditAction",
		"ID": key,
		"notes": txtnotes
		};
		//On fait la MAJ en Ajax
		$.ajax({
				   url : "../../../ajax/moi.php",
				   type : "POST",
				   data : dataTab,
					success: function(data){
					//on fait rien si succes  
					nodetodo = $("#myMOITODO").fancytree("getNodeByKey",ID); //On recupère la node via la Key
					nodetodo.data.notes = txtnotes;
					nodetodo.editEnd(); //on fait une save
					
					nodeoutline = $("#myMOI").fancytree("getNodeByKey",key); //On recupère la node via la Key
					nodeoutline.data.notes = txtnotes;
					nodeoutline.editEnd(); //on fait une save
				  },
				  error:function(){
					  alert("Erreur envoi AJAX")
				  } 
				}); 
	  
	  }
  break;
  }  
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
</script>	

<div class='omo-right'>
	<div id="tabs_moreinfos">  
	<ul><li><a name="tabsMI-1" href="#tabsMI-1">Informations</a></li></ul>
		
<!-- Onglet MOI avec les différentes informations -->  
  <div id="tabsMI-1">
	
<span class="titleactive"></span><br/>
<span class="father"></span><br/>
	<span class="purpose"></span>
	
	<div id="accountabilities"></div>
	<div id="domains"></div>
	
	<div id="statut">
	<div class="dateaction"></div>
	</div>
	<div id="effort"></div>
	<div id="time"></div>
	<div id="contexte">
	<div id="contexteinput" class="hidecontexte"><strong>Contexte :</strong> <input id='contextesearch'><button id='btnAddContexte'>+</button></div>
	<div id="contextesactuels"></div>
	
		
	</div>
	<div id="rolestension"></div>
	<div id="notes"></div>
	
	</div>
	</div>
</div>

<div class='omo-middle'>			
	<div id="tabs_middle">	
		
<?php		
		echo "</td><td>";
?>	
<!-- Système à onglets, avec les 6 titres -->	
  <ul>
    <li><a name="tabsM-1" href="#tabsM-1"><?php echo T_("Aperçu");?></span></a></li>
    <li><a name="tabsM-2" href="#tabsM-2"><?php echo T_("A faire");?></a></li>
  </ul>
  
<!-- Onglet OUTLINE, avec les différents rôles -->  
  <div id="tabsM-1">
	
		<div id="barreuser">
		<button id="reload">Rafraichissement</button> <!--<button>Retour arrière</button>!-->
		<input name="search" placeholder="Tapez votre mot clé ici...">
		<button id="btnResetSearch">&times;</button>
		<span id="matches"></span>
		</div>
	
	<!-- Affichage pour le OUTLINE !-->
	<div id="myboxMOI">
		<table id="myMOI">
		<colgroup>
		<col width="70%"></col>
		<!--<col width="9%"></col>
		<col width="7%"></col>
		<col width="9%"></col>
		<col width="5%"></col>!-->
		</colgroup>
		<thead>
		<tr><th></th> <!--<th>Contexte</th> <th>Effort</th> <th>key</th> <th>ID</th>!--></tr>
		</thead>
		<tbody>
		<tr> <td></td> <td></td> <td></td> </tr>
		</tbody>
		</table>
    </div>
  </div>
  
<!-- Onglet TODO, avec les différents rôles -->   
    <div id="tabsM-2">
	
	<div id="barreuser" class="todo">
		<?php
		$tabcircleselects = explode(";",$circlesORG);
		$cmptcircle = 0;
		echo "<strong>Cercle</strong> <select id='selectcircle' name='selectcircle'>";
		foreach ($tabcircleselects as $circleselect) {
				if($cmptcircle == 0){echo "<option value='all' checked>".$this->_organisation->getName()."</option>";}
				$tabcircleselect = explode("-",$circleselect);
				$circlename = $tabcircleselect[0];
				$circleclass = $tabcircleselect[1];
				if($circlename != ""){echo "<option value='".$circleclass."'>".$circlename."</option>";}
				$cmptcircle++;
		}
		echo "</select>";
		?>
		<input type="checkbox" id="todoinput" class="statutbarre" name="todoinput" value="todo" checked> A Faire
		<input type="checkbox" id="blockinput" class="statutbarre" name="blockinput" value="block" checked> Bloqué
		<input type="checkbox" id="finishedinput" class="statutbarre" name="finishedinput" value="finish"> Terminée
	</div>
		
		<!-- Affichage pour le TODO !-->
		<div id="myboxMOI">
			<table id="myMOITODO">
			<colgroup>
		<col width="70%"></col>
		<!--<col width="9%"></col>
		<col width="7%"></col>
		<col width="9%"></col>
		<col width="5%"></col>!-->
		</colgroup>
		<thead>
		<tr><th></th> <!--<th>Contexte</th> <th>Effort</th> <th>key</th> <th>ID</th>!--></tr>
		</thead>
		<tbody>
		<tr> <td></td> <td></td> <td></td> </tr>
		</tbody>
			</table>
		</div> 
    </div>
</div>
</div>
  
  