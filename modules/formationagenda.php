<?
	$GLOBALS['formationagendaINCLUDED']=true;

function formationagenda_GetDescription() {
	Return "Affiche l'agenda des formations";
}
function formationagenda_GetKeywords() {
	Return "agenda, formation";
}
function formationagenda_GetTitle() {
	Return "Agenda des formations";
}
function formationagenda_GetCredit() {
	Return "Ecrit par David Dräyer";
}
	
	function formationagenda_getParams() {
		$x=array(array("nom"=>"couleur_fond","label"=>"Couleur du fond","type"=>"string"),
				 array("nom"=>"couleur_texte","label"=>"Couleur du texte","type"=>"string")
				 
				 );
		return $x;
	}
		
function formationagenda_Print() {
?>

<style>
.fixed-dialog{
  position: fixed;
}
	</style>
 <script>
	 var idformation;
$(function() {

	formationagenda_dialog=$( "#dialog-message" ).dialog({
		dialogClass: 'fixed-dialog',
        resizable: false,
        autoOpen: false,
        height:480,
        width:600,
		modal: true,
		buttons: {
			"Valider l'inscription": function() {
				formationagenda_submitForm();
				//$( this ).dialog( "close" );
			}
		},
		open: function(event, ui) {
			$('#divInDialog').html("Loading...");
			$.ajaxSetup({
				contentType: 'application/x-www-form-urlencoded; charset=ISO-8859-1',
				  beforeSend: function(jqXHR) {
					jqXHR.overrideMimeType('application/x-www-form-urlencoded; charset=ISO-8859-1');
				  }
			});
			
			$.ajax({
				type: "get",
				url: "/modules/formation/form_inscription.php?id="+idformation,
				success: function(data){
					   $('#divInDialog').html(data);
				},
				error: function(){
				  alert("An error occured");
				}
			  });
			
			
			//$('#divInDialog').load('/modules/formation/form_inscription.php?id='+idformation, function() {
			//});
		}
	});
	 $( ".formationagenda_btn_inscription" ).button().on( "click", function() {
		
		idformation=$(this).attr("idformation");
		formationagenda_dialog.dialog( "open" );
	});
});
</script>
<script>
function formationagenda_submitForm()
{ 
	//$('#ml_submit').attr('disabled', 'disabled');
	//$('#ml_submit').blur()
	var masque= document.getElementById("masque_fa");
	var masque2= document.getElementById("masque_fa2");
	var div_ml= document.getElementById("divInDialog");
	masque2.innerHTML="<table width='100%' height='100%'><tr><td style='vertical-align:middle'><div style='font-weight:bold; color:<?=urldecode($GLOBALS["couleur_texte"])?>;'>Contact avec le serveur...</div></td></tr></table>";
    masque.style.display="";
	var xhr; 
    try {  xhr = new ActiveXObject('Msxml2.XMLHTTP');   }
    catch (e) 
    {
        try {   xhr = new ActiveXObject('Microsoft.XMLHTTP'); }
        catch (e2) 
        {
           try {  xhr = new XMLHttpRequest();  }
           catch (e3) {  xhr = false;   }
         }
    }
 
    xhr.onreadystatechange  = function() 
    { 
       if(xhr.readyState  == 4)
       {
        if(xhr.status  == 200) {
            document.getElementById("divInDialog").innerHTML=xhr.responseText; 
            document.getElementById("masque_fa").style.display="none";
        } else
        if(xhr.status  == 401) {
            document.getElementById("masque_fa2").innerHTML="<table width='100%' height='100%'><tr><td style='vertical-align:middle; color:<?=urldecode($GLOBALS["couleur_texte"])?>'>" + xhr.responseText + "<p><input  type='button' value='OK' style='width:100px' onclick='document.getElementById(\"masque_fa\").style.display=\"none\"; $(\"#ml_submit\").removeAttr(\"disabled\");'>" + "</td></tr></table>"; 
        }
        else 
            document.getElementById("masque_fa2").innerHTML="<table width='100%' height='100%'><tr><td style='vertical-align:middle; color:<?=urldecode($GLOBALS["couleur_texte"])?>'>" + "Error code " + xhr.status + "<p><input  type='button'  value='OK' style='width:100px' onclick='document.getElementById(\"masque_fa\").style.display=\"none\"; $(\"#ml_submit\").removeAttr(\"disabled\");'>" + "</td></tr></table>";
        }
    }; 

 xhr.open("POST", "/modules/formation/form_inscription.php", true);
 xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded"); 
 var data = "fa_mail=" +  encodeURIComponent(document.fa_formulaire.fa_mail.value);
 data += "&fa_id=" + encodeURIComponent(document.fa_formulaire.fa_id.value);
 data += "&fa_nom=" + encodeURIComponent(document.fa_formulaire.fa_nom.value);
 data += "&fa_prenom=" + encodeURIComponent(document.fa_formulaire.fa_prenom.value);
 data += "&fa_adresse=" + encodeURIComponent(document.fa_formulaire.fa_adresse.value);
 data += "&fa_npa=" + encodeURIComponent(document.fa_formulaire.fa_npa.value);
 data += "&fa_ville=" + encodeURIComponent(document.fa_formulaire.fa_ville.value);
 data += "&fa_pays=" + encodeURIComponent(document.fa_formulaire.fa_pays.value);
 data += "&fa_ml=" + (document.fa_formulaire.fa_ml.checked?1:0);
 data += "&fa_cg=" + (document.fa_formulaire.fa_cg.checked?1:0);
 xhr.send(data); 
} 
</script>
<div id="dialog-message" title="Inscription à la formation">
		<div style="position:absolute; z-index:10;  top:0; left:0; right:0; bottom:0;  display:none" id="masque_fa">
		<div style="position:absolute;  opacity:0.7;   filter:alpha(opacity=70); top:0; left:0; right:0; bottom:0; background:<?=urldecode($GLOBALS["couleur_fond"])?> ; " id="masque_fa1"></div>
		<div style="position:absolute; padding:15px; font-weight:bold;  top:0; left:0; right:0; bottom:0; text-align:center; " id="masque_fa2"></div>
	</div>
	<div id="divInDialog"></div>
</div>
<?

	$dbh =  connectDb(); 
	$query="select * from t_date_formation left join t_formation on (t_date_formation.form_id=t_formation.form_id) where t_date_formation.dafo_date>now() order by t_date_formation.dafo_date";
	$result=mysql_query($query, $dbh);
	if ($result>0) {
		if (mysql_num_rows($result)>0) {
			for ($i=0; $i<mysql_num_rows($result); $i++) {
				echo "<div class='formationagenda_formation'><h1>".mysql_result($result, $i, "form_titre")."</h1>";
				$oDate = new DateTime(mysql_result($result, $i, "dafo_date"));
				$wd=array("Dimanche","Lundi","Mardi","Mercredi","Jeudi","Vendredi","Samedi");
				echo "<h2>".$wd[$oDate->format("w")]." ".$oDate->format("d.m.Y à H:i")." - ".mysql_result($result, $i, "dafo_ville").(mysql_result($result, $i, "dafo_adresse")!=""?" (".mysql_result($result, $i, "dafo_adresse").")":"")."</h2>";
				//echo "<div>".mysql_result($result, $i, "form_description")."</div>";
				echo "<div>Durée : ".(mysql_result($result, $i, "dafo_duree")!=""?mysql_result($result, $i, "dafo_duree"):mysql_result($result, $i, "form_duree"))." | Tarif : ".(mysql_result($result, $i, "dafo_prix")!=""?mysql_result($result, $i, "dafo_prix"):mysql_result($result, $i, "form_prix")).".- CHF ";
				if (mysql_result($result, $i, "dafo_fichier")!="") {
					echo "| <a href='".mysql_result($result, $i, "dafo_fichier")."' target='_blank'>Informations (PDF)</a> | <input class='formationagenda_btn_inscription' type='button' value='Inscription' idformation='".mysql_result($result, $i, "dafo_id")."'/></div>";
				} else {
					echo "| informations à venir...</div>";
				}
				echo "</div>";
			}
		} else {
			echo "Aucune formation dans l'agenda";
		}
	} else {
		echo "Erreur dans la requête SQL. Les tables t_date_formation et t_formation existent-elles bien?";
		echo "<br><br>".$query;
	}
	
	}
?>
