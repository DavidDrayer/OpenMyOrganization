<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<?php
	// Inclus les �l�ments partag�s entre plusieurs pages: base de donn�e, instanciation de classe, etc...
	include_once("include.php");
?>
<html>
<head>
	<!-- Chargement des scripts et styles pour jquery et jquery-ui -->
		<script src="/plugins/jquery-2.1.0.min.js"></script>
		<script src="/plugins/jquery-ui-1.10.3.custom/js/jquery-ui-1.10.3.custom.min.js"></script>
	
	<!-- Chargement des styles propre au site -->
<link rel="stylesheet" href="style/templates/<?=$_SESSION["template"]?>/omo.css" />
	
	<!-- Info sur la page -->
	<title>O.M.O &gt; Utilisateur </title></head>
<body>
<?
	// Instantiation du gestionnaire de base de donn�e
	$manager=new \datamanager\SqlManager($dbh);
	// Chargement de l'�l�ment cercle s�lectionn�
	$user=$manager->loadUser($_GET["id"]);
	// Instantiation d'un manager graphic adapt� au support (browser, PDA, etc...)
	$graphicManager=new \displaymanager\GraphicManager((isset($_GET["display"])?$_GET["display"]:1));
	// Chargement d'un browser pour un cercle
	$mainDisplay=$graphicManager->getBrowser($user);
	// Affichage d browser
	$mainDisplay->display();
		
?>
</body>
