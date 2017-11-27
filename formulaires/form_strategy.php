
<?
	include_once("../include.php");
	if (isset($_POST["action"])) {
	//Si le formulaire a �t� envoy�
		if ($_POST["action"]=="addStrategy") {
	
		// Cr�e ou met � jour la metrics
			$circle=$_SESSION["currentManager"]->loadCircle($_POST["id"]);
			$circle->setStrategy(utf8_decode($_POST["strategyText"]));
			$_SESSION["currentManager"]->save($circle);

		// Texte de validation
		echo "La strat�gie a �t� mise � jour";	?>

		<script>

    $( "#dialogStd" ).dialog("close");
    location.reload();
    //$( "#dialogStd" ).dialog({ buttons: [ {text: "Fermer", click: function() { $( this ).dialog( "close" ); location.reload(); }} ] });
</script>
	
		<?php }
	
	}
	else{
	
		// Initialise pour l'�dition d'un metrics
		if (isset($_GET["id"]) && $_GET["id"]!="") {
			$id=$_GET["id"];
			$strategy= $_SESSION["currentManager"]->loadStrategy($_GET["id"]); //On recharge la strategie actuel du cercle
		}
		?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<script src="plugins/tinymce/tinymce.min.js"></script>	
<script src="plugins/tinymce/jquery.tinymce.min.js"></script>	
<?php

	echo "<form id='formulaire'>";
	echo "<input type='hidden' id='form_target' value='/formulaires/form_strategy.php'>";
	echo "<input type='hidden' name='id' value='".$id."'>";
	echo "<input type='hidden' name='action' value='addStrategy'>";

	echo "<h3>Description de la strat�gie</h3>";
	echo "<div><textarea id='strategyText' name='strategyText' class='tinymce' style='width:100%'>".$strategy."</textarea></div>";
	echo "</form>";	?>
	
<script type="text/javascript">	
			if (typeof tinymce != "undefined") tinymce.remove();			
           $('textarea.tinymce').tinymce({
                    // Location of TinyMCE script
					menubar : false,
					plugins: "link, paste",
					extended_valid_elements : "p/div/tr,br/td",
                    invalid_elements : "span, table, tr, img, button, input, form",
					paste_auto_cleanup_on_paste : true,
					paste_remove_styles: true,
		            paste_remove_styles_if_webkit: true,
		            paste_strip_class_attributes: true,					
					toolbar: "undo redo | bold italic | bullist numlist outdent indent | link",
					statusbar : false

            });
			
			 // Prevent jQuery UI dialog from blocking focusin
			$(document).on('focusin', function(e) {
			    if ($(event.target).closest(".mce-window").length) {
					e.stopImmediatePropagation();
				}
			});
			
			$( "#dialogStd" ).dialog({ buttons: [{ text: "Ajouter", click: function() { $( "#formulaire").submit(); } }, {text: "Annuler", click: function() { $( this ).dialog( "close" ); }} ] });
			
			$("#formulaire").submit(function() {
	  	
		// Envoie le formulaire en AJAX (m�thode POST), la destination �tant d�finie par l'�l�ment � l'ID form_target 
	 	$.post($("#form_target")[0].value, $("#formulaire").serialize()) 
	        .done(function(data, textStatus, jqXHR) {
	            if (textStatus="success")
	            {
	            	// Traite une �ventuelle erreur
	            	if (data.indexOf("Erreur")>0) {
	            		eval(data);
	            	} else {
	            	
	            	
		            	// Affiche les donn�es en retour en remplacement du contenu du formulaire (le contenant reste) 
		                $("#formulaire")[0].innerHTML=data;
		                // Int�rpr�te les scripts retourn�s (� v�rifier si �a fonctionne)
		                eval($("#formulaire").find("script").text());
	                }
				}
	            else {
	            	// Probl�me d'envoi
	            	alert("Echec!");
	            
	            }
	        });
	        // Bloque la proc�dure standard d'envoi
	        return false;
	});
        </script>
	<?
	}	
	?>