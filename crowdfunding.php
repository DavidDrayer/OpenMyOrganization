<? 
	$root = realpath($_SERVER["DOCUMENT_ROOT"]);
	include_once("$root/include.php");

	// ---------- ONLINE EDITION --------------------
	// Nécessaire si module d'identification
	$timeStart=time();
	// inclus les librairies permettant de gérer l'affichage des zones
	include_once ($_SERVER["DOCUMENT_ROOT"]."/onlineEdit/db.php");
	include_once ($_SERVER["DOCUMENT_ROOT"]."/onlineEdit/onlineEdit.php");

	// et se connecte à la base de données
	$dbh =  connectDb(); 
	$page=2;
	$_SESSION['currentPage']=$page;
	
	// Langue par défaut
	if (!isset($_SESSION["langue"])) {
		$langue=1;
	} else {
		$langue=$_SESSION["langue"];
	}
	
	$isDirectAccess =  !(strpos( $_SERVER['QUERY_STRING'],"page=")=== false);                
	
	// Lit les informations sur la page (titre, gabarit, style)
	$query = "select * from t_page, t_pageinfo, t_gabarit, t_style where t_page.page_id=t_pageinfo.page_id and t_pageinfo.lang_id=".$langue." and t_page.gaba_id=t_gabarit.gaba_id and t_style.styl_id=t_page.styl_id and t_page.page_id=".$page."";
	$result = mysql_query($query, $dbh);
		
	if ($result>0 && mysql_num_rows($result)==0) {
		$query=str_replace("lang_id=".$langue,"lang_id=1",$query);
		$result = mysql_query($query, $dbh);
	}	
		
	// Autre raccourcis???	
	if (isset($_GET["page"]) && $result>0 && mysql_num_rows($result)>0 && mysql_result($result,0,"page_shortcut")!="") {
	 	$badUrl= $_SERVER['REQUEST_URI'] ;
  		if (strpos($badUrl,"?")>0) {
 		$querystring=substr($badUrl,strpos($badUrl,"?")+1);
 		$querystring = preg_replace('/page=[0-9]+/', '', $querystring);
 		if ($querystring!="") $querystring="?".substr($querystring,1);
 		} else $querystring="";

		header("Status: 301 Moved Permanently", false, 301);
		header("Location: http://".$_SERVER['HTTP_HOST']."/".mysql_result($result,0,"page_shortcut").$querystring);
		exit();
	} 
		
	// enregistre les infos de statistiques
	if (isset($_SERVER['HTTP_REFERER']))	
		$referer = $_SERVER['HTTP_REFERER']; 
	else
		$referer="";
	$query = "insert into t_statistique  (page_id, stat_heure, stat_ip, stat_navigateur, stat_reference) values (".$page.",now(),'".$_SERVER["REMOTE_ADDR"]."','".@$_SERVER['HTTP_USER_AGENT']."','".$referer."')";
	$result2=mysql_query($query, $dbh);
	if ($result2<=0) {
		$query=str_replace("and t_pageinfo.lang_id=".$langue,"",$query);
		$result2 = mysql_query($query, $GLOBALS['dbh']);
	}
	
	// La page doit-elle être mise en cache, le fichier existe-t-il et est-il assez récent?
	if ($result>0 && mysql_num_rows($result)>0 ) {
	if (count($_POST)==0 && mysql_result($result,0,"page_cache")>0 && file_exists("tmp/".$page."_".$langue."_".urlencode($_SERVER['QUERY_STRING']).".txt")) {
		if (time()-filemtime ("tmp/".$page."_".$langue."_".urlencode($_SERVER['QUERY_STRING']).".txt")<(mysql_result($result,0,"page_cache")*60)) {
			$fp = fopen("tmp/".$page."_".$langue."_".urlencode($_SERVER['QUERY_STRING']).".txt",'r'); 
			echo fread($fp,9999999);
			exit;
		}
	}  
	$startScript="";
	if (isset($_GET["edit"])) {$startScript='loadEditor();"';}
	if (mysql_result($result,0,"page_active")==1 || isset($_GET["edit"])) {
			$nom =  mysql_result($result,0,"page_nom");
			$parent = mysql_result($result,0,"page_parent");
			$titre =  mysql_result($result,0,"page_titre");
			$description =  mysql_result($result,0,"page_description");
			$motCle =  mysql_result($result,0,"page_motCle");
			$gabarit= mysql_result($result,0,"gaba_url");
			$style=  mysql_result($result,0,"styl_url");
			$raccourci=mysql_result($result,0,"page_shortcut");
	} else {
		include ('404.php');
		exit;	
	}} else {
		include ('404.php');
		exit;	
	}

	// Garde le contenu
	ob_start();	

	// Choisi le bon gabarit
	include $langue.$gabarit;
	//echo "<div align='center' style='font-size:70%; color:#aaaaaa'>Temps de création : ".(time()-$timeStart)." sec</div>";	
?>
	<script language="JavaScript" src="/onlineEdit/edition.js">
		// Insertion du script permettant l'édition on-line des zones de textes
	</script>

</body>
<?

	$txtPage=ob_get_contents();
	ob_end_clean ();
	ob_start();	
