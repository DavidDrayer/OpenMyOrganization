<?
	// Inclus les �l�ments partag�s entre plusieurs pages: base de donn�e, instanciation de classe, etc...
	include_once("../include.php");
	
	// Formulaire destin� � �tre inclus, qui utilisera le manager du niveau sup�rieur si possible
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
			// D�fini la date d'ouverture
			$meeting=$_SESSION["currentManager"]->loadMeeting($_GET["id"]);
			$meeting->setSecretaryId($_SESSION["currentUser"]->getId());
			$_SESSION["currentManager"]->save($meeting);
			echo "alert('Vous avez pris la place du secr�taire');location.reload();";
		}
		if (isset($_GET["action"]) && $_GET["action"]=="start") {
			// D�fini la date d'ouverture
			$meeting=$_SESSION["currentManager"]->loadMeeting($_GET["id"]);
			$meeting->setOpeningTime(new DateTime());
			$_SESSION["currentManager"]->save($meeting);
			echo "alert('La r�union est d�but�e');location.reload();";
		}
		if (isset($_GET["action"]) && $_GET["action"]=="close") {
			// D�fini la date de cl�ture
			$meeting=$_SESSION["currentManager"]->loadMeeting($_GET["id"]);
			$meeting->setClosingTime(new DateTime());
			$_SESSION["currentManager"]->save($meeting);
			
			echo "alert('La r�union est cl�tur�e');location.reload();\n\n";

			// Envoie les e-mails � qui de droit
			// A impl�menter... - DDr, 7.8.2014
			
			// R�cup�ration de la liste des membres - DDr, 7.8.2014
			$members=$meeting->getCircle()->getMembers();
			
			// C�ation du message - DDr, 7.8.2014
			$title="[".$meeting->getOrganisation()->getName()."] [".$meeting->getCircle()->getName()."] PV de la r�union de ".$meeting->getMeetingType()." du ".$meeting->getDate()->format("d.m.Y")."";
			$content="La r�union de ".$meeting->getMeetingType()." du cercle [".$meeting->getCircle()->getName()."] s'est termin�e aujourd'hui ".$meeting->getClosingTime()->format("d.m.Y � H:i").". Vous pouvez retrouver le PV d�cisionnel directement en suivant ce lien: http://".$_SERVER['SERVER_NAME']."/pdf/meeting.php?id=".$meeting->getId()."&code=".md5($meeting->getOpeningTime()->format("dmY").$meeting->getClosingTime()->format("dmY")).". Merci de prendre note des changements dans le fonctionnement de l'organisation et/ou des actions � entreprendre.";

			// Envoi du message � chaque membre - DDr, 7.8.2014
			foreach ($members as $member) {
				$member->sendMessage ($title, $content);
			}

		}
	}
	
?>


