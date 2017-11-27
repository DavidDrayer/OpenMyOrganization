<?
	// Inclus les éléments partagés entre plusieurs pages: base de donnée, instanciation de classe, etc...
	include_once("../include.php");
	
	if (isset($_GET["id"])) {
		$doc=$_SESSION["currentManager"]->loadDocuments($_GET["id"]);
	} else {
		// Que faire i pas d'ID?
	}
	// Formulaire posté?
	if (isset($_POST["docu_title"])) {
		
		// Contrôle les erreurs
		if (!isset($_POST["docu_visibility"]) || $_POST["docu_visibility"]=="") {
			echo "/* Erreur */\n alert('Erreur!! Il n\'y a pas de visibilité définie.');$('#visibility1').focus();"; exit;
		} 
			
		
		// Sauve les infos
		$doc=$_SESSION["currentManager"]->loadDocuments($_POST["docu_id"]);
		$doc->setTitle(utf8_decode($_POST["docu_title"]));
		$doc->setDescription(utf8_decode($_POST["docu_description"]));
		$doc->setVisibility(utf8_decode($_POST["docu_visibility"]));
		$_SESSION["currentManager"]->save($doc);
	
	$tocken="9SBKukiVIYMAAAAAAABIHqBj9l7Xf8oIPe5jRXR-MA_1rNVtYZCAevCmMvs9qvzL";
    $root="OMO/";

 		$circlestr=strtr($doc->getRole()->getSuperCircle()->getName(),' àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ','_aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY');
		$rolestr=strtr($doc->getRole()->getName(),' àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ','_aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY');
		$namestr=strtr($doc->getName(),' àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ','_aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY');
		
		// Déplace une copie du fichier dans les répertoires correspondants
		if ($doc->getVisibility()==1) {
			$data_string = '{"from_path":"/'.$root.$doc->getRole()->getOrganisationId().'/'.$doc->getFile().'","to_path":"/'.$root.$doc->getRole()->getOrganisation()->getShortName().'/public/'.$namestr.'","allow_shared_folder":true,"autorename":false}';  

			$ch = curl_init('https://api.dropboxapi.com/2/files/copy');
			$cheaders = array('Authorization: Bearer '.$tocken,
							  'Content-Type: application/json',
							   'Content-Length: ' . strlen($data_string));
			curl_setopt($ch, CURLOPT_HTTPHEADER, $cheaders);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string); 
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$response = curl_exec($ch);
		} else {
			 // le supprime du répertoire public
			$data_string = '{"path":"/'.$root.$doc->getRole()->getOrganisation()->getShortName().'/public/'.$namestr.'"}';  

			$ch = curl_init('https://api.dropboxapi.com/2/files/delete');
			$cheaders = array('Authorization: Bearer '.$tocken,
							  'Content-Type: application/json',
							   'Content-Length: ' . strlen($data_string));
			curl_setopt($ch, CURLOPT_HTTPHEADER, $cheaders);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string); 
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$response = curl_exec($ch);
		}
		
		if ($doc->getVisibility()<3) {
			$data_string = '{"from_path":"/'.$root.$doc->getRole()->getOrganisationId().'/'.$doc->getFile().'","to_path":"/'.$root.$doc->getRole()->getOrganisation()->getShortName().'/'.$circlestr.'/'.$rolestr.'/'.$namestr.'","allow_shared_folder":true,"autorename":false}';  

			$ch = curl_init('https://api.dropboxapi.com/2/files/copy');
			$cheaders = array('Authorization: Bearer '.$tocken,
							  'Content-Type: application/json',
							   'Content-Length: ' . strlen($data_string));
			curl_setopt($ch, CURLOPT_HTTPHEADER, $cheaders);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string); 
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$response = curl_exec($ch);
		} else {
			 // le supprime du répertoire public
			$data_string = '{"path":"/'.$root.$doc->getRole()->getOrganisation()->getShortName().'/'.$circlestr.'/'.$rolestr.'/'.$namestr.'"}';  

			$ch = curl_init('https://api.dropboxapi.com/2/files/delete');
			$cheaders = array('Authorization: Bearer '.$tocken,
							  'Content-Type: application/json',
							   'Content-Length: ' . strlen($data_string));
			curl_setopt($ch, CURLOPT_HTTPHEADER, $cheaders);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string); 
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$response = curl_exec($ch);
		}
		

		
		echo "Sauvegarde effectuée";
		// Modifie les boutons
