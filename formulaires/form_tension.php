<?
	include_once("../include.php");
	
	
	
	/*
	Le clique sur Editer doit integré l'ajout du Role Checked et du type checked
	Par ailleurs, le Editer doit être ouvert par defaut sur la bonne fenêtre
	Il faut aussi que l'enregistrement ne fasse pas un doublon mais un update
	*/
	
	if (isset($_POST["action"])) {
	
		if ($_POST["action"]=="addTension") {
			// Vérifie le formulaire
			if ($_POST["tensionTitle"]=="") {
				echo "/* Erreur */\n alert('Erreur!! Le titre n\'est pas rempli.');$('#tensionTitle').focus();"; exit;
			}
			if ($_POST["roleID"]=="") {
				echo "/* Erreur */\n alert('Erreur!! Le rôle n\'est pas défini.');$('#role_id').focus();"; exit;
			}
			if (!isset($_POST["type"]) || $_POST["type"]=="") {
				echo "/* Erreur */\n alert('Erreur!! Le type n\'est pas choisit.');$('#gouvernance').focus();"; exit;
			}
			if ($_POST["tension"]=="") {
				echo "/* Erreur */\n alert('Erreur!! La description de la tension n\'est pas remplie. \\nVous risquez d\'en oublier le propos d\'ici la prochaine réunion.');$('#tension').focus();"; exit;
			}
			
			$circle = $_SESSION["currentManager"]->loadCircle($_POST["id_circle"]);
			$org = $circle->getOrganisation();
			
			
			// Crée une nouvelle tension
			$tension=new \holacracy\TensionMoi();
			$tension->setName(utf8_decode($_POST["tensionTitle"]));
			$tension->setDescription(utf8_decode($_POST["tension"]));
			$tension->setCircleId($circle->getId());
			$tension->setOrgId($org->getId());
			$tension->setId($_POST["id_tension"]);
			$tension->setRoleId($_POST["roleID"]);
			$tension->setUserId($_SESSION["currentUser"]->getId());
			$tension->setType($_POST["type"]); 
			
			// Et le sauve
			$_SESSION["currentManager"]->save($tension);
			$_POST["tension"]=$tension->getId();			
			
			// Confirme l'enregistrement
			echo "Rechargement de la page...";
			echo "<script>$('#dialogStdContent').load('/formulaires/form_tension.php?id=".$_POST["id_circle"]."');</script>";
			exit;
		}
	}
?>
		<!-- Affichage d'un système à onglet -->
		<div id="tabs-tension">
		  <ul>
		    <li><a href="#tabs-tension-1">Liste des tensions</a></li>
		    <li class="newtension"><a href="#tabs-tension-2">Tension</a></li>
		  </ul>
		  <div id="tabs-tension-1">
<?

	// Affichage de la liste des bugs actuellement dans la base de donnée
	$circle = $_SESSION["currentManager"]->loadCircle($_GET['id']);
	
		function writetensionlist($tensions) {
		echo "<table class='tension'>";		
		foreach ($tensions as $tension) {	
			echo "<tr>";
			echo "<td class='tensionnamerole'>";
			if ($tension->getRoleName()!="") {
				echo $tension->getRoleName();
			} else {
				echo "Membre du cercle";
			}
			echo "</td><td class='tensiontxt'><div id='tension-".$tension->getId()."'class='omo-light-accordion lightension'><h3><span class='omo-label'>".$tension->getName()."</span></h3><div>".$tension->getDescription()."</div>";	
			echo "</div></td><td class='tensionedit'><a href='/formulaires/form_tension.php?id=".$_GET['id']."&action=edit&tid=".$tension->getId()."' class='dialogPage'><img src='/style/templates/images/edit.png'></a> <a href='/ajax/deletetensions.php?id=".$_GET['id']."&tid=".$tension->getId()."' class='dialogPage'><img src='/style/templates/images/delete.png'></a></td></tr>";
		}
		echo "</table>";
		}
	
		// Récemment clôturés
		
		$tensions = $_SESSION["currentManager"]->loadTensoinMoiList($circle,$_SESSION["currentUser"],'gouvernance');
		echo "<h2>Gouvernance</h2>";
		if (count($tensions)>0) {
			
			writetensionlist($tensions);
		}
		else{ echo "Aucunes tensions enregistrées";}
		
		$tensions = $_SESSION["currentManager"]->loadTensoinMoiList($circle,$_SESSION["currentUser"],'triage');
		echo "<h2>Triage</h2>";
		if (count($tensions)>0) {
			writetensionlist($tensions);
		}
		else{ echo "Aucunes tensions enregistrées";}		
			
		//Pour edition
		
		if (isset($_GET["action"])) {
	
			if ($_GET["action"]=="edit") {
			//On charge les données 
			$tid = $_GET["tid"];
			$tension = $_SESSION["currentManager"]->loadTensionMoi($tid);
			$tensioname = $tension->getName();
			$tensiondescrpt = $tension->getDescription();
			$tensionroleid = $tension->getRoleId();	
			$tensiontype = $tension->getType();	
			}	
		}
		else{
		$tensioname = "";
		$tensiondescrpt = "";
		$tid = 0;
		$tensionroleid = 0;
		$tensiontype = "null";
		}
