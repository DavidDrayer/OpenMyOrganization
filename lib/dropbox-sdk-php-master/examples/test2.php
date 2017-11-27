<?
function dbx_get_file($token, $in_filepath, $out_filepath)
    {
    $out_fp = fopen($out_filepath, 'w+');
    if ($out_fp === FALSE)
        {
        echo "fopen error; can't open $out_filepath\n";
        return (NULL);
        }

    $url = 'https://content.dropboxapi.com/2/files/download';

    $header_array = array(
        'Authorization: Bearer ' . $token,
        'Content-Type:',
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
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="downloaded.pdf"');



	$metadata = dbx_get_file("fHQ9_c7oMQAAAAAAAAAALzjr7Q62fS8H8lLG_gnpIH9O0pG8UOoqcRkXR1pNoTjK", '/test/test.php', 'php://temp');
?>
