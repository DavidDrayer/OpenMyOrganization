<?
	echo "<div class='grey_design'>";
	$oldDate="";
	foreach($historique as $entry) {
		$currentDate=$entry->getDate()->format("d.m.Y");
		if ($currentDate!=$oldDate) {
			
			if ($oldDate!="") {
				echo "</table></div>";
			}
			echo "<div style='padding:5px; font-weight:bold;' class='ui-state-default ui-state-active ui-corner-top '>";
			echo $currentDate;
			echo "</div>";
			echo "<div style='padding:15px; margin-bottom:6px;' class=' ui-helper-reset ui-widget-content ui-corner-bottom '>";
			echo "<table>";		
			
			$oldDate=$currentDate;
		}
		echo "<tr><td nowrap><b>";
		echo $entry->getDate()->format("H:i");
		echo " - </b></td><td>";
		$childs=$entry->getChilds();
	
			echo "<div>".$entry->getTitle()."</div>";
	
		if (count($childs)>0) {
			foreach($childs as $child) {
				echo "<div";
			if (!is_null($child->getRole()) && $_SESSION["currentUser"]->isRole($child->getRole()) ) echo " class='omo-me'";
			echo ">".$child->getTitle();
				if ($child->getLink()!="") echo " (<a href='".$child->getLink()."'>".T_("lien")."</a>)";
				echo "</div>";
				// Y a-t-il encore des précisions
				$childs2=$child->getChilds();
				if (count($childs2)>0) {
					echo "<ul>";
					foreach($childs2 as $child2) {
						echo "<li>".$child2->getTitle()."</li>";
						echo ($child2->getDescription());
					}
					echo "</ul>";
				}
			}
		}
		echo "</td></tr>";
	}
	if ($oldDate!="") {echo "</table></div>";}
	echo "</div>";
?>