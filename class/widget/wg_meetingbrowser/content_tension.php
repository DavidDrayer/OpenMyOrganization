<?
	// Suis-je le secrétaire du meeting?
	$isSecretary=($_SESSION["currentUser"]->getId()>1 && $meeting->getSecretaryId()==$_SESSION["currentUser"]->getId());
	// Le meeting est-il en cours?
	$isInProcess=($meeting->getOpeningTime()!=null && $meeting->getClosingTime()==null);
	// Affichage des entrées du chat
	$tensionEntry=$meeting->getTensions();
	echo "<div id='sortable'>";
	for ($i=0; $i<count($tensionEntry); $i++) {
		echo "<div alt='Traiter une tension' href='formulaires/form_meeting2.php?meeting=".$meeting->getId()."&circle=".$meeting->getCircleId()."&tension=".$tensionEntry[$i]->getId()."' class='liste_tension dialogPage2 liste_tension_".$tensionEntry[$i]->getTypeId()."'><input ".($tensionEntry[$i]->isChecked()?"checked":"")." class='chkbx_tension' val='".$tensionEntry[$i]->getId()."' type='checkbox' ".($isSecretary && $isInProcess?"":"disabled")."> ".$tensionEntry[$i]->getTitle()."<span style='color:rgba(0,0,0,0.5)'>".($tensionEntry[$i]->getUserId()>0?" - ".$tensionEntry[$i]->getUser()->getUserName().($tensionEntry[$i]->getRoleId()>0?" (".$tensionEntry[$i]->getRole()->getName().")":""):"")."</span></div>";
	}
	echo "</div>";
?>
<script>
    $( "#sortable" ).sortable();
    $( "#sortable" ).disableSelection();
</script>
