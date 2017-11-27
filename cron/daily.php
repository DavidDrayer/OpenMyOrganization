<?
	// clé API du Bot à modifier
	define('TOKEN', '453413943:AAHW70VKXpgr1b2cpk0szerbu5K7Kid-es8');

	// fonction qui envoie un message à l'utilisateur
	function sendMessage($chat_id, $text) {
		$q = http_build_query([
			'chat_id' => $chat_id,
			'text' => utf8_encode($text)
			]);
		file_get_contents('https://api.telegram.org/bot'.TOKEN.'/sendMessage?'.$q);
    }
    
	header('Content-Type: text/html;charset=iso-8859-1');
		$month=array(1 => 'Jan', 2 => 'Fev',3 => 'Mars', 4 => 'Avr',5 => 'Mai', 6 => 'Juin',7 => 'Juil', 8 => 'Aout',9 => 'Sept', 10 => 'Oct',11 => 'Nov', 12 => 'Dec');
		$day=array(0 => 'Dim', 1 => 'Lun',2 => 'Mar', 3 => 'Mer',4 => 'Jeu', 5 => 'Ven',6 => 'Sam', 7 => 'Dim');

	// Inclus les éléments partagés entre plusieurs pages: base de donnée, instanciation de classe, etc...
	$root = realpath($_SERVER["DOCUMENT_ROOT"]);
	include_once($root."/include.php");
	// Instantiation du gestionnaire de base de donnée
	$manager=new \datamanager\SqlManager($_SESSION["currentDB"]);
	set_time_limit(60);
	// charge l'ensemble des utilisateurs
	$users=$manager->loadMemberListe();
	
	// Pour chaque utilisateur, charge les prochaines réunions
	foreach ($users as $user) {
		$meetings= $user->getMeetings();
		$mailtxt="";
		$mailavanttxt="";
		$telegramtxt="";
		$delayMail=1;
		$delayTelegram=-1;
		foreach ($meetings as $meeting) {
			// Si la réunion est dans 4 jours, ajoute son contenu à l'email
			$interval=(new DateTime())->diff($meeting->getDate())->format('%R%a');
			if ($interval>=0 && $interval<$delayMail) {
				$mailavanttxt.="<div>".$meeting->getDate()->format("d")." ".$month[$meeting->getDate()->format("n")].": [".$meeting->getOrganisation()->getName()."] Réunion de <a href='http://".$_SERVER["HTTP_HOST"]."/meeting.php?id=".$meeting->getId()."'>".$meeting->getMeetingType()."</a> pour le cercle ".$meeting->getCircle()->getName()."</div>";
			}
			if ($interval==$delayMail) {
				$mailtxt.="<div style='float:left;border:2px solid rgb(98, 124, 146); text-align:center; border-radius:5px; width:65px;background:rgba(255, 255, 255, 0.7); margin-right:10px;' ><div style='background:rgb(98, 124, 146); color:#FFFFFF; text-transform: uppercase;font-weight:bold;text-shadow: 1px 1px rgb(0,0,0);'>".$month[$meeting->getDate()->format("n")].($meeting->getDate()->format("y")!=date("y")?" ".$meeting->getDate()->format("n"):"")."</div><div style='font-size:30px; text-shadow: 0px 0px 3px rgb(255,255,255);'>".$meeting->getDate()->format("d")."</div><div style='text-transform: lowercase;font-weight:bold;'>".$day[$meeting->getDate()->format("w")]."</div></div>";
				
				$mailtxt.="<div><b>";	
				$mailtxt.="<span style='font-size: 150%;'>".$meeting->getOrganisation()->getName()."</span><br/>";	
				$mailtxt.="Réunion de <a href='http://".$_SERVER["HTTP_HOST"]."/meeting.php?id=".$meeting->getId()."'>";
				$mailtxt.=$meeting->getMeetingType();
				$mailtxt.="</a><br/>pour le cercle <span style='white-space: pre;'><a href='http://".$_SERVER["HTTP_HOST"]."/circle.php?id=".$meeting->getCircle()->getId()."'>".$meeting->getCircle()->getName()."</a></span>"."</b></div><div style='float:right; margin:8px; max-width:50%'><a href='http://".$_SERVER["HTTP_HOST"]."/meeting.php?id=".$meeting->getId()."&go=".$user->getId()."' style='width:150px; padding:3px;  color:#0055FF; background:#FFFFFF; text-decoration:none'></a><a href='http://".$_SERVER["HTTP_HOST"]."/meeting.php?id=".$meeting->getId()."&go=".$user->getId()."' style='width:150px; white-space: no-wrap; padding:3px; border:2px solid #0055FF; color:#0055FF; background:#FFFFFF; text-decoration:none'>J'y vais</a> <a href='http://".$_SERVER["HTTP_HOST"]."/meeting.php?id=".$meeting->getId()."&nogo=".$user->getId()."' style='padding:5px; border:2px solid #0055FF; color:#0055FF; background:#FFFFFF; text-decoration:none'>Ne suis absent</a></div><div style='font-size:smaller'>";
				
				// Affiche des infos sur la réunion, comme si elle est en cours, terminée, ou ses horaires
				$mailtxt.="De ".substr($meeting->getStartTime(),0,-3)." "."à"." ".substr($meeting->getEndTime(),0,-3).", ".($meeting->getLocation()!=""?$meeting->getLocation():"<i>lieu indéfini</i>");
				$mailtxt.="</div><div style='clear:both;'></div>";
					
			}
			// Si son contenu est dans un jour, ajoute son contenu à telegram
			if ($interval==$delayTelegram)
			$telegramtxt.="Rappel: Réunion aujourd'hui ".$meeting->getStartTime()." à ".$meeting->getLocation()." pour ".$meeting->getOrganisation()->getName();
		}
		
		// Si l'email est différent de rien, l'envoie
		if ($mailtxt!="") {
			echo "<p>----------MAIL------------------</p>";
			echo "<p>Pour: ".$user->getUserName()."</p>";
			
			
			$mailtxt="<div style='padding:15px 5px; background:#DDDDDD'><div style='padding:5px; background:#FFFFFF'>".$mailtxt;
			if ($mailavanttxt!="") {
				$mailtxt.="&nbsp;<br/><div><div><b>Et avant ça: </b></div>".$mailavanttxt."</div>";
			}
			$mailtxt.="</div></div>";
			echo $mailtxt;
			if ($user->getId()==3) {
			  $user->sendMessage("Rappel: Prochaine(s) Réunion(s)",$mailtxt);
			}
		}
		if ($telegramtxt!="") {
			echo "<p>----------TELEGRAM-------------</p>";
			echo "<p>Pour: ".$user->getUserName()."</p>";
			echo $telegramtxt;
			if ($user->getId()==3) {
				sendMessage("435667788", $telegramtxt);
			}
		}
		
		// Si telegram est différent de rien et que l'ID telegram est actif, l'envoie
		
	}
?>
