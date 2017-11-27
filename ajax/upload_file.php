<?

	// Inclus les éléments partagés entre plusieurs pages: base de donnée, instanciation de classe, etc...
	include_once("../include.php");
	

	// Formulaire destiné à être inclus, qui utilisera le manager du niveau supérieur si possible
	if (!isset($manager)) {
		$manager=new \datamanager\sqlManager($dbh);
	}
	
	// Charge les infos sur le rôle
	$role=$_SESSION["currentManager"]->loadRole($_POST["id"]);
	
	$tocken="9SBKukiVIYMAAAAAAABIHqBj9l7Xf8oIPe5jRXR-MA_1rNVtYZCAevCmMvs9qvzL";
    $root="OMO/";
    
    // Si c'est une nouvelle version d'un fichier, efface toutes les anciennes versions
    if (isset($_POST["id_docu"]) && $_POST["id_docu"]!="") {
		$doc=$_SESSION["currentManager"]->loadDocuments($_POST["id_docu"]);
		
 		$circlestr=strtr($doc->getRole()->getSuperCircle()->getName()," \n àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ",'___aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY');
		$rolestr=strtr($doc->getRole()->getName()," \n àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ",'___aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY');
		$namestr=strtr($doc->getName()," \n àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ",'___aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY');
		
		
		 // le supprime du répertoire principal
		$data_string = '{"path":"/'.$root.$doc->getRole()->getOrganisationId().'/'.$doc->getRoleId().'/'.$namestr.'"}';  

		$ch = curl_init('https://api.dropboxapi.com/2/files/delete');
		$cheaders = array('Authorization: Bearer '.$tocken,
						  'Content-Type: application/json',
						   'Content-Length: ' . strlen($data_string));
		curl_setopt($ch, CURLOPT_HTTPHEADER, $cheaders);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($ch);
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
		 // le supprime de l'arborescence de l'organisation
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

	// Charge le ou les fichiers
	foreach ($_FILES["files"]["name"] as $key => $name) {
	  if (!isset($_FILES["files"]["error"]) || $_FILES["files"]["error"][$key] == UPLOAD_ERR_OK) {
		$name=  utf8_decode($_FILES['files']['name'][$key]);
		$ext = pathinfo($name, PATHINFO_EXTENSION);
		// Ajoute le fichier à la dropbox
		//move_uploaded_file( $_FILES["images"]["tmp_name"][$key], $_SERVER["DOCUMENT_ROOT"]."/modules/agenda/img/".$file);
		$path = $_FILES['files']['tmp_name'][$key];
		$fp = fopen($path, 'rb');
		$size = filesize($path);
		
		// Emplacement
		$circlestr=strtr($role->getSuperCircle()->getName()," \n àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ",'___aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY');
		$rolestr=strtr($role->getName()," \n àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ",'___aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY');
		$namestr=strtr($name," \n àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ",'___aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY');
		
		
		// le déplace dans l'arborescence privée
		$ch = curl_init('https://content.dropboxapi.com/2/files/upload');
		$cheaders = array('Authorization: Bearer '.$tocken,
						  'Content-Type: application/octet-stream',
						  'Dropbox-API-Arg: {"path":"/'.$root.$role->getOrganisation()->getId().'/'.$role->getId().'/'.$namestr.'","autorename":true,"mode":{".tag":"overwrite"}}');
		curl_setopt($ch, CURLOPT_HTTPHEADER, $cheaders);
		curl_setopt($ch, CURLOPT_PUT, true);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($ch, CURLOPT_INFILE, $fp);
		curl_setopt($ch, CURLOPT_INFILESIZE, $size);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($ch);
		//echo ('<script>alert(\'Dropbox-API-Arg: {"path":"/'.$root.$role->getOrganisation()->getShortName().'/'.$circlestr.'/'.$rolestr.'/'.$namestr.'","autorename":true,"mode":{".tag":"overwrite"}}\')</script>');
		// le déplace dans l'arborescence publique (si nécessaire)
		$ch = curl_init('https://content.dropboxapi.com/2/files/upload');
		$cheaders = array('Authorization: Bearer '.$tocken,
						  'Content-Type: application/octet-stream',
						  'Dropbox-API-Arg: {"path":"/'.$root.$role->getOrganisation()->getShortName().'/public/'.$namestr.'","autorename":true,"mode":{".tag":"overwrite"}}');
		curl_setopt($ch, CURLOPT_HTTPHEADER, $cheaders);
		curl_setopt($ch, CURLOPT_PUT, true);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($ch, CURLOPT_INFILE, $fp);
		curl_setopt($ch, CURLOPT_INFILESIZE, $size);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($ch);

		// le déplace dans l'arborescence de l'organisation (si nécessaire)
		$ch = curl_init('https://content.dropboxapi.com/2/files/upload');
		$cheaders = array('Authorization: Bearer '.$tocken,
						  'Content-Type: application/octet-stream',
						  'Dropbox-API-Arg: {"path":"/'.$root.str_replace(" ","_",$role->getOrganisation()->getShortName()).'/'.$circlestr.'/'.$rolestr.'/'.$namestr.'","autorename":true,"mode":"add"}');
		curl_setopt($ch, CURLOPT_HTTPHEADER, $cheaders);
		curl_setopt($ch, CURLOPT_PUT, true);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($ch, CURLOPT_INFILE, $fp);
		curl_setopt($ch, CURLOPT_INFILESIZE, $size);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($ch);

		//echo $response;
		curl_close($ch);
		fclose($fp);

		// Ajoute l'info dans la base de donnée, si déjà chargé
		   if (isset($_POST["id_docu"]) && $_POST["id_docu"]!="") {
				// Ne change rien à la configuration courante, si ce n'est le nom
				$doc->setName($namestr);
				$doc->setFile($role->getId().'/'.$namestr);

		   } else {
				$doc = new \holacracy\document($_SESSION["currentManager"]);
				$doc->setTitle($name);
				$doc->setName($namestr);
				$doc->setFile($role->getId().'/'.$namestr);
				$doc->setUser($_SESSION["currentUser"]->getId());
				$doc->setRole($role->getId());
			}
			$doc->setFile($role->getId().'/'.$namestr);
			$_SESSION["currentManager"]->save($doc);	

		}

	}
 
 
	echo "Fichier chargé avec succès dans le rôle [".utf8_encode($role->getName())."]";
?>
<script>
	if(typeof refreshDoc == 'function') {refreshDoc(<?=$doc->getRoleId()?>)};
    $( "#dialogStd" ).dialog("close");
</script>
