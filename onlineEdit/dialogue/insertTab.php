<html>
<head>
<title>Choisissez une module</title>
<link href="onglet.css" rel="stylesheet" type="text/css">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<script language="javascript" src="palette.js">
</script>
<script language="javascript">
	function valider () {
		parent.replaceSelection('<table class="tableau1" width="70%" align="center"><caption>Monthly savings</caption><tr><th></th><th></th></tr><tr><td>[-selection-]</td><td></td></tr><tr><td></td><td></td></tr></table>');
		parent.closePopup();
	}
		

</script>
<style>
	body {padding:0px; margin:0px;}
	.bottomButton {text-align:right;  background:#CED7E8;  padding:5px; margin-bottom:2px;}
</style>
<body>
		<div class="ongletLegende">Propriétés du tableau</div>
			<div class="ongletContent">
<form method="post">


<div class="bottomButton">

<input type="button" value="Insérer" onclick="valider()"> &nbsp; <input type="button" value="Fermer" onclick="parent.closePopup();"><input type="hidden" id="fileName" name="fileName"/>
	</div></form></div>
</body>
</html>
