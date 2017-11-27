<?
	// Affichage des entrées du chat
	$chatEntry=$circle->getChat();
	for ($i=count($chatEntry)-1; $i>=0; $i--) {
		// Affichage de la photo, du nom et de la date
		if ($i==count($chatEntry)-1 || $chatEntry[$i]->getUser()->getId()!=$chatEntry[$i+1]->getUser()->getId() || $chatEntry[$i]->getDate()->diff($chatEntry[$i+1]->getDate())->format('%R%a')!=0) {
			if (checkMini("/images/user/".$chatEntry[$i]->getUser()->getId().".jpg",30,30,"mini",1,5)) {
				echo "<a href='/user.php?id=".$chatEntry[$i]->getUser()->getId()."&circle=".$chatEntry[$i]->getCircleId()."' class='dialogPage omo-user-img' alt='".$chatEntry[$i]->getUser()->getFullName()."'><img src='/images/user/mini/".$chatEntry[$i]->getUser()->getId().".jpg'/></a>";
			} else {
				echo "<a href='/user.php?id=".$chatEntry[$i]->getUser()->getId()."&circle=".$chatEntry[$i]->getCircleId()."' class='dialogPage omo-user-img' alt='".$chatEntry[$i]->getUser()->getFullName()."'><img src='/images/user/mini/0.jpg'/></a>";
			}
			echo "<div style='font-weight:bold; font-size:smaller'>".$chatEntry[$i]->getUser()->getFirstName()." ".$chatEntry[$i]->getUser()->getLastName()."</div>";
			echo "<div style='font-size:smaller; color:#aaaaaa'>publié le ".$chatEntry[$i]->getDate()->format('d.m.Y à H\hi')."</div>";
		}
		echo "<div style='padding-bottom:5px;margin-left:40px; background-color:#FFFFFF; border-radius: 0px 8px 8px 8px; margin: 4px 0px 4px 40px;padding:6px;'>".str_replace("\n","<br>",$chatEntry[$i]->getText())."</div>";
	}
?>