?>
<!DOCTYPE html>
<html>
	<head>
		<meta name="google-site-verification" content="<?=$google_verif?>" />
		<meta http-equiv="content-type" content="text/html; charset=iso-8859-1"/>
		<title> <? echo ($titre!=""?$titre." | ":"") ?><?=$site_title ?></title>
		<meta name="description" content="<?=$description ?>">
		<meta name="keywords" content="<?=$motCle ?>">
		<meta name="author" content="<?=$site_author ?>">
		 <meta name="viewport" content="width=device-width">
		  <link rel="stylesheet" href="css/circle.css">
<?
	// N'indexe pas les pages si elles ne sont pas accédées directement
	if ($isDirectAccess && $raccourci!="") {
		echo "<meta name='robots' content='noindex'>";
	}
?>
	<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
	<script src="/plugins/jquery-2.1.0.min.js"></script>
		<script src="/plugins/jquery-ui-1.10.3.custom/js/jquery-ui-1.10.3.custom.min.js"></script>
		<script src="jquery-scrolltofixed.js" type="text/javascript"></script>

		<link href="style/<? echo ($style) ?>" rel="stylesheet" type="text/css">
		<style>
			.noContent {display:none; color:#666666; font-style:italic; border: 2px dashed; padding:5px; margin:5px;min-width:100px; min-height:100px;}
		</style>

	</head>
	<body onload="<?=$startScript?>">
	
		<!-- Dialogue pour payer -->
		<div id="dialogPay" title="Soutenez OMO"  style='display:none'>
			Merci pour votre soutien. Nous donnons le meilleur de nous-même pour mettre à disposition de tous un outil permettant d'offrir une transparence complète au sein de votre organisation.<br/><b>Veuillez choisir le montant de votre contribution:</b><br/>&nbsp;
 		<div style='text-align:center;'>

		
		<form action="https://www.paypal.com/cgi-bin/webscr" id='formPay' method="post" target="popupPay">
		<input type="hidden" name="cmd" value="_donations">
		<input type="hidden" name="business" value="VSVXYMPB9FV6S">
		<input type="hidden" name="lc" value="CH">
		<input type="hidden" id="item_name" name="item_name" value="<?=utf8_encode("Soutien au developpement de OMO")?>">
		<input type="hidden" id="item_number" name="item_number" value="12-23-DavidD">
		<select  name="amount" style='width:60%; text-align:center;'>
		<option value="10.00">10 CHF</option>
		<option value="25.00">25 CHF</option>
		<option value="50.00">50 CHF</option>
		<option value="100.00" selected>100 CHF</option>
		<option value="250.00">250 CHF</option>
		<option value="500.00">500 CHF</option>
		<option value="1000.00">1000 CHF</option>
		</select><br/>
		<input type="hidden" name="currency_code" value="CHF">
		<input type="hidden" name="no_note" value="1">
		<input type="hidden" name="no_shipping" value="1">
		<input type="hidden" name="rm" value="1">
		<input type="hidden" name="return" value="http://www.openmyorganization.com/success.htm?id=23">
		<input type="hidden" name="bn" value="PP-DonationsBF:btn_donateCC_LG.gif:NonHosted">
		<input type="image" src="https://www.paypalobjects.com/fr_FR/CH/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal, le réflexe sécurité pour payer en ligne">
		<img alt="" border="0" src="https://www.paypalobjects.com/fr_FR/i/scr/pixel.gif" width="1" height="1">
		</form>

		
		</div>
 
 		</div>

		<script>
		
		 var menu;
		var myVar;
		 
		// A faire toute à la fin
		 $(document).ready(function(){	
			 $("#formPay").submit(function () {
					w=800; h=640;
				     var y = window.top.outerHeight / 2 + window.top.screenY - ( h / 2)
					var x = window.top.outerWidth / 2 + window.top.screenX - ( w / 2)
					var myWindow = window.open("", "popupPay", 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+w+', height='+h+', top='+y+', left='+x);
					$( "#dialogPay" ).dialog("close");
				 //var myWindow = window.open("", "popupPay", "width=800,height=640");
			 });
			 $(".btn_pay").click(function () {
				 
				var payDialog = $("#dialogPay").dialog({ 
					closeOnEscape: false,  
					modal: true,
					width:400,
					height:300,
					buttons: {"Fermer": function() {
					  payDialog.dialog("close");
					}}
				});
				payDialog.dialog( "open" );
				$("#item_name").val($(this).attr("txt"));
				$("#item_number").val("<?=(isset($_SESSION["currentUser"])?$_SESSION["currentUser"]->getId():"0") ?>-"+$(this).attr("nbr")+"-<?=(isset($_SESSION["currentUser"])?$_SESSION["currentUser"]->getUserName():"unknown") ?>");

			 });
		});
	</script>	
	
<? 
	echo $txtPage;
?>
</html>
<?
	$txtPage=ob_get_contents();
	ob_end_clean();
	if (count($_POST)==0  && mysql_result($result,0,"page_cache")>0) {
		// Ici on peut mettre en cache
		$fp = fopen("tmp/".$page."_".$langue."_".urlencode($_SERVER['QUERY_STRING']).".txt",'w'); 
		fputs($fp,$txtPage);
		fclose($fp);
	} 
	echo $txtPage;
?>
