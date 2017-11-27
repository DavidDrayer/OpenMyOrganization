<html>
<head>
<title>Document sans titre</title>
	<base target="contentFrame"/>
	<link href="tree.css" rel="stylesheet" type="text/css">
	<style>
		.treeEntry, .treeEntryMore, .treeEntryLess {
		padding-left:20px;
	}

		.treeEntryMore {
		background-image:  url(../images/icons/treeMore2.GIF);
	}
		.treeEntry {
		background-image:  url(../images/icons/treeEnd2.GIF);
	}
	/* Affichage du "-" pour cacher une section */
	.treeEntryLess {
		background-image:  url(../images/icons/treeLess2.GIF);
	}

	</style>
</head>
<script language="javascript" src="onglet.js"></script>
<script language="javascript" src="lib/tree.js"></script>
<body onLoad="initTree(myThirdTree)">
<div id="myThirdTree" class="treeView" onClick="manageTree(event,false,true)" style="overflow:auto; white-space: nowrap;">

<div class="treeEntry"><a href="fonctionDetail.php#aboutEditor"><b>A propos de l'éditeur Online</b></a>
<div class='treeContent'>
<div class="treeEntry"><a href="fonctionDetail.php#aboutAuthor">Qui a développé l'éditeur</a></div>
<div class="treeEntry"><a href="fonctionDetail.php#howWorksEditor">Comment fonctionne l'éditeur</a></div>
<div class="treeEntry"><a href="fonctionDetail.php#whatNeedEditor">Prérequis technique pour l'éditeur</a></div>
<div class="treeEntry"><a href="fonctionDetail.php#activateEditor">Activer l'éditeur</a></div>
</div></div>

<div class="treeEntry"><b>Fonction sur le texte</b>
<div class='treeContent'>
<div class="treeEntry"><a href="fonctionDetail.php#editText">Editer une zone de texte</a></div>
<div class="treeEntry"><a href="fonctionDetail.php#modifyText">Modifier le texte d'une zone</a></div>
<div class="treeEntry"><a href="fonctionDetail.php#insertBreak">Ins&eacute;rer un saut de paragraphe ou un saut de ligne</a></div>
<div class="treeEntry"><a href="fonctionDetail.php#addBold">Mettre un texte en gras</a></div>
<div class="treeEntry"><a href="fonctionDetail.php#addItalic">Mettre un texte en italique</a></div>
<div class="treeEntry"><a href="fonctionDetail.php#addUnderline">Mettre un texte en soulign&eacute;</a></div>
<div class="treeEntry"><a href="fonctionDetail.php#selectAlign">Modifier l'alignement d'un texte</a></div>

<div class="treeEntry"><a href="fonctionDetail.php#createTitle">Cr&eacute;er un titre ou un sous-titre</a></div>
<div class="treeEntry"><a href="fonctionDetail.php#createSpecial">Cr&eacute;er un bloc de texte mis en &eacute;vidence</a></div>
<div class="treeEntry"><a href="fonctionDetail.php#createBox">Cr&eacute;er un bloc de texte encadr&eacute;</a></div>
<div class="treeEntry"><a href="fonctionDetail.php#createComment">Cr&eacute;er un bloc de texte de commentaire</a></div>
<div class="treeEntry"><a href="fonctionDetail.php#createLink">Cr&eacute;er un lien sur un texte</a></div>
</div></div>

<div class="treeEntry">
<b>Fonctions avec les images</b>
<div class='treeContent'>
<div class="treeEntry"><a href="fonctionDetail.php#insertImage">Ins&eacute;rer une image dans un texte</a></div>
<div class="treeEntry"><a href="fonctionDetail.php#alignImage">Aligner une image diff&eacute;remment</a></div>
<div class="treeEntry"><a href="fonctionDetail.php#addLegend">Ajouter une l&eacute;gende &agrave; une image</a></div>
<div class="treeEntry">Changer la taille d'une image</div>
<div class="treeEntry">Utiliser une image qui n'est pas sur le site Web</div>
<div class="treeEntry">T&eacute;l&eacute;charger une image sur le site Web</div>
<div class="treeEntry">Gérer les liens avec les images
<div class="treeContent">
<div class="treeEntry">Ajouter un lien &agrave; une image</div>
<div class="treeEntry">Ajouter un lien &agrave; un bloc de texte contenant des images</div>
<div class="treeEntry">Supprimer le lien associ&eacute; &agrave; une image</div>
<div class="treeEntry">Cr&eacute;er un lien vers une image plus grande</div>
</div></div>
</div></div>
<div class="treeEntry"><b>Fonctions sur les documents</b>
<div class='treeContent'>
<div class="treeEntry">T&eacute;l&eacute;charger un document sur le site Web</div>
<div class="treeEntry">Cr&eacute;er un lien vers un document</div>
</div></div>
<div class="treeEntry"><b>Fonctions sur les liens</b>
<div class='treeContent'>
<div class="treeEntry">Cr&eacute;er un lien vers une page du site Web</div>
<div class="treeEntry">Cr&eacute;er un lien vers un document</div>
<div class="treeEntry">Cr&eacute;er un lien vers une image</div>
<div class="treeEntry">Cr&eacute;er un lien vers un autre site Web</div>
<div class="treeEntry">Supprimer un lien</div>
<div class="treeEntry">Supprimer tous les liens dans un bloc de texte</div>
</div></div>
<div class="treeEntry"><b>Fonctions sur la page</b>
<div class='treeContent'>
<div class="treeEntry">Modifier les propri&eacute;t&eacute;s d'une page</div>
<div class="treeEntry">Modifier le titre d'une page</div>
<div class="treeEntry">Modifier le nom d'une page</div>
<div class="treeEntry">Modifier la description d'une page</div>
<div class="treeEntry">Modifier les mots-cl&eacute; d'une page</div>
<div class="treeEntry">Modifier le gabarit d'une page</div>
<div class="treeEntry">Modifier la palette de couleurs d'une page</div>
<div class="treeEntry">Rendre une page active ou inactive</div>
</div></div>
<div class="treeEntry"><b>Fonction sur le site</b>
<div class='treeContent'>
<div class="treeEntry">Naviguer vers une page</div>
<div class="treeEntry">Naviguer vers une page inactive</div>
<div class="treeEntry">Ajouter une page au site</div>
<div class="treeEntry">Supprimer une page du site</div>
<div class="treeEntry">Changer l'ordre des pages dans le site</div>
</div></div>
</div>
</body>
</html>
