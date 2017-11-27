<?
	include_once("../include.php");
	// Un formulaire avec une action demandé en GET???
	if (isset($_GET["action"])) {
	
		// Il s'agit d'un des formulaire
		if ($_GET["action"]=="getForm") {
			// Choix du formulaire et affichage de ce dernier
			$param=$_GET["param"];
			$id=$_GET["id"];
			echo "<input type='hidden' name='formulaire[]' value='$id'>";
			echo "<input type='hidden' name='form_type_$id' value='$param'>";

			switch ($param) {
				case 1:
					include "form_gouvernance/form_addcircle.php";
					break;
				case 2:
					include "form_gouvernance/form_editcircle.php";
					break;
				case 3:
					include "form_gouvernance/form_deletecircle.php";
					break;
				case 4:
					$_GET["role"]="";
					include "form_gouvernance/form_addcircle.php";
					break;
				case 5:
					include "form_gouvernance/form_editrole.php";
					break;
				case 10:
					include "form_gouvernance/form_moverole.php";
					break;
				case 6:
					include "form_gouvernance/form_deleterole.php";
					break;
				case 7:
					include "form_gouvernance/form_addpolicy.php";
					break;
				case 8:
					include "form_gouvernance/form_editpolicy.php";
					break;
				case 9:
					include "form_gouvernance/form_deletepolicy.php";
					break;
			

				default:
					echo "<h1>Formulaire $param en position $id</h1>";
		
					echo "<input type='text' name='nom_$id' id='nom_$id'>";
			}
		} else {
			echo "<input type='hidden' id='form_target' value='/formulaires/form_gouvernance.php'/>";
			echo "<input type='hidden' name='circle' value='".@$_GET["circle"]."'/>";
			echo "<input type='hidden' name='meeting' value='".@$_GET["meeting"]."'/>";
			echo "<input type='hidden' name='action' id='action' value='createTab'/>";
			echo "<input type='hidden' name='param' id='param' value=''/>";
			echo "<input type='hidden' name='param_txt' id='param_txt' value=''/>";
			echo "<div style='text-align:center; font-size:larger'><b>De quoi avez-vous besoin?</b></div>";
			echo "<table align='center'  border=0><tr><td><div class='ui-corner-all omo-circle-action' >";
			echo "<p><button type='submit' value='1' name='yopola' class='nextStep'>Ajouter un cercle</button></p>";
			echo "<p><button type='submit' value='2' name='yopola' class='nextStep'>Modifier un cercle</button></p>";
			echo "<p><button type='submit' value='3' name='yopola' class='nextStep'>Supprimer un cercle</button></p>";
			echo "</div></td></tr><tr><td><div class='ui-corner-all omo-role-action' >";
			echo "<p><button type='submit' value='4' name='yopola' class='nextStep'>Ajouter un rôle</button></p>";
			echo "<p><button type='submit' value='5' name='yopola' class='nextStep'>Modifier un rôle</button></p>";
			echo "<p><button type='submit' value='10' name='yopola' class='nextStep'>Déplacer un rôle</button></p>";
			echo "<p><button type='submit' value='6' name='yopola' class='nextStep'>Supprimer un rôle</button></p>";
			echo "</div></td></tr><tr><td><div class='ui-corner-all omo-policy-action' >";
			echo "<p><button type='submit' value='7' name='yopola' class='nextStep'>Ajouter une politique</button></p>";
			echo "<p><button type='submit' value='8' name='yopola' class='nextStep'>Modifier une politique</button></p>";
			echo "<p><button type='submit' value='9' name='yopola' class='nextStep'>Supprimer une politique</button></p>";  
			echo "</div></td></tr></table>";	
?>
<script>
	$(".nextStep").button().css('width','300px').css('display','block').click(function(){
		$("#param").val($(this).val());$("#param_txt").val($(this).text())
	});
   $( "#dialogStd" ).dialog({ buttons: [ { text: "ScratchPad", click:showHideScratchPad}, {text: "Fermer", click: function() { $( this ).dialog( "close" ); }} ] });

</script>
<?
		}
		
	} else
		
	// ***********************************************************************************
	// ********** Action postée
	// """""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""
	if (isset($_POST["action"])) {
	
		switch ($_POST["action"]) {
			case "saveGouv" :
				include ("action_save_meeting.php");
			break;
			case "createTab":
	
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<script>
    var tabs = $( "#tabs_gouv" ).tabs({

  beforeLoad: function( event, ui ) {
    if ( ui.tab.data( "loaded" ) ) {
      event.preventDefault();
      return;
    }
	ui.panel.html("Chargement. Merci de patienter..."); 
    ui.jqXHR.success(function() {
      ui.tab.data( "loaded", true );
    });
  }
} );

    tabs.find( ".ui-tabs-nav" ).sortable({
      axis: "y",
      stop: function() {
        tabs.tabs( "refresh" );
      }
    });
	tabs.addClass( "ui-tabs-vertical ui-helper-clearfix" );
    $( "#tabs_gouv li" ).removeClass( "ui-corner-top" ).addClass( "ui-corner-left" );
    
   
      tabTemplate = "<li><a id='#{id}' href='#{href}'>#{label}</a></li>",
      tabCounter = 2;


	$(".new_action").button().css('width','300px').css('display','block').click(function(){
		addTab($(this).val(),$(this).text());
		$(this).closest('.ui-dialog-content').dialog("close");
		});
	
	 var dialog_action= $( "#new_action" ).dialog({
      autoOpen: false,
      height: 422,
      width: 480,
      modal: true});
      
	function newAction() {
		dialog_action.dialog( "open" );
	}
      // actual addTab function: adds new tab using the input from the form above
    function addTab(no,text) {
      var label = text,
        id = tabCounter,
        li = $( tabTemplate.replace( /#\{href\}/g, "/formulaires/form_gouvernance.php?circle=<?=$_POST["circle"]?>&action=getForm&param="+no+"&id=" + id ).replace( /#\{label\}/g, label ).replace( /#\{id\}/g, "tab_gouv_"+id ) );

 
      tabs.find( ".ui-tabs-nav" ).append( li );
      tabs.tabs( "refresh" );
      tabs.tabs({ active: tabCounter-1 });
      tabCounter++;
    }
	function deleteTab () {
		var panelId = tabs.find( ".ui-tabs-active" ).remove().attr( "aria-controls" );
        $( "#" + panelId ).remove();
        tabs.tabs( "refresh" );

	}
 
    // addTab button: just opens the dialog

  
    $( "#dialogStd" ).dialog({ buttons: [ { text: "ScratchPad", click:showHideScratchPad}, { text: "Ajouter une action", click:newAction},{ text: "Supprimer cette action", click:deleteTab},{ text: "Enregistrer", click: function() { $( "#formulaire").submit(); } }, {text: "Annuler", click: function() { $( this ).dialog( "close" ); }} ] });
  
	// addTab button: just opens the dialog
    $( "#send_form" ).button();
      
 
</script>
  <style>

#tabs_gouv  { 
    position: relative; 
    padding-left: 15.3em; 
    height:382px;
    overflow:auto;
  
} 
 #tabs_gouv .ui-tabs-panel {
	overflow:auto;
	height:92%;
}
 #tabs_gouv  .ui-tabs-nav { 
    position: absolute; 
    left: 0.25em; 
    top: 0.25em; 
    bottom: 0.25em; 
    width: 15em; 
    padding: 0.2em 0 0.2em 0.2em; 
} 
 #tabs_gouv .ui-tabs-nav li { 
    right: 1px; 
    width: 100%; 
    border-right: none; 
    border-bottom-width: 1px !important; 
    -moz-border-radius: 4px 0px 0px 4px; 
    -webkit-border-radius: 4px 0px 0px 4px; 
    border-radius: 4px 0px 0px 4px; 
    overflow: hidden; 
} 
 #tabs_gouv .ui-tabs-nav li.ui-tabs-selected, 
 #tabs_gouv .ui-tabs-nav li.ui-state-active { 
    border-right: 1px solid transparent; 
} 
 #tabs_gouv .ui-tabs-nav li a { 
    float: right; 
    width: 100%; 
    text-align: right; 
    }
 

  #add_tab { cursor: pointer; }  

  </style>
 <?
 
 		// Affichage des infos cachées pour post du formulaire
  		echo "<input type='hidden' id='form_target' value='/formulaires/form_gouvernance.php'>";
		echo "<input type='hidden' name='circle' value='".@$_POST["circle"]."'>";
		echo "<input type='hidden' name='meeting' value='".@$_POST["meeting"]."'>";
		echo "<input type='hidden' name='action' value='saveGouv'>";
		// Affichage du système d'onglet
