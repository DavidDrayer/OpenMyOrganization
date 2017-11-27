<?php
	namespace widget;
	
// Cette classe affiche un browser HTML permettant de parcourir un ou plusieurs objet de type "rorganisation" dans son
// intégralité : redevabilites, perimetres, raison d'etre, etc...
class wg_Login extends Widget {

public function display() {
	?>
	<html>
<head>
	<meta name="viewport" content="width=device-width"/>
	<!-- Chargement des scripts et styles pour jquery et jquery-ui -->
		<script src="/plugins/jquery-2.1.0.min.js"></script>
		<script src="/plugins/jquery-ui-1.10.3.custom/js/jquery-ui-1.10.3.custom.min.js"></script>

	   <!-- pour éditer l'agenda ??? -->
	   <script type="text/javascript" src="/plugins/timepicker/jquery.ui.timepicker.js?v=0.3.3"></script>

	<script src="/scripts/organisation.js"></script>
	<script src="/scripts/shortcuts.js"></script>
	
		<link href="/plugins/select2/select2.min.css" rel="stylesheet" />
		<script src="/plugins/select2/select2.min.js"></script>

		<!-- Smartsupp Live Chat script -->
		<script type="text/javascript">
		var _smartsupp = _smartsupp || {};
		_smartsupp.key = '0494c3f917ace7e29405137cc471f391bbd3416a';
		window.smartsupp||(function(d) {
			var s,c,o=smartsupp=function(){ o._.push(arguments)};o._=[];
			s=d.getElementsByTagName('script')[0];c=d.createElement('script');
			c.type='text/javascript';c.charset='utf-8';c.async=true;
			c.src='//www.smartsuppchat.com/loader.js?';s.parentNode.insertBefore(c,s);
		})(document);
		</script>
	
	<!-- Chargement des styles propre au site -->
	<link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Ubuntu" />
	<link rel="stylesheet" href="style/templates/<?=$_SESSION["template"]?>/circle.css" />

	<link rel="stylesheet" href="/plugins/timepicker/jquery.ui.timepicker.css?v=0.3.3" type="text/css" />
	<script src="/scripts/login.js"></script>
	
	
	<!-- Info sur la page -->
	<title>O.M.O | Login</title>
	<style>
	/* Login */
	table.accueil{text-align:center; margin : 0px auto auto auto;  width:570px; }
	input#user_login, input#user_password{padding:2px; width:120px; font-size:12px; display: inline-block;}
	button#btn_login {padding:2px; width:140px; font-size:11px; display: inline-block;}
	#password_refresh {display:block; margin-top:10px; text-align:center;}
	.copyright {padding: 0px 5px; display: inline-block;color:#ffffff; font-size:smaller; text-align:center; background:rgba(0,0,0,0.5)}
	.copyright a {color:#FFFF00;}
	.facebook {margin:10px;background:url(/style/templates/common/images/logo_facebook.png); width:52px; height:52px; }
	.youtube {margin:10px;background:url(/style/templates/common/images/logo_youtube.png); width:52px; height:52px; }
	.bigstat {font-size:60px;color:#444444;}
	@media all and (min-width: 600px)  {
		.first_cell {padding-right:20px; border-right:1px solid black; margin-right:20px;}
	}
	@media all and (max-width: 600px)  {
		table.accueil{text-align:center; margin : 0px auto auto auto; padding-top:0px; width:300px; }
		input#user_login, input#user_password{padding:5px; width:290px; font-size:16px; display: block;}
		#img_spit {display:none}
		td.second_cell, .copyright {display:none}
		input[type='checkbox'] {   padding-left:5px;
			padding-right:5px;
			border: double 2px #00F;
			width:50px;
			height:50px;
		}
		
	}
	.omo_presentation {
		max-width:800px; padding:20px;
		margin:auto;
	}
	.tab_stat td {border-top:5px solid #2980cf;}
	.stat_add { opacity:0.5;}
	td:hover div.stat_add {opacity:1;}
	input {padding:10px;}

</style>
    <!--[if lte IE 8]><script type="text/javascript" src="plugins/DD_roundies_0.0.2a.js"></script><script>DD_roundies.addRule('.cadre', '20px');
</script><![endif]-->



	</head>
	<body class="login_page" style='overflow:inherit'><?
	
	
			// Est-ce que le code est spécifié pour un nouvel utilisateur
			if (isset($_GET["code"])) {
				// Cherche si l'utilisateur existe
				$filter=new \holacracy\Filter();
				$filter->addCriteria("code",$_GET["code"]);
				$users= $_SESSION["currentManager"]->findUsers($filter);
				if (count($users)>0) {
				
					// S'il existe, est-il désactivé?
					if ($users[0]->isActive()==false) {
						// Si oui, affiche le dialogue pour compléter les infos
						echo "<table style='width:100%; height:100%'><tr><td style='vertical-align:middle'>'";
						echo "<table class='accueil grey_design'><tr><td style='height:150px;'><img id='img_spit' src='/images/logo-mascotte.png' style='position:absolute; padding-left:350px; '><img src='/images/titre_omo.png' style='padding-top:105px;'></td></tr><tr>";
						echo "<td style='text-align:center'>";
						echo "<div style='padding:5px; font-weight:bold;' class='ui-state-default ui-state-active ui-corner-top '>";
						echo "Veuillez compléter votre profil";
						echo "</div>";
						echo "<div style='padding:15px; margin-bottom:6px;' class=' ui-helper-reset ui-widget-content ui-corner-bottom '>";
						echo "<div class='loginconnect'><form id='form_login'></form></div>";
						
						echo "<table><tr><td>Username: </td><td><input name='user_id' id='user_id' type='hidden' value='".$users[0]->getId()."'><input name='user_username' id='user_username' type='text' style='width:100%'></td></tr><tr><td>Nom:</td><td><input name='user_lastname' id='user_lastname' type='text'  style='width:100%'></td></tr><tr><td>Prénom:</td><td><input name='user_firstname' id='user_firstname' type='text'  style='width:100%'></td></tr><tr><td>Mot de passe:</td><td><input name='user_password2' id='user_password2' type='password' style='width:100%'></td></tr><tr><td>Vérification:</td><td><input name='user_verif' id='user_verif' type='password'  style='width:100%'></td></tr><tr><td colspan=2>&nbsp;<br/><button id='btn_validate' style='width:100%'>Finaliser l'inscription</button></td></tr></table>";
						
						echo "</div>";
						echo "<div class='copyright ui-corner-all'>Coyright (c) 2014-2017 - Ennoïa, <a href='http://www.ateliersinstantz.net'>les Ateliers de l'Instant Z</a> | Contactez le <a href='mailto:webmaster@openmyorganization.com'>Webmaster</a></div> ";
						echo "</td></tr></table></td></tr></table>";
						
						echo "</body></html>";
						exit;
					}
				}
				// Si non, continue comme si de rien n'était
			}
	
	
	
			//affiches le logo et la box pour se logger

			echo "<div id='organisationBrowser' class='main' style='display:relative; height:calc(100% - 50px); min-height:494px;'><table style='width:100%; height:100%'><tr><td style='vertical-align:middle'>'";
			echo "<table class='accueil grey_design'><tr><td style='height:150px;'><img id='img_spit' src='/images/logo-mascotte.png' style='position:absolute; padding-left:350px; '><img src='/images/titre_omo.png' style='padding-top:105px;'></td></tr><tr>";
			echo "<td style='text-align:center'>";
			echo "<div style='padding:5px; font-weight:bold;' class='ui-state-default ui-state-active ui-corner-top '>";
			echo "Veuillez vous connecter pour accéder au site";
			echo "</div>";
			echo "<div style='padding:15px; margin-bottom:6px;' class=' ui-helper-reset ui-widget-content ui-corner-bottom '>";
			echo "<div class='loginconnect'><form id='form_login'><table border=0 cellspacing=0 cellpadding=0><tr><td class='first_cell'><table><tr><td>User: </td><td><input name='user_login' id='user_login' type='text'></td></tr><tr><td>Password:</td><td><input name='user_password' id='user_password' type='password'></td></tr><tr><td colspan=2>&nbsp;<br/><button id='btn_login'  style='width:100%'>Se Connecter</button></td></tr></table>";
			echo "<input type='checkbox' id='remember_me' name='remember_me' value='remember_me''> Se souvenir de moi<span id='password_refresh'><a href='#' id='openFormPassword'>Mot de passe oublié?</a></span>";
			echo "</td><td class='second_cell' style='padding-left:20px;'>OpenMyOrganization est un logiciel web permettant de centraliser et de partager de façon transparente avec ses membres toutes les informations concernant le fonctionnement interne de l'organisation.";
			echo "<br/>&nbsp;<br/><a id='btn_visit' href='organisation.php' style='width:100%'>Explorer les organisations publiques</a>";
			echo "<br>&nbsp;<br><a id='btn_try' href='http://demo.openmyorganization.com' style='width:100%'>Tester la version démo</a>";
			echo "</td></tr>";
			echo "</table></form></div>";
			echo "</div>";
			echo "<div class='copyright ui-corner-all'>Coyright (c) 2014-2017 - Ennoïa, <a href='http://www.ateliersinstantz.net'>les Ateliers de l'Instant Z</a> | Contactez le <a href='mailto:webmaster@openmyorganization.com'>Webmaster</a></div> ";
			echo "<div><a class='facebook' href='https://www.facebook.com/openmyorganization/'></a> <a class='youtube' href='https://www.youtube.com/channel/UCXD3iHVohRc5p5MeD8gLlSg'></a></div>";
			echo "</td></tr></table></td></tr></table></div>";
			echo "<div style=' width:100%; background:#FFFFFF;  left:0px; right:0px;'>";
			
			echo "<table class='tab_stat' cellspacing=10 style='max-width:1024px;' align='center'>";
			echo "<tr><th colspan=5 style='text-align:center; font-size:25px; background:#DDDDDD'>";
			echo "O.M.O en quelques chiffres:";
			echo "</th></tr>";
			echo"<tr><td width='20%'  style='text-align:center'>";
			
			$query="SELECT distinct(t_organisation.orga_id) , orga_name, orga_website, max(DATEDIFF(t_history.hist_date,NOW())) as lastActivity from t_organisation left join t_role on (t_organisation.orga_id=t_role.orga_id) left join t_history on (t_role.role_id=t_history.role_id_circle) where DATEDIFF(t_history.hist_date,NOW())>-365 group by t_organisation.orga_id order by lastActivity DESC ";
			$result=mysql_query($query,$GLOBALS["dbh"]);
			if ($result>0) {
				echo "Nombre d'organisations";
				echo "<div class='bigstat'>".mysql_num_rows($result)."</div>";
			}
			echo "<div class='stat_add'>Dont ";
			for ($i=0; $i<5; $i++) {
				if (mysql_result($result,$i,"orga_website")!="") {
					echo "<a target='_new' href='".(strpos(mysql_result($result,$i,"orga_website"),"://")>0?"":"http://").mysql_result($result,$i,"orga_website")."'>".mysql_result($result,$i,"orga_name")."</a>";
				} else {
					echo mysql_result($result,$i,"orga_name");
				}
				echo ", ";
			}
			echo "...</div>";
			echo "</td><td width='20%'  style='text-align:center'>";
			
			$query="SELECT *, DATEDIFF(user_lastConnexionDate, NOW()) as param FROM `t_user` HAVING param>-365 ORDER BY param DESC   ";
			$result=mysql_query($query,$GLOBALS["dbh"]);
			if ($result>0) {
				echo "Nombre d'utilisateurs";
				echo "<div class='bigstat'>".mysql_num_rows($result)."</div>";
			}

			$query="select * from t_role where t_role.user_id in (SELECT user_id FROM `t_user` WHERE DATEDIFF(t_user.user_lastConnexionDate, NOW())>-365) ";
			$result=mysql_query($query,$GLOBALS["dbh"]);
			if ($result>0) {
				echo "";
				echo "<div class='stat_add'>se répartissant ".mysql_num_rows($result)." rôles</div>";
			}

			echo "</td><td width='20%' style='text-align:center'>";
			
			$query="select proj_id from t_project  ";
			$result=mysql_query($query,$GLOBALS["dbh"]);
			if ($result>0) {
				echo "Nombre de projets";
				echo "<div class='bigstat'>".mysql_num_rows($result)."</div>";
			}
			$query="SELECT proj_id, DATEDIFF(proj_dateCreation, NOW()) as CD FROM `t_project` HAVING CD>-365 ";
			$result=mysql_query($query,$GLOBALS["dbh"]);
			if ($result>0) {
				echo "<div class='stat_add'>Dont ";
				echo "".mysql_num_rows($result)." cette année";
			}
			$query="SELECT proj_id, prst_id, DATEDIFF(proj_dateCreation, NOW()) as CD FROM `t_project` HAVING CD>-365 and prst_id=1 ";
			$result=mysql_query($query,$GLOBALS["dbh"]);
			if ($result>0) {
				echo "et ";
				echo "".mysql_num_rows($result)." en cours</div>";
			}
		
			echo "</td><td width='20%'  style='text-align:center'>";
			
			$query="SELECT TIMEDIFF(meet_closing, meet_opening) as duree, DATEDIFF(meet_closing, meet_opening) as dureejours FROM `t_meeting` HAVING duree>'00:15:00' and duree<'08:00:00' and dureejours<1 ";
			$result=mysql_query($query,$GLOBALS["dbh"]);
			if ($result>0) {
				echo "Nombre de réunions";
				echo "<div class='bigstat'>".mysql_num_rows($result)."</div>";
			}
			$query="SELECT SEC_TO_TIME(avg(TIME_TO_SEC(TIMEDIFF(meet_closing, meet_opening)))) as duree FROM `t_meeting` WHERE TIMEDIFF(meet_closing, meet_opening)>'00:15:00'and TIMEDIFF(meet_closing, meet_opening)<'08:00:00' and DATEDIFF(meet_closing, meet_opening)<1 ";
			$result=mysql_query($query,$GLOBALS["dbh"]);
			if ($result>0) {
				echo "<div class='stat_add'>Durée moyenne:";
				$phpdate = strtotime(mysql_result($result,0,"duree") );
				$mysqldate = date( 'h\hi', $phpdate );
				echo $mysqldate;
				echo "</div>";
			}
			
			echo "</td><td width='20%'  style='text-align:center'>";
			
			$query="select * from t_tension ";
			$result=mysql_query($query,$GLOBALS["dbh"]);
			if ($result>0) {
				echo "Nombre de tensions";
				echo "<div class='bigstat'>".mysql_num_rows($result)."</div>";
			}
			$query="select * from t_tension where not tens_dateend is NULL";
			$result=mysql_query($query,$GLOBALS["dbh"]);
			$query="select distinct meet_id from tl_tension_meeting";
			$result2=mysql_query($query,$GLOBALS["dbh"]);

			if ($result>0) {
				echo "<div class='stat_add'>Dont ".mysql_num_rows($result)." traitées durant ".mysql_num_rows($result2)." réunions</div>";
			}
			
			echo "</td></tr></table>";

			echo "<div class='omo_presentation'>";
			
			echo "<h1>Transparence et agilité en gouvernance</h1>";
			echo "OpenMyOrganization est un logiciel qui soutient la mise en place de techniques de gouvernance partagée au sein d'organisations, que ce soit dans le domaine professionnel ou associatif. OpenMyOrganization permet de clarifier les rôles au sein de votre organisation, centraliser les règles et tenir à jour la liste des activités de chacun dans le groupe.";
			echo "En s'appuyant sur la transparence radicale, OpenMyOrganization donne les outils à chque personne pour contribuer en toute autonomie à la réalisation d'un but commun.";
			echo "<h2>Accessible à tous</h2>";
			echo "De par son modèle de financement, OpenMyOrganization est avant tout conçu pour les organisations oeuvrant pour le bien commun. A travers le don, nous espérons rendre accessible les moyens et les outils nécessaires permettant aux groupes qui construisent un avenir durable d'être encore plus efficaces et pouvoir se concentrer sur le coeur de leur activité: leurs projets.";
			echo "<h2>Envie de découvrir?</h2>";
			echo "Créez un compte! Cela ne vous engage à rien. Vous aurez l'occasion d'explorer le logiciel, créer votre structure, inviter vos collaborateurs. Plusieurs vidéo permettent de comprendre le fonctionnement de l'outil, mais n'hésitez pas à découvrir <a href='http://ateliersinstantz.net/gouvernance-partagee-autorite-distribuee/'>les ateliers</a> que nous proposons pour vous accompagner dans la mise en place de ces techniques: <a href='http://www.ateliersinstantz.net'>www.ateliersinstantz.net</a>.";
		
			echo "<div align='center' style='padding:30px;'><input placeholder='Votre adresse e-mail' id='txt_mail'><input type='button' value='Créer un compte' id='btn_newmember'></div>";
		
			echo "</div>";



			echo "</div>";

			echo "<script>$('#btn_login').button()</script>";
			echo "<script>$('#btn_visit').button()</script>";
			echo "<script>$('#btn_try').button()</script>";
			
?>	
	</body>
</html>
<?
	include_once("class/widget/nav/nav.php");
	} // fonction Display
}
?>
