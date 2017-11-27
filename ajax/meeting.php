<?
	// Inclus les éléments partagés entre plusieurs pages: base de donnée, instanciation de classe, etc...
	include_once("../include.php");
	
	// Formulaire destiné à être inclus, qui utilisera le manager du niveau supérieur si possible
	if (!isset($manager)) {
		$manager=new \datamanager\sqlManager($dbh);
	}
	
	if (isset($_REQUEST["action"])) {
		if (isset($_REQUEST["action"]) && $_REQUEST["action"]=="sendChat") {
			$chat=new \holacracy\Chat ();
			$chat->setText(utf8_decode($_REQUEST["txt"]));
			$chat->setUserId($_SESSION["currentUser"]->getId());
			$chat->setMeetingId($_REQUEST["id"]);
			$_SESSION["currentManager"]->save($chat);
		}

		if (isset($_REQUEST["action"]) && $_REQUEST["action"]=="checkTension") {
			$tension=$_SESSION["currentManager"]->loadTension($_REQUEST["id"]);
			$tension->check($_REQUEST["val"]=="true");
			$_SESSION["currentManager"]->save($tension);
			//echo  $_REQUEST["id"]."-". $_REQUEST["val"];
		}	
			
		if (isset($_REQUEST["action"]) && $_REQUEST["action"]=="sendTension") {
			$tension=new \holacracy\Tension ();
			$meeting=$_SESSION["currentManager"]->loadMeeting($_REQUEST["id"]);
			$tension->setTitle(utf8_decode($_REQUEST["txt"]));
			if (isset($_REQUEST["user"]) && $_REQUEST["user"]!="") {
				$tension->setUserId($_REQUEST["user"]);
			} else {
				$tension->setUserId($_SESSION["currentUser"]->getId());
			}
			if (isset($_REQUEST["role"]) && $_REQUEST["role"]!="") {
				$tension->setRoleId($_REQUEST["role"]);
			}
			if (isset($_REQUEST["type"]) && $_REQUEST["type"]!="") {
				$tension->setTypeId($_REQUEST["type"]);
			}
			$tension->setCircleId($meeting->getCircleId());
			$tension->setOrganisationId($meeting->getOrganisationId());
			$_SESSION["currentManager"]->save($tension);
			$meeting->addTension($tension->getId());
		}

		if ($_REQUEST["action"]=="sendScratch") {
			$meeting=$_SESSION["currentManager"]->loadMeeting($_REQUEST["id"]);
			$meeting->setScratchpad(utf8_decode(urldecode($_REQUEST["txt"])));
			$meeting->setScratchdate(new DateTime());
			$_SESSION["currentManager"]->save($meeting);
			
		}
		if (isset($_GET["action"]) && $_GET["action"]=="setsecretary") {
			// Défini la date d'ouverture
			$meeting=$_SESSION["currentManager"]->loadMeeting($_GET["id"]);
			$meeting->setSecretaryId($_SESSION["currentUser"]->getId());
			$_SESSION["currentManager"]->save($meeting);
			echo "alert('Vous avez pris la place du secrétaire');location.reload();";
		}
		if (isset($_GET["action"]) && $_GET["action"]=="start") {
			// Défini la date d'ouverture
			$meeting=$_SESSION["currentManager"]->loadMeeting($_GET["id"]);
			$meeting->setOpeningTime(new DateTime());
			$_SESSION["currentManager"]->save($meeting);
			echo "alert('La réunion est débutée');location.reload();";
		}
		if (isset($_GET["action"]) && $_GET["action"]=="close") {
			// Défini la date de clôture
			$meeting=$_SESSION["currentManager"]->loadMeeting($_GET["id"]);
			$meeting->setClosingTime(new DateTime());
			$_SESSION["currentManager"]->save($meeting);
			
			echo "alert('La réunion est clôturée');location.reload();\n\n";

			// Envoie les e-mails à qui de droit
			// A implémenter... - DDr, 7.8.2014
			
			// Récupération de la liste des membres - DDr, 7.8.2014
			$members=$meeting->getCircle()->getMembers();
			
			// Céation du message - DDr, 7.8.2014
			$title="[".$meeting->getOrganisation()->getName()."] [".$meeting->getCircle()->getName()."] PV de la réunion de ".$meeting->getMeetingType()." du ".$meeting->getDate()->format("d.m.Y")."";
			$content="La réunion de ".$meeting->getMeetingType()." du cercle [".$meeting->getCircle()->getName()."] s'est terminée aujourd'hui ".$meeting->getClosingTime()->format("d.m.Y à H:i").". Vous pouvez retrouver le PV décisionnel directement en suivant ce lien: http://".$_SERVER['SERVER_NAME']."/pdf/meeting.php?id=".$meeting->getId()."&code=".md5($meeting->getOpeningTime()->format("dmY").$meeting->getClosingTime()->format("dmY")).". Merci de prendre note des changements dans le fonctionnement de l'organisation et/ou des actions à entreprendre.";

			// Envoi du message à chaque membre - DDr, 7.8.2014
			foreach ($members as $member) {
				$member->sendMessage ($title, $content);
			}

		}
	}
	
?>


