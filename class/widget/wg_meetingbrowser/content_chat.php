<?
	// Affichage des entrées du chat
	$chatEntry=$meeting->getChat();
	for ($i=count($chatEntry)-1; $i>=0; $i--) {
		if ($i==count($chatEntry)-1 || $chatEntry[$i]->getUser()->getId()!=$chatEntry[$i+1]->getUser()->getId())
		echo "<div style='font-weight:bold; font-size:smaller'>".$chatEntry[$i]->getUser()->getFirstName()." ".$chatEntry[$i]->getUser()->getLastName().":</div>";
		echo "<div style='padding-bottom:5px;'>".$chatEntry[$i]->getText()."</div>";
	}
?>