<?
	include_once("../include.php");
	
if (isset($_POST["action"]) ) {
	if ($_POST["action"]=="add" || $_POST["action"]=="start") {
		if ($_POST["meeting"]!="") {
			// Edite un meeting existant
			$meeting=$_SESSION["currentManager"]->loadMeeting($_POST["meeting"]);

		} else {
			// Crée un nouveau meeting
						// Crée un nouveau bug
			$meeting=new \holacracy\Meeting();

		}
		$circle=$_SESSION["currentManager"]->loadCircle($_POST["circle"]);

		//$meeting->setTitle(utf8_decode($_POST["bugTitle"]));
		$meeting->setLocation(utf8_decode($_POST["lieuMeeting"]));
		$meeting->setCircle($_POST["circle"]);
		$meeting->setOrganisation($circle->getOrganisation()->getId());
		$meeting->setMeetingTypeId($_POST["typeMeeting"]);
		$meeting->setDate($_POST["datepickerMeeting"]);
		$meeting->setStartTime($_POST["timepicker_start"]);
		$meeting->setEndTime($_POST["timepicker_end"]);
		// Et le sauve
		$_SESSION["currentManager"]->save($meeting);
		$_POST["bug"]=$meeting->getId();			
		
		// Confirme l'enregistrement
		echo "Rechargement de la page...";
		if ($_POST["action"]=="add") {
			echo "<script>$('#dialogStdContent').load('/formulaires/form_meeting.php?circle=".$_POST["circle"]."', function() {showDialog();});</script>";
		} else {
			echo "<script>document.location='meeting.php?id=".$meeting->getId()."'</script>";
		}
	}
	exit;
} else
if (isset($_GET["action"]) && ($_GET["action"]=="new" || $_GET["action"]=="edit")) {
	if ($_GET["action"]=="edit") {
		// Charge les infos sur le meeting
		$meeting=$_SESSION["currentManager"]->loadMeeting($_GET["id"]);
		$location=$meeting->getLocation();
		$circle=$meeting->getCircle()->getId();
		$date=$meeting->getDate()->format("d.m.Y");
		$timeStart=$meeting->getStartTime();
		$timeEnd=$meeting->getEndTime();
		$meetingid=$meeting->getId();
		$type=$meeting->getMeetingTypeId();
	} else {
		// Valeurs par défaut
		$circle=$_GET["circle"];
		$location="";
		$date=date('d.m.Y');
		$timeStart=date('G:i:s');
		$timeEnd=date('G:i:s', time()+3600);
		$meetingid="";
		$type="";
	}

	echo "<form id='formulaire'>";
	echo "<input type='hidden' id='form_target' value='/formulaires/form_meeting.php'>";
	echo "<input type='hidden' name='action' id='action' value='add'>";
	echo "<input type='hidden' name='circle' id='circle' value='".$circle."'>";
	echo "<input type='hidden' name='meeting' id='meeting' value='".$meetingid."'>";
	echo "<p>Date: <input type='text' name='datepickerMeeting' id='datepickerMeeting' value='".$date."'></p>";
	echo "<p>Horaire: de <input onkeypressed='return false;' type='text' name='timepicker_start' id='timepicker_start' style='width:80px'  value='".substr($timeStart,0,-3)."'> à ";
	echo "<input onkeypressed='return false;' type='text' name='timepicker_end' id='timepicker_end'  style='width:80px'  value='".substr($timeEnd,0,-3)."'></p>";
	echo "<p>Lieu: <input type='text' id='lieuMeeting' name='lieuMeeting' value='".$location."'></p>";
	echo "<p>Type: <select name='typeMeeting' id='typeMeeting'>";
	// Affiche les différents types de réunion
	echo "<option value='1'>Gouvernance</option>";
	echo "<option value='2' ";
	if ($type==2) echo " selected "; 
	echo ">Triage</option>";
	echo "<option value='3' ";
	if ($type==3) echo " selected "; 
	echo ">Mixte</option>";
	echo "<option value='4' ";
	if ($type==4) echo " selected "; 
	echo ">Stratégie</option>";
	echo "<option value='5' ";
	if ($type==5) echo " selected "; 
	echo ">Design</option>";


	echo "</select></p>";
	echo "</form>";
?>

  <script>
  
// when start time change, update minimum for end timepicker
function tpStartSelect( time, endTimePickerInst ) {
   $('#timepicker_end').timepicker('option', {
       minTime: {
           hour: endTimePickerInst.hours,
           minute: endTimePickerInst.minutes
       }
   });
}

function showDialog() {
	$("#dialogLoading").css("display","none");
	//alert('Yop');
}

// when end time change, update maximum for start timepicker
function tpEndSelect( time, startTimePickerInst ) {
   $('#timepicker_start').timepicker('option', {
       maxTime: {
           hour: startTimePickerInst.hours,
           minute: startTimePickerInst.minutes
       }
   });
}

  $(function() {
  
  	   $('#timepicker_start').timepicker({
       showLeadingZero: false,
       onSelect: tpStartSelect
   });
   $('#timepicker_end').timepicker({
       showLeadingZero: false,
       onSelect: tpEndSelect
   });

     $( "#datepickerMeeting" ).datepicker({
      changeMonth: true,
      changeYear: true,
      dateFormat:"dd.mm.yy"
    });
    
    
    	  $("#formulaire").submit(function() {
	  	
		// Envoie le formulaire en AJAX (méthode POST), la destination étant définie par l'élément à l'ID form_target 
	 	$.post($("#form_target")[0].value, $("#formulaire").serialize()) 
	        .done(function(data, textStatus, jqXHR) {
	            if (textStatus="success")
	            {
	            	// Traite une éventuelle erreur
	            	if (data.indexOf("Erreur")>0) {
	            		eval(data);
	            	} else {
	            	
	            	
		            	// Affiche les données en retour en remplacement du contenu du formulaire (le contenant reste) 
		                $("#formulaire")[0].innerHTML=data;
		                // Intérprète les scripts retournés (à vérifier si ça fonctionne)
		                eval($("#formulaire").find("script").text());
	                }
				}
	            else {
	            	// Problème d'envoi
	            	alert("Echec!");
	            
	            }
	        });
	        // Bloque la procédure standard d'envoi
	        return false;
	});
    
    $( "#dialogStd" ).dialog({ buttons: [ { text: "Démarrer la réunion", click: function() { $("#dialogLoading").css("display","");$("#action").val("start");$( "#formulaire").submit(); } },{ text: "Enregistrer et retourner à l'agenda", click: function() { $("#dialogLoading").css("display","");$( "#formulaire").submit(); } },{ text: "Annuler", click:function() {$("#dialogStdContent").load("/formulaires/form_meeting.php?circle=<?=$circle?>", function () {$("#dialogLoading").css("display","none");}); }}, {text: "Fermer", click: function() { $( this ).dialog( "close" ); }} ] });
   
  });
  </script>
<?
} else {
		$month=array(1 => 'Jan', 2 => 'Fev',3 => 'Mars', 4 => 'Avr',5 => 'Mai', 6 => 'Juin',7 => 'Juil', 8 => 'Aout',9 => 'Sept', 10 => 'Oct',11 => 'Nov', 12 => 'Dec');
		$day=array(0 => 'Dim', 1 => 'Lun',2 => 'Mar', 3 => 'Mer',4 => 'Jeu', 5 => 'Ven',6 => 'Sam', 7 => 'Dim');

		$circle=$_SESSION["currentManager"]->loadCircle($_GET["circle"]);

		$canEdit=(isset($_SESSION["currentUser"]) && ($_SESSION["currentUser"]->isRole(\holacracy\Role::LEAD_LINK_ROLE | \holacracy\Role::SECRETARY_ROLE,$circle) || $_SESSION["currentUser"]->isAdmin($circle)));
		$isAdmin=(isset($_SESSION["currentUser"]) && $_SESSION["currentUser"]->isAdmin($circle->getOrganisation()));
		$meetings=$circle->getMeetings();
		if (count($meetings)==0) {
			echo "<div>&nbsp;</div><i style='margin:30px; margin-top:60px;'>Aucune réunion planifiée dans ce cercle</i>";
		} else 
		foreach ($meetings as $meeting) {
			echo "<div class='omo-user-block ui-corner-all";
			echo " omo-meeting-type".$meeting->getMeetingTypeId();
			if ($meeting->getClosingTime()!="") {
				echo " omo-old-meeting";
			} else if ($meeting->getOpeningTime()!="") {
				echo " omo-current-meeting";
			} 
			
			echo "'>";
						// Affichage de la date sous forme de calandrier
			echo "<div style='float:left' class='omo-calendrier'><div class='omo-date-mois'>".$month[$meeting->getDate()->format("n")].($meeting->getDate()->format("y")!=date("y")?" ".$meeting->getDate()->format("m"):"")."</div><div  class='omo-date-nojour'>".$meeting->getDate()->format("d")."</div><div class='omo-date-jour'>".$day[$meeting->getDate()->format("w")]."</div></div>";

			if ($canEdit && $meeting->getOpeningTime()=="") {
			echo "<span style='float:right;  position: relative;'>";
					echo "<a class='omo-delete ajax' href='ajax/deletemeeting.php?id=".$meeting->getId()."' alt='".T_("Supprimer")."' check='".T_("Etes-vous s&ucirc;r de vouloir supprimer cette réunion?")."'>&nbsp;</a>";
					echo "<a class='omo-edit dialogPage' href='formulaires/form_meeting.php?action=edit&id=".$meeting->getId()."' alt='".T_("Editer les infos de la réunion")."'>&nbsp;</a>";
			echo "</span>";
			}

			echo "<div><b>";			
			echo "Réunion de ";
			echo "<a href='meeting.php?id=".$meeting->getId()."'>";
			echo $meeting->getMeetingType();
			echo "</a></b></div>";
			
			// Affiche des infos sur la réunion, comme si elle est en cours, terminée, ou ses horaires
			if ($meeting->getClosingTime()!="") {
				echo "La réunion est terminée depuis le ".$meeting->getClosingTime()->format("d.m.Y à H:i");
			} else if ($meeting->getOpeningTime()!="") {
				echo "La réunion est en cours depuis ".$meeting->getOpeningTime()->format("H:i, \l\e d.m.Y");				
			} else {
				echo "De ".substr($meeting->getStartTime(),0,-3)." à ".substr($meeting->getEndTime(),0,-3).", ".($meeting->getLocation()!=""?$meeting->getLocation():"<i>lieu indéfini</i>");
			}

			
			echo "<div style='clear:both;'></div></div>";
		}
		echo "<div style='margin-top:15px; padding-top:10px;border-top:1px solid #BBBBBB'>".T_(htmlentities("Importez le calendrier dans Google")).": <a href='/interface/ical.php?circle=".$circle->getId()."'><img style='vertical-align:bottom' src='/images/ical-feed.png'></a></div>";

?>
<script>
   $( "#dialogStd" ).dialog({ buttons: [ <? if ($canEdit) { ?>{ text: "Ajouter une réunion", click:function() {$("#dialogStdContent").load("/formulaires/form_meeting.php?action=new&circle=<?=$_GET["circle"]?>")}},<? } if ($canEdit || $isAdmin) { ?>{ text: "Out of Gouv", click:function() {$("#dialogStdContent").load("/formulaires/form_gouvernance.php?circle=<?=$_GET["circle"]?>")}}, <? } ?>{text: "Fermer", click: function() { $( this ).dialog( "close" ); }} ] });

</script>
<?
}
?>
