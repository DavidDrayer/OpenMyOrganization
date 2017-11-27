<?
	// Suis-je membre du rôle?
	$role=(isset($role)?$role:$this->_role);
	$isRole=(isset($_SESSION["currentUser"]) && $_SESSION["currentUser"]->isRole($role));
	$isCircle=$_SESSION["currentUser"]->isMember($role->getSuperCircle());
	$isMember=$role->getOrganisation()->isMember($_SESSION["currentUser"]);
	$isAdmin=$role->getOrganisation()->isAdmin($_SESSION["currentUser"]);

	// Ne s'affiche que si je suis le rôle (script y compris)
	if ($isRole) {
?>
<script language="javascript">
		  formdata = false;
	  
	  function addImageUploader(input,response,id="",id_docu="") {
		  formdata = new FormData();
		  input.addEventListener("change", function (evt) {
			var i = 0, len = this.files.length, img, reader, file;
			
			response.innerHTML = "<div style='background: url(/img/load.gif); border:1px solid black; border-radius:3px; padding-left:3px; font-weight:bold;'>Chargement . . .</div>"
			
			for ( ; i < len; i++ ) {
			  file = this.files[i];
				  
			if ( window.FileReader ) {
				  reader = new FileReader();
				  reader.readAsDataURL(file);
				}
				if (formdata) {
				  formdata.append("id", id);
				  if (id_docu!="") formdata.append("id_docu", id_docu);
				  formdata.append("files[]", file);
				  // Ajoute les paramètres s'ils existent
				  form=$(input).parent('form')
				  
				}
				
			  
			}
			if (formdata) {
			  $.ajax({
				url: "/ajax/upload_file.php",
				type: "POST",
				data: formdata,
				processData: false,
				contentType: false,
				success: function (res) {
				  
				  // eval les scripts 

				   
				   
				   response.innerHTML = res; 
				   eval ($(response).find("script").text());
				}
			  });
			}
		}, false);
	  }

	
	$(function () {
		

 
	  //var input = document.getElementById("images"),
		  
		
	  if (window.FormData) {
		document.getElementById("btn").style.display = "none";
		addImageUploader(document.getElementById("files"),document.getElementById("response"), <?=$role->getId() ?>)

	  }
	});	
</script>
	<div style='position:absolute; z-index:99; left:6px; right:5px; top:48px; background:#FFFFFF; border-bottom:1px solid #CCCCCC; padding:5px; height:50px;'>

	<form style='background:rgba(255,255,255,0.7); padding:5px; margin:5px;' name="file_form" method="post" enctype="multipart/form-data"  action="/ajax/upload_file.php">
      <input type="file" name="files[]" id="files" multiple=""/> <!-- multiple -->
      <input type="hidden" id="role" name="role" value="1">
      <button type="submit" id="btn">Upload Files!</button>
    <div id="response"></div>
	</form>
	</div>
<?
	}
	echo "<div style='padding-top:50px;'>";
	// Affiche la liste des documents
	foreach($documents as $document) {
		// Le document doit-il être affiché?
		if ($document->getVisibility()==1 || ($document->getVisibility()==2 && $isMember) || ($document->getVisibility()==3 && $isCircle) || ($document->getVisibility()==4 && $isRole)) {
			echo "<div class='role_document' style='background-image:url(/ajax/thumbnail_file.php?id=".$document->getId().")'>";
			// Affiche la visibilité
			echo "<div class='omo-document-visibility".$document->getVisibility()."'></div>";
			
			// Est-ce un URL ou est-ce un fichier?
			echo "<div class='bottom'>";
			if ($document->getURL()!="") {
				echo "<b><a href='".$document->getURL()."' target='_blank'>".$document->getTitle()."</a></b>";
			} else {
				echo "<b><a href='/ajax/download_file.php?id=".$document->getId()."&file=".$document->getName()."'>".$document->getTitle()."</a></b>";
			}
			if ($document->getTitle() != $document->getName()) {
				echo "<div><i>".$document->getName()."</i></div>";
			}
			echo "</div>";
			if ($isRole) echo "<div class='options'><a class='omo-delete ajax' href='ajax/delete_file.php?id=".$document->getId()."' alt='Supprimer' check='Etes-vous s&ucirc;r de vouloir supprimer ce document?'></a><a class='omo-edit dialogPage' href='formulaires/form_document.php?id=".$document->getId()."' alt='Editer les propriétés du document'></a></div>";
			echo "</div>";
		}
	}
	echo "</div>";


?>