?>
<script>
	if(typeof refreshDoc == 'function') {refreshDoc(<?=$doc->getRoleId()?>)};
    $( "#dialogStd" ).dialog("close");
</script>
<?
		exit;
	}
	

	
	echo "<form id='formulaire'>";
		echo "<input type='hidden' id='form_target' value='/formulaires/form_document.php'/>";
		echo "<input type='hidden' name='docu_id' value='".$doc->getId()."'/>";

		echo "<fieldset><legend><div id='mask1'></div><span>Informations sur le document</span><div id='mask2'></div></legend>";

			echo "<div><span class='omo-label-fields'>Titre:</span><span class='omo-field'>";
			echo "<input id='docu_title' name='docu_title' type='text' value='".str_replace("'","&#39;",$doc->getTitle())."'>";
			echo "</span></div>";
			echo "<div><span class='omo-label-fields'>Description:</span><span class='omo-field'>";
			echo "<input type='text' id='docu_description' name='docu_description' value='".$doc->getDescription()."'>";
			echo "</span></div>";
			echo "<div><span class='omo-label-fields'>Visibilité:</span><span class='omo-field'>";

?>
      <input type="radio" name="docu_visibility" id="visibility1" value="1" <? echo ($doc->getVisibility()==1?"checked":"");?>>
      <label for="visibility1">Public</label>
      <input type="radio" name="docu_visibility" id="visibility2" value="2" <? echo ($doc->getVisibility()==2?"checked":"");?>>
      <label for="visibility2">Organisation</label>
      <input type="radio" name="docu_visibility" id="visibility3" value="3" <? echo ($doc->getVisibility()==3?"checked":"");?>>
      <label for="visibility3">Cercle</label>
      <input type="radio" name="docu_visibility" id="visibility4" value="4" <? echo ($doc->getVisibility()==4?"checked":"");?>>
      <label for="visibility4">Role</label>
 <?
			echo "</span></div>";

		echo "</fieldset>";
		
		
	echo "</form>";
	
	echo "<fieldset><legend><div id='mask1'></div><span>Mise à jour du document</span><div id='mask2'></div></legend>";

	echo "<form style='background:rgba(255,255,255,0.7); padding:5px; margin:5px;' name='file_form' method='post' enctype='multipart/form-data'  action='/ajax/upload_file.php'>";
    echo '  <input type="file" name="files[]" id="files2" multiple=""/>'; 
    echo '  <input type="hidden" id="role" name="role" value="1">';
    echo '  <input type="hidden" id="id" name="id" value="'.$doc->getId().'">';
    echo '  <button type="submit" id="btn2">Upload Files!</button>';
    echo '  <div id="response2"></div>';
	echo "</form>";

	echo "</fieldset>";	
?>
<script language="javascript">    
 	 
 	  if (window.FormData) {
		document.getElementById("btn2").style.display = "none";
		addImageUploader(document.getElementById("files2"),document.getElementById("response2"), <?=$doc->getRole()->getId()?>, <?=$doc->getId()?>)

	  }
 
	  $("#formulaire").submit(function() {
	// Envoie le formulaire en AJAX (méthode POCapture du 2017-02-28 21-32-42.pngST), la destination étant définie par l'élément à l'ID form_target 
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

    $( "#dialogStd" ).dialog({ buttons: [{ text: "Enregistrer", click: function() { $( "#formulaire").submit(); } }, {text: "Annuler", click: function() { $( this ).dialog( "close" ); }} ] });

</script>
