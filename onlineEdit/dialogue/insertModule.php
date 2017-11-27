<html>
<head>
<title>Choisissez une module</title>
<link href="onglet.css" rel="stylesheet" type="text/css">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<script language="javascript" src="palette.js">
</script>
<script language="javascript">
	function selectModule (nom,credit,description) {
		document.getElementById('fileName').value=nom ; 
		bigImg.innerHTML="<h1>"+nom+"</h1>"+ "<div style='text-align:left'>" + description + "</div><div style='text-align:left; font-style:italic; margin-top:10px;'>" + credit+"</div>";
	}
		

</script>
<style>
	body {padding:0px; margin:0px;}
	.bottomButton {text-align:right;  background:#CED7E8;  padding:5px; margin-bottom:2px;}
</style>
<body>
		<div class="ongletLegende">Choisissez un module à insérer</div>
			<div class="ongletContent">
<form method="post">
<?


echo "<table><tr><td width='150'>";

include "lib/moduleChooser.php";
 
 echo "</td><td style='vertical-align:top; text-align:left; padding:10px;' bgcolor='#EEEEEE' width='400'><div id='bigImg'></div></td></table>";

//insertHtml('&lt;IMG class=module src=\'/onlineEdit/module.php?name='+ document.getElementById('fileName').value + '\' unselectable=\'on\'/&gt;'); window.close();	
?>
<div class="bottomButton">

<input type="button" value="Insérer" onclick="if (document.getElementById('fileName').value!='') {document.location='/onlineEdit/editModule.php?name='+ document.getElementById('fileName').value} else {alert ('Veuillez sélectionner le module à insérer.')}"> &nbsp; <input type="button" value="Fermer" onclick="parent.closePopup();"><input type="hidden" id="fileName" name="fileName"/>
	</div></form></div>
</body>
</html>