?>
  <div id="tabs_gouv">
  <ul>
    <li><a id="tab_gouv_1" href="/formulaires/form_gouvernance.php?circle=<?=$_POST["circle"]?>&action=getForm&param=<?=$_POST["param"]?>&id=1"><?=utf8_decode($_POST["param_txt"])?></a></li>
  </ul>
</div>

<div id="new_action" title="Ajouter une action">
		<table align='center' border='0'><tr><td><div class='ui-corner-all omo-circle-action' >
  		<button  name='new_action' class='new_action' value='1'>Ajouter un cercle</button>
		<button name='new_action' class='new_action' value='2'>Modifier un cercle</button>
  		<button name='new_action' class='new_action' value='3'>Supprimer un cercle</button>
  		</div></td></tr><tr><td><div class='ui-corner-all omo-role-action' >
  		<button  name='new_action' class='new_action' value='4'>Ajouter un rôle</button>
		<button  name='new_action' class='new_action' value='5'>Modifier un rôle</button>
		<button  name='new_action' class='new_action' value='10'>Déplacer un rôle</button>
  		<button name='new_action' class='new_action' value='6'>Supprimer un rôle</button>
  		</div></td></tr><tr><td><div class='ui-corner-all omo-policy-action' >
  		<button name='new_action' class='new_action' value='7'>Ajouter une politique</button>
		<button name='new_action' class='new_action' value='8'>Modifier une politique</button>
  		<button  name='new_action' class='new_action' value='9'>Supprimer une politique</button>
		</div></td></tr></table>


