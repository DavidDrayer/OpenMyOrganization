<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>jQuery.getJSON demo</title>
  <style>
  img {
    height: 100px;
    float: left;
  }
  </style>
  <script src="https://code.jquery.com/jquery-1.10.2.js"></script>
</head>
<body>
<?

$str = file_get_contents('https://monitoringapi.solaredge.com/site/575258/overview?api_key=RK41HEPMT1LO3RX7SRP8OT50OGXQS3E4');

$json = json_decode($str, true); // decode the JSON into an associative array


$totalenergy = $json['overview']['lifeTimeData']['energy'];
$totalrevenue = $json['overview']['lifeTimeData']['revenue'];

echo $totalenergy." - ".$totalrevenue;

?>
</body>
</html>