?>

		  </div>
		  <div id="tabs-tension-2">
		  
			 <form id='formulaire'>
<?
	  		echo "<input type='hidden' id='form_target' value='/formulaires/form_tension.php'>";
			echo "<input type='hidden' name='action' value='addTension'>";
			echo "<input type='hidden' name='id_circle' value='".$_GET['id']."'>";
			echo "<input type='hidden' name='id_tension' value='".$tid."'>"; //valeur 0 par défaut, sinon c'est une édition
			
			

			// Inutile, la personne sait qui elle est
			//if (isset($_SESSION["currentUser"])) {		
				
				//echo "<strong>Votre nom : </strong>".$_SESSION["currentUser"]->getFirstName()." ".$_SESSION["currentUser"]->getLastName()."<br/><br/>";}
?>
				<strong>Titre</strong><input type='text' name='tensionTitle' id='tensionTitle' value='<? echo $tensioname; ?>' class='fill'/><br/>
				<br/><strong>Rôle</strong>   
				<?
				$roles = $_SESSION["currentUser"]->getRoles($circle);
				
				echo "<select id='role_id' name='roleID'>";
				echo "<option value=''>Choisissez...</option>";
				echo "<option value='0' ".($tensionroleid==0?"selected":"").">Membre de cercle (".$_SESSION["currentUser"]->getFirstName()." ".$_SESSION["currentUser"]->getLastName().")</option>";
				foreach ($roles as $role) {
						if( $role->getID() == $tensionroleid ) 
						{ echo "<option value='".$role->getID()."' selected>".$role->getName()."</option>"; }
						else{ echo "<option value='".$role->getID()."'>".$role->getName()."</option>";}
				}
				echo "</select>";				
				?>
				<strong>Type</strong><input type="radio" name="type" id="gouvernance" value="gouvernance" <?if($tensiontype == "gouvernance") { echo "checked";} ?>>Gouvernance <input type="radio" name="type" value="triage" <?if($tensiontype == "triage") { echo "checked";} ?>>Triage
				<br/><br/><strong>Description</strong><br/>
				<textarea rows="5" cols="80" name="tension" id="tension" class='tinymce'><? echo $tensiondescrpt; ?></textarea>
			</form>
			</div>
		</div>
	<script src="plugins/tinymce/tinymce.min.js"></script>	
	<script src="plugins/tinymce/jquery.tinymce.min.js"></script>	
		
<script>
  $(function() {
  
		$(".omo-light-accordion").accordion({collapsible: true }, {active: "false"} , {heightStyle: "content"});
    
    		// transforme le textarea en éditeur - DDr 4.6.2014
       $('textarea.tinymce').tinymce({
            // Location of TinyMCE script
			menubar : false,
					plugins: "link, paste",
					extended_valid_elements : "p/div/tr/li,br/td",
                    invalid_elements : "span, table, tr, img, button, input, form, ul, li",
					paste_auto_cleanup_on_paste : true,
					paste_remove_styles: true,
		            paste_remove_styles_if_webkit: true,
		            paste_strip_class_attributes: true,			toolbar: "undo redo | bold italic | bullist numlist outdent indent | link",
			statusbar : false

        });

         // Résoud des problèmes de Focus lorsque TinyMCE est utilisé dans des boîtes de dialog - DDr
		$(document).on('focusin', function(e) {
		    if ($(event.target).closest(".mce-window").length) {
				e.stopImmediatePropagation();
			}
		});
		
	$( "#dialogStd" ).dialog({ buttons: [{ text: "Nouvelle tension", click: function() { $( "#tabs-tension" ).tabs( "option", "active", 1 ); } }, {text: "Fermer", click: function() { $( this ).dialog( "close" ); }} ] });

  	/* $(".dialogPage").click(function() {
	  	openDialog ($(this).attr("href"),$(this).attr("alt"));
	  	event.preventDefault();
		event.stopPropagation();
	  });
	  */ //Remove because multiple call (bug) - DDr 1.10.2014
    $( "#tabs-tension" ).tabs({
    activate: function(event ,ui){
                        //console.log(event);
                        if (ui.newTab.index()==0) {
           					$( "#dialogStd" ).dialog({ buttons: [{ text: "Nouvelle tension", click: function() { $( "#tabs-tension" ).tabs( "option", "active", 1 ); } }, {text: "Fermer", click: function() { $( this ).dialog( "close" ); }} ] });
						}                        
						if (ui.newTab.index()==1) {
           					$( "#dialogStd" ).dialog({ buttons: [{ text: "Enregistrer", click: function() { $( "#formulaire").submit(); } }, {text: "Fermer", click: function() { $( this ).dialog( "close" ); }} ] });
						}
						
                    }
});

	<? if (isset($_GET["action"])) { //Si on est en mode edition, on se positionne directement sur Edit	
			if ($_GET["action"]=="edit") { 	
			echo '$( "#tabs-tension" ).tabs( "option", "active", 1 )'; 	
			}} ?>

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
	
  });
  </script>	
