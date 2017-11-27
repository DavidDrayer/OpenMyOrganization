<?

// Affichage des statistiques

	// Nombre de membres

	// Nombre de projets en cours

	// Durée moyenne des réunions
	
// Affichage du Chat
	echo "<div style='position:relative; background-color:rgba(172,188,202,0.5); width:50%; border-left:10px solid white; box-sizing: border-box; float:right; height:100%'>";
	echo " <div id='chat' class='unselectable'  unselectable='on' style='overflow-y:auto; margin-bottom:5px;padding:5px;box-sizing: border-box; height: -moz-calc(100% - (130px));  height: -webkit-calc(100% - (130px));  height: calc(100% - (130px));'>";

	//$circle=$this->_circle;
	//include_once("content_chat.php");
	
	 echo "</div><div style='padding:0px 15px 0px 10px ; width:100%; box-sizing: border-box;'>";
	 
	 echo "<textarea ".(isset($isMember) && $isMember==true?"":"disabled")." id='txt_chat' style='height:70px; width: 100%; resize: none; '></textarea><button ".(isset($isMember) && $isMember==true?"":"disabled")." id='btn_sendChat' >Envoyer</button>";
	echo "</div></div>";
	
	// Affiche les stats

	 include_once ("content_stat.php");
	
// Affichage des derniers PV
    echo "<fieldset style='max-height:300px; overflow:auto;'><legend><div id='mask1'></div>Dernières réunions<div id='mask2'></div></legend>";

	
	$meetings=$this->_circle->getMeetings(0,true);
	$count=0;
	$oldmonth="";
	$month_name=array("","Janvier","Février","Mars","Avril","Mai","Juin","Juillet","Août","Septembre","Octobre","Novembre","Décembre");
	foreach ($meetings as $meeting) {
		$count++;
		if ($count==5) {
			echo "<div id='meet_hidden' style='display:none'>";
		}
		if ($oldmonth=="" || $oldmonth!=$meeting->getDate()->format("mY")) {
			$oldmonth=$meeting->getDate()->format("mY");
			echo "<div style='border-bottom:1px solid #aaaaaa; color:#aaaaaa; font-size:130%'>".$month_name[intval($meeting->getDate()->format("m"))]." ".$meeting->getDate()->format("Y")."</div>";
		}
		echo "<div style='clear:both'><a href='meeting.php?id=".$meeting->getId()."'>".$meeting->getDate()->format("d.m")." - réunion de ".$meeting->getMeetingType()."</a><a style='float:right' targwt='new' href='pdf/meeting.php?id=".$meeting->getId()."&code=".md5($meeting->getOpeningTime()->format("dmY").$meeting->getClosingTime()->format("dmY"))."'><img src='/images/icon_download_pdf.png'></a><br/>";
		echo intval(($meeting->getClosingTime()->getTimeStamp()-$meeting->getOpeningTime()->getTimeStamp())/60)." minutes - ".count($meeting->getTensions())." tension(s) - ".count($meeting->getHistory())." décision(s)</div>";
		
	}
	if ($count>4) {
		echo "</div><a  style='text-align:right; display: block;' id='meet_hidden_button' href='#' >Afficher ".($count-4)." réunions de plus...</a>";
	}
	echo "</fieldset>";
   echo "<button style='padding:5px;' class='dialogPage ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only' href='/class/widget/wg_circlebrowser/onglet_historique.php?id=".$this->_circle->getId()."'><span class='omo-history'>Voir tout l'historique</span></button>";

?>
	<script>
		$("#meet_hidden_button").click(function() {
			$(this).hide();
			$("#meet_hidden").show();
			return false;
		});
			function refreshChat(circle) {
				$("#chat").load("/class/widget/wg_circlebrowser/refresh.php?refresh=chat&circle="+circle, function() {$("#chat").scrollTop($("#chat")[0].scrollHeight);});
			}
			refreshChat(<?=$this->_circle->getId()?>)
			// Envoie une ligne de CHAT
			$("#btn_sendChat").button().click(function( event ) {
		        event.preventDefault();
		        $.post( 'ajax/circle.php', { action: 'sendChat', id: '<?=$this->_circle->getId()?>', txt: $("#txt_chat").val() } ,function( data ) {
  
});
		        refreshChat(<?=$this->_circle->getId()?>);
		        $("#txt_chat").val("");
		    });
	</script>
