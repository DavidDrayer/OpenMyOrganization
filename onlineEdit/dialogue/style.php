<html>
<head>
<title>Styles</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../../style/<?

	include("../db.php");
	$dbh =  connectDb(); 
	
	//récupère le style de la page en question
	$query = "select t_style.* from t_page, t_style where t_style.styl_id=t_page.styl_id and page_id=".$_GET["page"];
	$result = mysql_query($query, $dbh);
	if ($result>0) {
		echo mysql_result($result,0,"styl_url");
	}
 

?>" rel="stylesheet" type="text/css">
</head>
<script language="javascript" src="palette.js">
</script>
<body><div class="styleChooser">
<div style="padding:6px; border-bottom:1px solid black" onmouseover="this.style.backgroundColor='#CCCCCC'" onmouseout="this.style.backgroundColor=''" onclick="if (checkActive()) {applyStyle('normal')}"><p>Normal</p></div>
<div style="padding:6px; border-bottom:1px solid black" onmouseover="this.style.backgroundColor='#CCCCCC'" onmouseout="this.style.backgroundColor=''" onclick="if (checkActive()) {applyStyle('&lt;H1&gt;')}"><h1 style="margin-top:0px; margin-bottom:0px;">Titre</h1></div>
<div style="padding:6px; border-bottom:1px solid black" onmouseover="this.style.backgroundColor='#CCCCCC'" onmouseout="this.style.backgroundColor=''" onclick="if (checkActive()) {applyStyle('&lt;H2&gt;')}"><h2 style="margin-top:0px; margin-bottom:0px;">Sous-titre</h2></div>
<div style="padding:6px; border-bottom:1px solid black" onmouseover="this.style.backgroundColor='#CCCCCC'" onmouseout="this.style.backgroundColor=''" onclick="if (checkActive()) {applyStyle('&lt;H3&gt;')}"><h3 style="margin-top:0px; margin-bottom:0px;">Encadré</h3></div>
<div style="padding:6px; border-bottom:1px solid black" onmouseover="this.style.backgroundColor='#CCCCCC'" onmouseout="this.style.backgroundColor=''" onclick="if (checkActive()) {applyStyle('&lt;H4&gt;')}"><h4 style="margin-top:0px; margin-bottom:0px;">En évidence</h4></div>
<div style="padding:6px; border-bottom:1px solid black" onmouseover="this.style.backgroundColor='#CCCCCC'" onmouseout="this.style.backgroundColor=''" onclick="if (checkActive()) {applyStyle('&lt;H5&gt;')}"><h5 style="margin-top:0px; margin-bottom:0px;">Remarque</h5></div>
</div>
</body>
</html>
