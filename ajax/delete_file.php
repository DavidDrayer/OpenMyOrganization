<?

	// Inclus les éléments partagés entre plusieurs pages: base de donnée, instanciation de classe, etc...
	include_once("../include.php");
	
	// Formulaire destiné à être inclus, qui utilisera le manager du niveau supérieur si possible
	if (!isset($manager)) {
		$manager=new \datamanager\sqlManager($dbh);
	}
	
	// Charge les infos sur le rôle
	$doc=$_SESSION["currentManager"]->loadDocuments($_GET["id"]);
	
	$tocken="9SBKukiVIYMAAAAAAABIHqBj9l7Xf8oIPe5jRXR-MA_1rNVtYZCAevCmMvs9qvzL";
    $root="OMO/";
  
  		$circlestr=strtr($doc->getRole()->getSuperCircle()->getName(),' àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ','_aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY');
		$rolestr=strtr($doc->getRole()->getName(),' àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ','_aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY');
		$namestr=strtr($doc->getName(),' àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ','_aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY');

  		// le supprime du répertoire privé
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

  		// le supprime du répertoire de l'organisation
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

		//echo $response;
		curl_close($ch);

		

		//refresh la liste

?>

	if(typeof refreshDoc == 'function') {refreshDoc(<?=$doc->getRoleId()?>)};
    $( "#dialogStd" ).dialog("close");

<?
// Supprime le document dans la base de donnée
		$_SESSION["currentManager"]->delete($doc);	
?>
