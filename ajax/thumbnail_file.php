<?
function dbx_get_file($token, $in_filepath, $out_filepath)
    {
    $out_fp = fopen($out_filepath, 'w+');
    if ($out_fp === FALSE)
        {
        echo "fopen error; can't open $out_filepath\n";
        return (NULL);
        }



    $url = 'https://content.dropboxapi.com/2/files/get_thumbnail';

    $header_array = array(
        'Authorization: Bearer ' . $token,
        'Content-Type:',
        'format:{".tag":"jpeg"}',
        'size:{".tag":"w64h64"}',
        'Dropbox-API-Arg: {"path":"' . $in_filepath . '"}'
    );

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header_array);
    curl_setopt($ch, CURLOPT_FILE, $out_fp);

    $metadata = null;
    curl_setopt($ch, CURLOPT_HEADERFUNCTION, function ($ch, $header) use (&$metadata)
        {
        $prefix = 'dropbox-api-result:';
        if (strtolower(substr($header, 0, strlen($prefix))) === $prefix)
            {
            $metadata = json_decode(substr($header, strlen($prefix)), true);
            }
        return strlen($header);
        }
    );

    $output = curl_exec($ch);

    if ($output === FALSE)
        {
        echo "curl error: " . curl_error($ch);
        }

    curl_close($ch);
    
	rewind($out_fp);
	echo stream_get_contents($out_fp);  
    
    fclose($out_fp);

    return($metadata);
    } // dbx_get_file()
 
 	// Inclus les éléments partagés entre plusieurs pages: base de donnée, instanciation de classe, etc...
	include_once("../include.php");
	
	// Formulaire destiné à être inclus, qui utilisera le manager du niveau supérieur si possible
	if (!isset($manager)) {
		$manager=new \datamanager\sqlManager($dbh);
	}
	
	// Charge les infos sur le rôle
	$doc=$_SESSION["currentManager"]->loadDocuments($_GET["id"]);
    
    header('Content-Type:  image/jpeg');
    //header('Content-Disposition: attachment; filename="'.$doc->getName().'"');
 
 	$tocken="9SBKukiVIYMAAAAAAABIHqBj9l7Xf8oIPe5jRXR-MA_1rNVtYZCAevCmMvs9qvzL";
    $root="OMO/";
     
	$metadata = dbx_get_file($tocken, '/'.$root.$doc->getRole()->getOrganisation()->getId().'/'.$doc->getFile().'', 'php://temp');
?>
