<?
	// Session et environnement
	include_once("../include.php");
 	$currentTime=new DateTime();

	$meeting=$_SESSION["currentManager"]->loadMeeting($_GET["id"]);
 	
 	// Faut-il raffraichir l'historique?
 	$historique=$meeting->getHistory();
 	$refreshHistory=false;
 	if (count($historique)>0 && $currentTime->getTimestamp()-$historique[0]->getDate()->getTimestamp()<=$_GET["time"]) {
  		$refreshHistory=true;
	}
	
 	// Faut-il raffraichir le chat?
  	$chatEntries=$meeting->getChat();
  	$refreshChat=false;
  	if (count($chatEntries)>0 && $currentTime->getTimestamp()-$chatEntries[0]->getDate()->getTimestamp()<=$_GET["time"]) {
  		$refreshChat=true;
	}
	
	// Faut-il raffraichir les tensions?
  	$tensionEntries=$meeting->getTensions();
  	$refreshTension=false;
  	if (count($tensionEntries)>0 && $currentTime->getTimestamp()-$tensionEntries[0]->getDate()->getTimestamp()<=$_GET["time"]) {
  		$refreshTension=true;
	}
	
 	// Faut-il rafraichir le scrachpad?
   	$refreshScratch=false;
  	if ($currentTime->getTimestamp()-$meeting->getScratchDate()->getTimestamp()<=$_GET["time"]) {
  		$refreshScratch=true;
	}
	
 	

	if ($refreshHistory) {
		echo "refreshHistory(".$meeting->getId().");";
	}
	if ($refreshScratch) {
		echo "refreshScratch(".$meeting->getId().");";
	}
	if ($refreshChat) {
		echo "refreshChat(".$meeting->getId().");";
	}
	if ($refreshTension) {
		echo "refreshTension(".$meeting->getId().");";
	}

	// Pour chaque élément de la liste, crée le code adéquat
 	
?>
