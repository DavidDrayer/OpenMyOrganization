<html>
<head>
<title>Nouveau fichier</title>
<link href="onglet.css" rel="stylesheet" type="text/css">
</head>

<body bgcolor="#CCCCCC">
		<div class="ongletLegende">Charger un nouveau fichier.</div>
			<div class="ongletContent">
<form enctype="multipart/form-data" action="uploadFile.php" method="post">			<div style="height:406px">

 <input type="hidden" name="MAX_FILE_SIZE" value="1000000" />
<div class="label">S�lectionnez un fichier sur votre ordinateur:</div> <input name="userfile" type="file" style="width:450px"/>
<div class="label">Important:</div><div><ul>
  <li>La taille du fichier doit �tre de maximum 1 m�gabyte.</li>
  <li>Pr�f�rez des formats compatibles sur PC et Mac, comme le format PDF.</li>
  <li>Assurez-vous d'avoir inclus les polices de caract�res avec le document.</li>
  <li>Etes-vous certain d'avoir v�rifi� l'orthographe du document?</li>
</ul>
</div>
</div>
<div align="right"><input type="submit" value="Envoyer" title="Envoyer le fichier sur le site Internet"/> <input type="button" title="Retourner � la page des liens" value="Annuler" onClick="document.location='selectLink.php'"/> <input title="Fermer ce dialogue" type="button" value="Fermer" onClick="window.close()"/></div>
</form>
</div>
</body>
</html>
