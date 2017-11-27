<?
	include_once("../include.php");
	if (!isset($_SESSION["currentUser"]) || $_SESSION["currentUser"]->getId()<2) {
		// Si l'utilisateur n'est pas identifié (NULL ou visiteur)
		?>
		<script>
			alert("Vous avez été déconnectés du serveur.");
			$( "#dialogStd" ).dialog( "close" );
			 location.reload();
		</script>
<?
		exit;
	}
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
				case 0:
					$_GET["role"]="";
					include "form_generic/form_tension.php";
					break;
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
				case 11:
					include "form_triage/form_addproject.php";
					break;
				case 12:
					$_GET["role"]="";
					include "form_triage/form_addaction.php";
					break;
				case 13:
					$_GET["role"]="";
					include "form_triage/form_addtension.php";
					break;
				case 14:
					$_GET["role"]="";
					include "form_triage/form_addinfo.php";
					break;

				default:
					echo "<h1>Formulaire $param en position $id</h1>";
					echo "<input type='text' name='nom_$id' id='nom_$id'>";
			}
		}
		exit;
	} else 
		if (isset($_POST["action"])) {
			switch ($_POST["action"]) {
				case "saveGouv" :
					include ("action_save_meeting.php");
				break;
			}
			exit;
		}
	
	if (isset($_GET["circle"]) || isset($_GET["meeting"])) {
	$meeting=$_SESSION["currentManager"]->loadMeeting($_GET["meeting"]);
	$isSecretary=($meeting->getSecretaryId()==$_SESSION["currentUser"]->getId());
	$isInProcess=($meeting->getOpeningTime()!=null && $meeting->getClosingTime()==null);
	$closed=($meeting->getClosingTime()!=null);
	if (isset($_GET["tension"])) {
		$tension=$_SESSION["currentManager"]->loadTension($_GET["tension"]);
		$isMy=($tension->getUserId()==$_SESSION["currentUser"]->getId());
	} else $isMy=false;
?>
<style>
	textarea[disabled] {
		background:#f0efee;
		border:1px solid #aaaaaa;
		color:#aaaaaa;
	}
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
		echo "<div class='scratchPad' id='scratch' style='display:none'><textarea placeholder='Scratchpad. Ecrivez ce que vous souhaitez ici'></textarea></div>";
		echo "<form id='formulaire'>";
		
?>

 
 <?
 
 		// Affichage des infos cachées pour post du formulaire
  		echo "<input type='hidden' id='form_target' value='/formulaires/form_meeting2.php'>";
		echo "<input type='hidden' name='circle' value='".@$_GET["circle"]."'>";
		echo "<input type='hidden' name='meeting' value='".@$_GET["meeting"]."'>";
		echo "<input type='hidden' name='tension' value='".@$_GET["tension"]."'>";
		echo "<input type='hidden' name='action' value='saveGouv'>";
		// Affichage du système d'onglet
?>
  <div id="tabs_gouv">
  <ul>
    <li><a id="tab_gouv_1" href="/formulaires/form_meeting2.php?meeting=<?=$_GET["meeting"]?>&action=getForm&param=0&id=1&tension=<?=@$_GET["tension"]?>">Informations</a></li>
  </ul>
</div>

<div id="new_action" title="Ajouter un élément">


<?
	// Adapte le contenu en fonction du type de réunion
if (isset($_GET["meeting"])) {
	$meeting=$_SESSION["currentManager"]->loadMeeting($_GET["meeting"]);
	if ($meeting->getMeetingTypeId()==2) {
		echo "<table id='table_new_action' align='center'  border=0><tr><td><div class='ui-corner-all omo-project-action' >";
		echo "<p><button  name='new_action' class='new_action' value='11'>Ajouter un projet</button></p>";
		echo "<p><button  name='new_action' class='new_action' value='12'>Ajouter une action</button></p>";
		echo "<p><button  name='new_action' class='new_action' value='14'>Ajouter un point info</button></p>";
		echo "</div></td></tr></table>";
	}
	if ($meeting->getMeetingTypeId()==1) {
		echo "<table id='table_new_action' align='center' border='0'><tr><td><div class='ui-corner-all omo-circle-action' >";
  		echo "<button  name='new_action' class='new_action' value='1'>Ajouter un cercle</button>";
		echo "<button name='new_action' class='new_action' value='2'>Modifier un cercle</button>";
  		echo "<button name='new_action' class='new_action' value='3'>Supprimer un cercle</button>";
  		echo "</div></td></tr><tr><td><div class='ui-corner-all omo-role-action' >";
  		echo "<button  name='new_action' class='new_action' value='4'>Ajouter un rôle</button>";
		echo "<button  name='new_action' class='new_action' value='5'>Modifier un rôle</button>";
		echo "<button  name='new_action' class='new_action' value='10'>Déplacer un rôle</button>";
  		echo "<button name='new_action' class='new_action' value='6'>Supprimer un rôle</button>";
  		echo "</div></td></tr><tr><td><div class='ui-corner-all omo-policy-action' >";
  		echo "<button name='new_action' class='new_action' value='7'>Ajouter une politique</button>";
		echo "<button name='new_action' class='new_action' value='8'>Modifier une politique</button>";
  		echo "<button  name='new_action' class='new_action' value='9'>Supprimer une politique</button>";
		echo "</div></td></tr></table>";
	}

}

?>
</div>		
<?	
		
		echo "</form>";
?>
<script>
	
	  tabTemplate = "<li><a id='#{id}' href='#{href}'>#{label}</a></li>",
      tabCounter = 2;



var tabs = $( "#tabs_gouv" ).tabs({  
	        activate: function( event, ui ) {
            // Si c'est pas un formulaire de tension, affiche le bouton supprimer
            //alert (ui.newPanel.find("input[class='nodelete']").length);
			if (ui.newPanel.find("input[class='nodelete']").length==0) {
				$(".ui-dialog-buttonpane button:contains('Supprimer')").button('enable');
			} else {
				$(".ui-dialog-buttonpane button:contains('Supprimer')").button('disable');
			}
            
        }   ,   
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

	function showHideScratchPad() {
		$( "#scratch" ).toggle( "drop", 500 );
	}
	function newAction() {
		dialog_action.dialog( "open" );
		
	}

	function deleteTab () {
		var panelId = tabs.find( ".ui-tabs-active" ).remove().attr( "aria-controls" );
        $( "#" + panelId ).remove();
        tabs.tabs( "refresh" );

	}
    function addTab(no,text) {
      var label = text,
        id = tabCounter,
        li = $( tabTemplate.replace( /#\{href\}/g, "/formulaires/form_meeting2.php?circle=<?=$_GET["circle"]?>&action=getForm&param="+no+"&id=" + id ).replace( /#\{label\}/g, label ).replace( /#\{id\}/g, "tab_gouv_"+id ) );

 
      tabs.find( ".ui-tabs-nav" ).append( li );
      tabs.tabs( "refresh" );
      tabs.tabs({ active: tabCounter-1 });
      tabCounter++;
    }
    	
	var dialog_action= $( "#new_action" ).dialog({
    autoOpen: false,
    height: 152,
    width: 480,
    modal: true,
    open: function( event, ui ) {
		$(this).height($(this).find("#table_new_action").height());
		$(this).dialog("option", "position", "center");
	}
    });
      
    // Les boutons dépendent directement de l'état de la réunion  
 <?
	echo '$("#dialogStd").dialog({ buttons: [ ';
	if ($isSecretary && !$closed) echo '{ text: "ScratchPad", click:showHideScratchPad}, ';
	if ($isSecretary && $isInProcess) echo '{text: "Ajouter un élément", click:newAction},';
	if ($isSecretary && $isInProcess) echo '{text: "Supprimer cet élément", click:deleteTab},';
	if (!$closed) {
		if ($isSecretary || $isMy)
			echo '{text: "Enregistrer", click: function() { $( "#formulaire").submit(); } },';
		echo '{text: "Annuler", click: function() { $( this ).dialog( "close" ); }} ] });';
	} else {
		echo '{text: "Fermer", click: function() { $( this ).dialog( "close" ); }} ] });';
	}
?>
 
    $(".ui-dialog-buttonpane button:contains('Supprimer')").button('disable');

	$(".new_action").button().css('width','300px').css('display','block').click(function(){
		addTab($(this).val(),$(this).text());
		$(this).closest('.ui-dialog-content').dialog("close");
	});
	
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
				}).fail(function(xhr) {
					alert( xhr.responseText );
				});
				// Bloque la procédure standard d'envoi
				return false;
		});

	}); // formulaire.ready
</script>

<?
	} else {
		// Ni cercle ni meeting spécifié... erreur
	}
?>
