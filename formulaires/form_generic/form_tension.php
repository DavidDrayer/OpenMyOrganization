<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />

<?

	$nom=$description="";
	if (isset($_GET["project"])) {
		// Modification d'un cercle existant
		echo "<input type='hidden' name='idProject_".$_GET["id"]."' value='".$_GET["project"]."' >";
		// Chargement de l'objet et initialisation des variables
		$project=$_SESSION["currentManager"]->loadProject($_GET["project"]);
		$nom=$project->getName();
		$description=$project->getDescription();
	}
	if (isset($_GET["tension"])) {
		// Modification d'un cercle existant
		echo "<input type='hidden' class='nodelete' name='idTension_".$_GET["id"]."' value='".$_GET["tension"]."' >";
		// Chargement de l'objet et initialisation des variables
		$tension=$_SESSION["currentManager"]->loadTension($_GET["tension"]);
		$isMy=($tension->getUserId()==$_SESSION["currentUser"]->getId());
		$title=$tension->getTitle();
		$description=$tension->getDescription();
		$type=$tension->getTypeId();
	} else exit;
	if (isset($_GET["meeting"])) {
		$meeting=$_SESSION["currentManager"]->loadMeeting($_GET["meeting"]);
		$isSecretary=($meeting->getSecretaryId()==$_SESSION["currentUser"]->getId());
		$isInProcess=($meeting->getOpeningTime()!=null && $meeting->getClosingTime()==null);
		$closed=($meeting->getClosingTime()!=null);
	} else exit;
	
?>
<script>
	$(function () {;
		// ***********************************************************
		// Affichage de la liste des rôles si un membre est sélectionné
		$('select#idProposer_<?php echo $_GET["id"];?>').change(function () {
		var ProposerIDselect = this.value; //Recuperation du rôle selectionné

		if($.isNumeric(ProposerIDselect)) {
			   var dataTab = {
				"SelectformID": <?php echo $_GET["id"];?>,
				"ProposerID": ProposerIDselect,
				"CircleID": <?=$meeting->getCircleId()?>,
				"action": "GetRoles",
				"selected": '<?php echo $tension->getRoleId();?>'
				};
			//On fait la MAJ en Ajax
			$.ajax({
					   type : "POST",
					   url : "ajax/formtriage.php",
					   data : dataTab,
						success: function(data){
						if(!$.isNumeric(data)){//Si il y a des focus
							$("input#idRoleProposer_"+<?php echo $_GET["id"];?>).remove();
							$("select#idRoleProposer_"+<?php echo $_GET["id"];?>).remove();
							$("#idRoleProposer"+<?php echo $_GET["id"];?>).append(data); //on ajoute le select focus
							$("#idRoleProposer"+<?php echo $_GET["id"];?>).removeClass("hidefocus");
						} 					
						},
					  error:function(){
						  alert("Erreur Ajax");
					  } 
					});
		} else{ //Par defaut on efface
		$("select#idRoleProposer_"+<?php echo $_GET["id"];?>).remove();
		$("input#idRoleProposer_"+<?php echo $_GET["id"];?>).remove();
		$("#idRoleProposer"+<?php echo $_GET["id"];?>).addClass("hidefocus");
		}
		});

		$('select#idProposer_<?php echo $_GET["id"];?>').change();
	});
	</script>
<?	
	
	
	
	echo "<div class='light'>";

	// Titre (2 mots) de la tension
	echo "<div>Titre : <input ".($closed || !($isMy || $isSecretary)?"disabled":"")." id='titleTension_".$_GET["id"]."' type='text' name='titleTension_".$_GET["id"]."' value='$title' style='width:100%'></div>";

	// Type de tension
	echo "<div>Type : <div>";
	echo "<input ".($type<1?"checked":"")." type='radio' ".($closed || !($isMy || $isSecretary)?"disabled":"")." name='typeTension_".$_GET["id"]."' value='0'><img src='/images/icon_point.png'> &nbsp;";
	echo "<input ".($type==1?"checked":"")." type='radio' ".($closed || !($isMy || $isSecretary)?"disabled":"")." name='typeTension_".$_GET["id"]."' value='1'><img src='/images/icon_tension.png'> &nbsp;";
	echo "<input ".($type==2?"checked":"")." type='radio' ".($closed || !($isMy || $isSecretary)?"disabled":"")." name='typeTension_".$_GET["id"]."' value='2'><img src='/images/icon_info.png'> &nbsp;";
	echo "<input ".($type==3?"checked":"")." type='radio' ".($closed || !($isMy || $isSecretary)?"disabled":"")." name='typeTension_".$_GET["id"]."' value='3'><img src='/images/icon_important.png'>";
	echo "</div></div>";

	// Description (utile si rentré en avance, ou pour le secrétaire pour noter le contexte
	echo "<div>Description : <textarea ".($closed || !($isMy || $isSecretary)?"disabled":"")." style='resize: vertical; max-height:200px; height:130px; width:100%' id='textInfo_".$_GET["id"]."' type='text' name='textInfo_".$_GET["id"]."'>".$description."</textarea></div>";

	if ($closed || !($isMy || $isSecretary)) {
		echo ($tension->getUserId()>0?"<div>Amené par : <br/><b>".$tension->getUser()->getUserName()."</b>".($tension->getRoleId()>0?" dans son rôle <b>".$tension->getRole()->getName()."</b>":"")."</div>":"");
	} else { 
		// Affichage de deux colonnes
		echo "<table style='width:100%;' cellspacing=0 cellpadding=0><tr><td style='width:50%; padding-right:10px;'>";
		echo "<div>Amené par : <br/><select name='idProposer_".$_GET["id"]."' id='idProposer_".$_GET["id"]."'>";
		echo "<option value=''>Choisissez...</option>";
		// Charge le cercle en cours
		$circle=$meeting->getCircle();
		// Charge la liste des rôles qui peuvent être modifiés
		$roles=$circle->getMembers();
		// Affiche la liste
		foreach ($roles as $role) {
			echo "<option value='".$role->getId()."' ";
			if ($role->getId()==$tension->getUserId()) echo " selected";
			echo ">".$role->getUserName()."</option>";
		}
		echo "</select></div>";
		echo "</td><td style='width:50%'>";	
			
		echo "<div id='idRoleProposer".$_GET["id"]."' class='hidefocus'>Dans son rôle : <br/></div>";
		
		// echo "<div id='idProjectProposer".$_GET["id"]."' class='hidefocus'>Attacher à un projet : <br/></div>";
		echo "</td></tr></table>";
	}
	
?>

	</div> 

