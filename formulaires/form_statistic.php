<?
	// Inclus les éléments partagés entre plusieurs pages: base de donnée, instanciation de classe, etc...
	include_once("../include.php");

	if (isset($_POST["action"])) {
	
		if ($_POST["action"]=="addMetric") {
			// Vérifie le formulaire
			if ($_POST["meva_value"]=="") {
				echo "/* Erreur */\n alert('Erreur!! La valeur n\'est pas remplie.');$('#meva_value').focus();"; exit;
			}
		
		}
		
		// Efface une éventuelle valeur datée du même jour il y a moins d'une heure
		$query="delete from t_metric_value where metr_id='".$_POST["metr_id"]."' and meva_date>= DATE_SUB(NOW(), INTERVAL 1 HOUR)";
		mysql_query($query, $dbh);
		// Ajoute la valeur
		$query="insert into t_metric_value (metr_id, meva_value) values ('".$_POST["metr_id"]."','".$_POST["meva_value"]."')";
		mysql_query($query, $dbh);
		// Force le rechargement du formulaire
		echo "<script>";
		if (isset($_POST["circ_id"]) && $_POST["circ_id"]!="") echo "refreshMetrics(".$_POST["circ_id"].");";
		if (isset($_POST["role_id"]) && $_POST["role_id"]!="") echo "refreshMetrics(".$_POST["role_id"].");";
		echo "$('#dialogStdContent').load('/formulaires/form_statistic.php?id=".$_POST["metr_id"]."');";
		echo "</script>";
		exit;
		
	}

	header('Content-type: text/html; charset=ISO-8859-1');

	// Lit les infos sur le metric
	$metric=$_SESSION["currentManager"]->loadMetric($_GET["id"]);
	// Lit les valeurs du metric
	$metric_values=$metric->getValues();

	//$query="select * from t_metric_value where metr_id=".$_GET["id"]." order by meva_date";
	//$result=mysql_query($query,$dbh);

	//$query="select * from t_metric where metr_id=".$_GET["id"];
	//$result2=mysql_query($query,$dbh);

	// Est-ce l'utilisateur en charge de cet indicateur?
	if (isset($_SESSION["currentUser"]) && ((!is_null($metric->getRole()) && $_SESSION["currentUser"]->getId()==$metric->getRole()->getUserId()) || $_SESSION["currentUser"]->isRole(\holacracy\Role::LEAD_LINK_ROLE | \holacracy\Role::SECRETARY_ROLE,$metric->getCircle()))) {

	// Affiche une boîte de saisie
	//if (count($metric_values)==0 || end($metric_values)->getDate()->format("Y, m, d")!=date("Y, m, d")) {

?>
<form id='formulaire'>
	Saisir une nouvelle valeur: <input type='text' name='meva_value' id='meva_value'> 
	<input type='hidden' name='metr_id' id='metr_id' value='<?=$_GET["id"]?>'>
	<input type='hidden' name='circ_id' id='circ_id' value='<?=(isset($_GET["circ_id"])?$_GET["circ_id"]:"")?>'>
	<input type='hidden' name='role_id' id='role_id' value='<?=(isset($_GET["role_id"])?$_GET["role_id"]:"")?>'>
	<input id="btn_new_metric" type='button' value='Ajouter'>
	<input type='hidden' id='form_target' value='/formulaires/form_statistic.php'>
	<input type='hidden' name='action' value='addMetric'>

</form>
<?
	//} else {
		// Affiche comme quoi il faut attendre demain pour entrer une nouvelle valeur
		//echo "Saisir une nouvelle valeur: <input type='text' disabled='yes' name='meva_value' id='meva_value'> <input disabled='yes' type='button' value='Ajouter'>";
	
	if (!(count($metric_values)==0 || end($metric_values)->getDate()->format("Y, m, d")!=date("Y, m, d"))) {
		echo "<span class='omo-info'> Une valeur a déjà été entrée aujourd'hui</span>";
	}
	//}
}
	if (count($metric_values)>1) {
?>
		<!-- Script google pour les graphiques -->
		<script type="text/javascript" src="https://www.google.com/jsapi"></script>

<script type="text/javascript">
google.load('visualization', '1', { packages : ['controls'], callback: createTable });
google.setOnLoadCallback(createTable);

function createTable() {
  // Create the dataset (DataTable)

 <?
	// Lit les infos sur les autres metrics à ajouter
	$ref_metrics=$metric->getReferences();
	$tab[0]=$metric_values;
	$tab_pointer[0]=0;
	$nbcol=count($ref_metrics)+1;

	
	echo "var myData = new google.visualization.DataTable();\n";
	echo "myData.addColumn('date', 'Date');\n";
	echo "myData.addColumn('number', '".str_replace("'","\'",$metric->getName())."');\n";
	$add=1;

	// Y a-t-il un objectif numérique à ce metrics?
	if ($metric->getGoal()>0) {
		echo "myData.addColumn('number', 'Objectif');\n";
		$add=2;
		// Ajoute les valeurs, toutes à la hauteur du goal
		$count=0;
		foreach ($tab[0] as $cell) {
			// Crée une metric value
			$tab[1][$count]=new \holacracy\MetricValue();
			$tab[1][$count]->setDate($cell->getDate());
			$tab[1][$count]->setValue($metric->getGoal());
			$count++;
		}
	}
	
	for ($i=0; $i<count($ref_metrics);$i++) {
		$tab[$i+$add]=$ref_metrics[$i]->getValues();
		$tab_pointer[$i+$add]=0;
		// Ajoute les colonnes supplémentaires si nécessaire
		echo "myData.addColumn('number', '".$ref_metrics[$i]->getName()."');\n";
	}

	

 	echo "myData.addRows([";
 
	// Affiche chaque tableau
	for ($i=0; $i<count($tab);$i++) {
		// Et bien sûr chaque ligne
		for ($j=0; $j<count($tab[$i]);$j++) {
			if ($j>0 || $i>0) echo ",\n";
			echo "[new Date(".$tab[$i][$j]->getDate()->format("Y, m-1, d").")";
			for ($k=0; $k<$i; $k++) {
				echo ",null";
			}
			echo ",".$tab[$i][$j]->getValue();
			for ($k=$i+1; $k<count($tab); $k++) {
				echo ",null";
			}

			echo "]";

		}
	}
 
 /*
  	while ($tab_pointer[0]<=count($tab[0])-1) {
		// Récupère la plus petite valeur de la liste des pointeurs
		$mindate=$tab[0][$tab_pointer[0]]->getDate();
		

		for ($i=1; $i<count($tab);$i++) {
			if ($tab_pointer[$i]<=count($tab[$i])-1 && $mindate>$tab[$i][$tab_pointer[$i]]->getDate()) {
				$mindate=$tab[$i][$tab_pointer[$i]]->getDate();
			}
		}
		// Reparcours la liste, en ajoutant les valeurs si correspondant, et en incrémentant les pointeurs
		echo "[new Date(".$mindate->format("Y, m, d").")";

		for ($i=0; $i<count($tab);$i++) {
			// La date est identique, ajoute la valeur
			if ($tab_pointer[$i]<=count($tab[$i])-1 && $tab[$i][$tab_pointer[$i]]->getDate()==$mindate) {
				echo ",".$tab[$i][$tab_pointer[$i]]->getValue();
				// Et incrément le pointeur
				$tab_pointer[$i]++;
			} else {
				echo ",null";
			}
		}

		echo "],";
	}
	*/
	// Ajoute les lignes issues de l'historique	
	//for ($i=0; $i<count($metric_values);$i++) {
		//echo "[new Date(".$metric_values[$i]->getDate()->format("Y, m, d")."),".$metric_values[$i]->getValue()."],";
	//}
	
?>
  ]);

  // Create a dashboard.
  var dash_container = document.getElementById('dashboard_div'),
    myDashboard = new google.visualization.Dashboard(dash_container);

  // Create a date range slider
  var myDateSlider = new google.visualization.ControlWrapper({
   'controlType': 'ChartRangeFilter',
    'containerId': 'control_div',

    'options': {
       'filterColumnLabel': 'Date',
        'ui':    {
			'chartOptions': {
				'vAxis': {'minValue': 0},
				'colors': ['#ff0000', '#6666ff', '#9999ff', '#ccccff']
				
			}
		}
    },
    // Initial range: 2012-02-09 to 2012-03-20.
    'state': {'range': {'start': new Date(<?= date("Y, m-1, d",strtotime('-'.(10*7).' day'))?>), 'end': new Date(<?=date("Y, m, d")?>)}}

  });


  // Line chart visualization
  var myLine = new google.visualization.ChartWrapper({
	  'interpolateNulls' :true, 
    'chartType' : 'LineChart',
    'containerId' : 'line_div',
    'options': {
		'vAxis': {'minValue': 0},

		'colors': ['#ff0000', '#6666ff', '#9999ff', '#ccccff'],
		'pointSize':0,
		'series': {
                0: { pointSize: 2 }
            },

		'legend': { 'position': 'top' },
		'trendlines': {
          '0': {'type': 'exponential', 'color': '#666', 'opacity': '1'}
        }
	}
  });
  
  // Bind myLine to the dashboard, and to the controls
  // this will make sure our line chart is update when our date changes
  myDashboard.bind(myDateSlider, myLine );

  myDashboard.draw(myData);
}
</script>

<div id="dashboard_div">
 <div id="line_div" style='height:300px;'><!-- Line chart renders here --></div>
  <div id="control_div"  style='height:50px;'><!-- Controls renders here --></div>

  <div id="table_div" style='display:none'><!-- Table renders here --></div>
</div>
<?
} else {
	// Informe qu'il n'y a aucune donnée pour dessiner le graphique
}
?>
<script>
$(function() {

	$("#btn_new_metric").button().click(function() {$( "#formulaire").submit()});
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
		                if ($("#formulaire").find("script")) {
							eval($("#formulaire").find("script").text());
						}
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
	
  });
</script>






