<?php

require_once "lib/dropbox-php-sdk-1.1.2/lib/Dropbox/autoload.php";

use \Dropbox as dbx;

$dropbox_config = array(
    'key'    => 'your_key',
    'secret' => 'your_secret'
);

$appInfo = dbx\AppInfo::loadFromJson($dropbox_config);
$webAuth = new dbx\WebAuthNoRedirect($appInfo, "PHP-Example/1.0");

$authorizeUrl = $webAuth->start();
echo "1. Go to: " . $authorizeUrl . "<br>";
echo "2. Click \"Allow\" (you might have to log in first).<br>";
echo "3. Copy the authorization code and insert it into $authCode.<br>";

$authCode = trim('DjsR-iGv4PAAAAAAAAAAAbn9snrWyk9Sqrr2vsdAOm0');

list($accessToken, $dropboxUserId) = $webAuth->finish($authCode);
echo "Access Token: " . $accessToken . "<br>";

$dbxClient = new dbx\Client($accessToken, "PHP-Example/1.0");

// Uploading the file
$f = fopen("working-draft.txt", "rb");
$result = $dbxClient->uploadFile("/working-draft.txt", dbx\WriteMode::add(), $f);
fclose($f);
print_r($result);

// Get file info
$file = $dbxClient->getMetadata('/working-draft.txt');

// sending the direct link:
$dropboxPath = $file['path'];
$pathError = dbx\Path::findError($dropboxPath);
if ($pathError !== null) {
    fwrite(STDERR, "Invalid <dropbox-path>: $pathError\n");
    die;
}

// The $link is an array!
$link = $dbxClient->createTemporaryDirectLink($dropboxPath);
// adding ?dl=1 to the link will force the file to be downloaded by the client.
$dw_link = $link[0]."?dl=1";

echo "Download link: ".$dw_link."<br>";

?>
