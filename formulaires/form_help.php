<?
	// Inclusion standard pour acc�s � la base de donn�es - DDr 4.6.2014
	include_once("../include.php");
	// Instantiation du gestionnaire de base de donn�e - DDr 4.6.2014
	$manager=new \datamanager\SqlManager($_SESSION["currentDB"]);
	
	// Il s'agit d'un POST du formulaire - DDr 4.6.2014
	if (isset($_POST["action"])) {
		switch ($_POST["action"]) {
			case "editHelp":
				// Si l'ID est sp�cifi�, ouvre l'objet - DDr 4.6.2014
				if (isset($_POST["id"]) && $_POST["id"]!="") {
					$help=$manager->loadHelp($_POST["id"]);
				}
				 
				// Sinon cr�e un nouvel objet - DDr 4.6.2014
				else {
					$help=new \holacracy\Help ();
				}
				
				// Met � jour les informations en fonction du formulaire - DDr 4.6.2014
				$help->setKey(str_replace("\\","\\\\",utf8_decode($_POST["helpKey"]))); // Remplace les \ par des \\ - DDr 4.6.2014
				$help->setTitle(utf8_decode($_POST["helpTitle"]));
				$help->setText(utf8_decode($_POST["helpContent"]));
				
				// Le sauve dans la base de donn�e - DDr 4.6.2014
				$manager->save($help);
				
				// Affiche un petit message (Qui ne sera pas lu si le formulaire est correctement ferm�) - DDr 4.6.2014
				echo "R�ussi";
				
				// Ferme le formulaire - DDr 4.6.2014
?>
	<script>
	    $( "#dialogStd" ).dialog("close");
	    $("#main_waiting_screen").css("display","");
	    location.reload();
	</script>
<?				
				// Ne continue pas le processus - DDr 4.6.2014
				exit;
				
				break;
			default:
				echo "Action inconnue";
				exit;
		}
	
	} else
	// Il s'agit d'�diter un block - DDr 4.6.2014
	if (isset($_GET["id"])) {
		// Lit les infos sur le block et affiche le formulaire de saisie - DDr 4.6.2014
		$help=$manager->loadHelp($_GET["id"]);
		$key=$help->getKey();
		$title=$help->getTitle();
		$text=$help->getText();
		$id=$help->getId();
	} else 
	// Il s'agit de cr�er un block - DDr 4.6.2014
	if (isset($_GET["key"])) {
		$key=$_GET["key"];
		$key=str_replace("http://","",$key);
		$key=str_replace($_SERVER["SERVER_NAME"],"",$key);
		$key=str_replace("?","\\?",$key);

		$string = 'April 15, 2003';
$key=preg_replace('/([0-9]+)/i', '[0-9]+', $key);

		$title="";
		$text="";
		$id="";		
	} else {
		// Aucun des cas pr�c�dents, comportement par d�faut - DDr 4.6.2014
		echo "Erreur";
		exit;
	}

	
?>
	<script src="plugins/tinymce/tinymce.min.js"></script>	
	<script src="plugins/tinymce/jquery.tinymce.min.js"></script>	
	

	<form id='formulaire'>
<?
	echo "<input type='hidden' id='form_target' value='/formulaires/form_help.php'>";
	echo "<input type='hidden' name='id' value='".$id."'>";
	echo "<input type='hidden' name='action' value='editHelp'>";

?>			
		<strong>Cl� de reconnaissance</strong><br/>
		<input class='fill' type='text' name='helpKey' id='helpKey' value='<?=htmlentities($key,ENT_QUOTES)?>'/><br/><br/>
		<strong>Titre</strong><br/>
		<input class='fill' type='text' name='helpTitle' id='helpTitle' value='<?=htmlentities($title,ENT_QUOTES)?>'/><br/><br/>
		<strong>Description</strong><br/>
		<textarea class='tinymce' rows="15" cols="80" name="helpContent" id="helpContent"><?=htmlentities($text,ENT_QUOTES)?></textarea>
	</form>
	<script>
		// transforme le textarea en �diteur - DDr 4.6.2014
       $('textarea.tinymce').tinymce({
            // Location of TinyMCE script
			menubar : false,
			plugins: "link",
			toolbar: "undo redo | bold italic | bullist numlist outdent indent | link",
			statusbar : false

        });

         // R�soud des probl�mes de Focus lorsque TinyMCE est utilis� dans des bo�tes de dialog - DDr
		$(document).on('focusin', function(e) {
		    if ($(event.target).closest(".mce-window").length) {
				e.stopImmediatePropagation();
			}
		});
		
		// Ajoute un bouton pour sauver - DDr 4.6.2014
         $( "#dialogStd" ).dialog({ buttons: [{ text: "Sauver", click: function() { $( "#formulaire").submit(); } }, {text: "Annuler", click: function() { $( this ).dialog( "close" ); }} ] });

		// Proc�dure pour envoyer le formulaire en AJAX - DDr 4.6.2014
		$("#formulaire").submit(function() {
		  	
			// Envoie le formulaire en AJAX (m�thode POST), la destination �tant d�finie par l'�l�ment � l'ID form_target 
		 	$.post($("#form_target")[0].value, $("#formulaire").serialize()) 
		        .done(function(data, textStatus, jqXHR) {
		            if (textStatus="success")
		            {
		            	// Traite une �ventuelle erreur en ex�cutant le code retourn� (ce dernier devant afficher l'erreur
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
		            	alert(textStatus);
		            
		            }
		        });
		        // Bloque la proc�dure standard d'envoi
		        return false;
		});
	
		
	</script>