</div>
<?
		break;
		}
	} else {
	
	
	// *****************************************************************
	// ********** Affichage par défaut *********************************
	// *****************************************************************
	
	if (isset($_GET["circle"]) || isset($_GET["meeting"])) {
	
 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
	<script src="plugins/tinymce/jquery.tinymce.min.js"></script>	

<script>
$("#formulaire").ready(function(){

 $("#formulaire").submit(function() {
	// Envoie le formulaire en AJAX (méthode POST), la destination étant définie par l'élément à l'ID form_target 
 	$.post($("#form_target")[0].value, $("#formulaire").serialize()) 
        .done(function(data, textStatus, jqXHR) {
            if (textStatus="success")
            {
            	// Affiche les données en retour en remplacement du contenu du formulaire (le contenant reste) 
                $("#formulaire")[0].innerHTML=data;
                // Intérprète les scripts retournés (à vérifier si ça fonctionne)
                eval($("#formulaire").find("script").text());
			}
            else {
            	// Problème d'envoi
            	alert("Echec!");
            
            }
        });
        // Bloque la procédure standard d'envoi
        return false;
});

});
</script>
<?
		echo "<div class='scratchPad' id='scratch' style='display:none'><textarea placeholder='Scratchpad. Ecrivez ce que vous souhaitez ici'></textarea></div>";
		echo "<form id='formulaire'>";
		echo "</form>";
?>
<script>
	function showHideScratchPad() {
		$( "#scratch" ).toggle( "drop", 500 );
	}
	$("#formulaire").load('/formulaires/form_gouvernance.php?action=new&<?
	
		if (isset($_GET["meeting"])) {
			echo "meeting=".$_GET["meeting"]."&circle=".@$_GET["circle"];
		} else {
			echo "circle=".$_GET["circle"];
		}
		
	?>');
</script>
<?

	}
}
?>
