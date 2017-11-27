<?


/*-- curl -X POST https://content.dropboxapi.com/2/files/upload \
  --header 'Authorization: Bearer fHQ9_c7oMQAAAAAAAAAALzjr7Q62fS8H8lLG_gnpIH9O0pG8UOoqcRkXR1pNoTjK' \
  --header 'Content-Type: application/octet-stream' \
  --header 'Dropbox-API-Arg: {"path":"test","mode":{".tag":"add"},"autorename":true}' \
  --data-binary @'presentation_farinet_pro.pdf'
 */
  


$path = 'test.php';
$fp = fopen($path, 'rb');
$size = filesize($path);

$cheaders = array('Authorization: Bearer fHQ9_c7oMQAAAAAAAAAALzjr7Q62fS8H8lLG_gnpIH9O0pG8UOoqcRkXR1pNoTjK',
                  'Content-Type: application/octet-stream',
                  'Dropbox-API-Arg: {"path":"/test/'.$path.'","autorename":true,"mode":{".tag":"overwrite"}}');

$ch = curl_init('https://content.dropboxapi.com/2/files/upload');
curl_setopt($ch, CURLOPT_HTTPHEADER, $cheaders);
curl_setopt($ch, CURLOPT_PUT, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_INFILE, $fp);
curl_setopt($ch, CURLOPT_INFILESIZE, $size);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);

echo $response;
curl_close($ch);
fclose($fp);



$parameters = array('path' => '/test/test.php');

$headers = array('Authorization: Bearer fHQ9_c7oMQAAAAAAAAAALzjr7Q62fS8H8lLG_gnpIH9O0pG8UOoqcRkXR1pNoTjK',
                 'Content-Type: application/json');

$curlOptions = array(
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($parameters),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_VERBOSE => true
    );

$ch = curl_init('https://api.dropboxapi.com/2/sharing/create_shared_link_with_settings');
curl_setopt_array($ch, $curlOptions);

$response = curl_exec($ch);
echo $response;

curl_close($ch);




?>
