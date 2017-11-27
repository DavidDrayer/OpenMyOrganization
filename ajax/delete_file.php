<?

	// Inclus les �l�ments partag�s entre plusieurs pages: base de donn�e, instanciation de classe, etc...
	include_once("../include.php");
	
	// Formulaire destin� � �tre inclus, qui utilisera le manager du niveau sup�rieur si possible
	if (!isset($manager)) {
		$manager=new \datamanager\sqlManager($dbh);
	}
	
	// Charge les infos sur le r�le
	$doc=$_SESSION["currentManager"]->loadDocuments($_GET["id"]);
	
	$tocken="9SBKukiVIYMAAAAAAABIHqBj9l7Xf8oIPe5jRXR-MA_1rNVtYZCAevCmMvs9qvzL";
    $root="OMO/";
  
  		$circlestr=strtr($doc->getRole()->getSuperCircle()->getName(),' ���������������������������������������������������','_aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY');
		$rolestr=strtr($doc->getRole()->getName(),' ���������������������������������������������������','_aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY');
		$namestr=strtr($doc->getName(),' ���������������������������������������������������','_aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY');

  		// le supprime du r�pertoire priv�
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

  		// le supprime du r�pertoire public
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

  		// le supprime du r�pertoire de l'organisation
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
// Supprime le document dans la base de donn�e
		$_SESSION["currentManager"]->delete($doc);	
?>
