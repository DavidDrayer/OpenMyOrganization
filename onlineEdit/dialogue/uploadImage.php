<?php

//Parcours toutes les pages et les affiche
include("../db.php");
// et se connecte � la base de donn�es
$dbh =  connectDb(); 

$uploadPath = '../../images/uploads/';
$uploadExt = '.jpg';
$ok=false;

function getLoadedImage ($fileName) {
	if (eregi ("(.+)(\.png)",$fileName['name'], $regs)) {
		return imagecreatefrompng($fileName['tmp_name']);
	}
	if (eregi ("(.+)(\.gif)",$fileName['name'], $regs)) {
		return imagecreatefromgif($fileName['tmp_name']);
	}
	if (eregi ("(.+)(\.jpg)",$fileName['name'], $regs)) {
		return imagecreatefromjpeg($fileName['tmp_name']);
	}
}
// R�cup�re l'extension du fichier source
if (eregi ("(.+)(\.[A-Z,a-z]+)",$_FILES['userfile']['name'], $regs)) {
$uploadExt = $regs[2];
}

//le fichier doit-il �tre renomm� ?
if (isset($_POST["changeName"])) {
	$uploadName =  $_POST['newName'];
} else {
	$uploadName = $_FILES['userfile']['name'];
}

// Supprime l'extension avec des expressions r�guli�res
if (eregi ("(.+)(\.[A-Z,a-z]+)", $uploadName, $regs)) {
	 $uploadName=$regs[1];
}

// Faut-il cr�er une mignature ?
if (isset($_POST["vignette"])) {
	$src =  getLoadedImage($_FILES['userfile']);
	if ($src) {
	$srcX=imagesx($src);
	$srcY=imagesy($src);
	if ($srcX>=$srcY)
		$tailleX=$_POST['tailleVignette'];
	else
		$tailleX=$_POST['tailleVignette']/$srcY*$srcX;
	// Les miniatures sont de toute fa�on des jpeg
	$im=imagecreatetruecolor ($tailleX, $srcY/$srcX*$tailleX);
	imagefill($im, 0,0, ImageColorAllocate( $im, 255, 255, 255 ));
	
	$ok=imagecopyresampled ( $im, $src, 0, 0, 0, 0, $tailleX, $srcY/$srcX*$tailleX, $srcX, $srcY);
	imagejpeg($im,$uploadPath.$uploadName."_mini.jpg",90);
	imagedestroy($im);
	}

}

// Le fichier doit-il �tre redimentionner?
if  (isset($_POST["changeSize"])) {
	$src = getLoadedImage($_FILES['userfile']);
	if ($src) {
	$srcX=imagesx($src);
	$srcY=imagesy($src);
	
	// D�fini la taille X et la taille Y
	
	// Est-ce des valeurs personnalis�es?
	if ($_POST["tailleImage"]=="3") {
		if ($_POST["tailleX"]!="") $tailleX=$_POST["tailleX"];
		if ($_POST["tailleY"]!="") $tailleY=$_POST["tailleY"];
	} else {
		// Sinon, r�cup�re dans la base les donn�es de taille
		
		$query = "select * from t_tailleimage where taim_id=".$_POST["predefini"];
		$result = mysql_query($query, $dbh);
					
		if ($result>0 && mysql_num_rows($result)>0) {
			$tailleX=mysql_result($result,0,"taim_x");
			$tailleY=mysql_result($result,0,"taim_y");
			$max=mysql_result($result,0,"taim_max");
			if (($tailleX=="") && ($tailleY=="") && ($max!="")) $tailleX=$max;
		}
	}
	// Si l'une des deux valeure n'est pas renseign�e, la compl�te
	if (!isset($tailleY) || ($tailleY=="")) {
		$tailleY = $srcY/$srcX*$tailleX;
	}
	if (!isset($tailleX) || ($tailleX=="")) {
		$tailleX = $srcX/$srcY*$tailleY;
	}
	// S'assure que l'on ne d�passe pas le maximum
	if (isset($max) && ($max!="")) {
		if ($tailleX>$max) {
			$tailleY=$tailleY*$max/$tailleX;
			$tailleX=$max;
		}
		if ($tailleY>$max) {
			$tailleX=$tailleX*$max/$tailleY;
			$tailleY=$max;
		}
	}
	if (eregi ("(\.gif)",$uploadExt, $regs) || eregi ("(\.png)",$uploadExt, $regs)) {
		$colorTransparent = imagecolortransparent($src);
		$im=imagecreate ($tailleX, $tailleY);
		imagepalettecopy($im,$src);
		imagefill($im,0,0,$colorTransparent);
		imagecolortransparent($im, $colorTransparent);
		$ok=imagecopyresampled ( $im, $src, 0, 0, 0, 0, $tailleX, $tailleY, $srcX, $srcY);
		imagepng ($im,$uploadPath.$uploadName.'.png');
	} else {
		$im=imagecreatetruecolor ($tailleX, $tailleY);
		$ok=imagecopyresampled ( $im, $src, 0, 0, 0, 0, $tailleX, $tailleY, $srcX, $srcY);
		imagejpeg($im,$uploadPath.$uploadName.$uploadExt,90);
	}
	imagedestroy($im);
	}
	
} else {
	$ok=move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadPath.$uploadName.$uploadExt);
}

if ($ok) {
			header("Location: selectImage.php"); 
} else {

?>

<html>
<head>
<title>Probl�me de chargement d'image</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<link href="onglet.css" rel="stylesheet" type="text/css">
<body>
<div class="ongletLegende">Le chargement de l'image a pos� un probl�me.</div>
			<div class="ongletContent">
<div style="height:406px">
L'image n'a pas pu �tre t�l�charg�e. Veuillez vous assurez que le format d'image est valide et que vous avez rempli correctement l'ensemble des informations.
</div>
<div align="right"><input type="button" title="Retourner a chargement des images" value="R�essayer" onClick="document.location='newImage.php'"/> <input type="button" title="Retourner � la liste des images" value="Annuler" onClick="document.location='selectImage.php'"/> <input title="Fermer ce dialogue" type="button" value="Fermer" onClick="window.close()"/></div>
			</div>
			
</body>
</html>

<?
}
?> 
