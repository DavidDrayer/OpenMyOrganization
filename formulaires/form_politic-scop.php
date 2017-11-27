<?
	include_once("../include.php");

	if (isset($_POST["action"])) {
	//Si le formulaire a été envoyé
		if ($_POST["action"]=="editPolitics") {
		
		$scopeId=$_POST["scopeId"];		
		$scope= $_SESSION["currentManager"]->loadScope($scopeId); //On recharge la strategie actuel du cercle
		$politics= $scope->getPolitiques();
		
		echo "<form id='formulaire'>";
		echo "<input type='hidden' id='form_target' value='/formulaires/form_politic-scop.php'>";
		echo "<input type='hidden' name='scopeId' value='".$scopeId."'>";
		echo "<input type='hidden' name='action' value='savePolitics'>";

		echo "<h3>Description des politiques du domaine</h3>";
		echo "<div><textarea id='politicsText' name='politicsText' class='tinymce' style='width:100%'>".$politics."</textarea></div>";
		echo "</form>";
	?>

		<script>
if (typeof tinymce != "undefined") tinymce.remove();			
           $('textarea.tinymce').tinymce({
                    // Location of TinyMCE script
					menubar : false,
					plugins: "link, paste",
					extended_valid_elements : "p/div/tr/li,br/td",
                    invalid_elements : "span, table, tr, img, button, input, form, ul, li",
					paste_auto_cleanup_on_paste : true,
					paste_remove_styles: true,
		            paste_remove_styles_if_webkit: true,
		            paste_strip_class_attributes: true,					toolbar: "undo redo | bold italic | bullist numlist outdent indent | link",
					statusbar : false

            });
			
$( "#dialogStd" ).dialog({ buttons: [{ text: "Sauvegarder", click: function() { $( "#formulaire").submit(); } }, {text: "Annuler", click: function() { $( this ).dialog( "close" ); }} ] });
</script>
	
		<?php }
		
		if ($_POST["action"]=="savePolitics") {
				
		$scope=$_SESSION["currentManager"]->loadScope($_POST["scopeId"]);
		$scope->setPolitiques(utf8_decode($_POST["politicsText"]));
		$_SESSION["currentManager"]->save($scope);
		?>
		<script>
		$( "#dialogStd" ).dialog("close");
		location.reload();
		</script>
		<?php
		}	
	}
	else{
	
		if (isset($_GET["domaine"]) && $_GET["domaine"]!="") {
			$scopeId=$_GET["domaine"];
			$scope= $_SESSION["currentManager"]->loadScope($scopeId); //On recharge la strategie actuel du cercle
		}
		?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<script src="plugins/tinymce/tinymce.min.js"></script>	
<script src="plugins/tinymce/jquery.tinymce.min.js"></script>	
<?php
	$roleId = $scope->getRoleId();
	
	echo "<form id='formulaire'>";
	echo "<input type='hidden' id='form_target' value='/formulaires/form_politic-scop.php'>";
	echo "<input type='hidden' name='scopeId' value='".$scopeId."'>";
	echo "<input type='hidden' name='action' value='editPolitics'>";
	$politics= $scope->getPolitiques();
	if($politics == ""){ echo "Aucune politique n'a été défini"; } else { echo $politics;}
	echo "</form>";
	
	$userId = $_SESSION["currentUser"]->getId();
	$role = $_SESSION["currentManager"]->loadRole($roleId);
	//$roleFillers=$role->getRoleFillers();
	//for ($i=0;($i<count($roleFillers));$i++){
	//	if($roleFillers[$i]->getUserId() == $userId) {
	if ($_SESSION["currentUser"]->isRole($role)) {
	?>
	<script type="text/javascript">	
	$( "#dialogStd" ).dialog({ buttons: [{ text: "Editer", click: function() { $( "#formulaire").submit(); } }, {text: "Fermer", click: function() { $( this ).dialog( "close" ); }} ] });
	</script>
	<?php
		}
	//}
	?>
	
<script type="text/javascript">				
			 // Prevent jQuery UI dialog from blocking focusin
			$(document).on('focusin', function(e) {
			    if ($(event.target).closest(".mce-window").length) {
					e.stopImmediatePropagation();
				}
			});
					
			$("#formulaire").submit(function() {
	  	
		// Envoie le formulaire en AJAX (méthode POST), la destination étant définie par l'élément à l'ID form_target 
	 	$.post($("#form_target")[0].value, $("#formulaire").serialize()) 
	        .done(function(data, textStatus, jqXHR) {
	            if (textStatus="success")
	            {
	            	// Traite une éventuelle erreur
	            	if (data.indexOf("Erreur")>0) {
	            		eval(data);
	            	} else {
		            	// Affiche les données en retour en remplacement du contenu du formulaire (le contenant reste) 
		                $("#formulaire")[0].innerHTML=data;
		                // Intérprète les scripts retournés (à vérifier si ça fonctionne)
		                eval($("#formulaire").find("script").text());
	                }
				}
	            else {
	            	// Problème d'envoi
	            	alert("Echec!");
	            }
	        });
	        // Bloque la procédure standard d'envoi
	        return false;
	});
        </script>
	<?
	}	
	?>
