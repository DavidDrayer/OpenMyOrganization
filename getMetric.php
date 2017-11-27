<?

	if (isset($_GET["id"])) {
		include_once("include.php");
		// Sélectionne la valeur de l'indicateur dans la base de donnée
		$metric=$_SESSION["currentManager"]->loadMetric($_GET["id"]);
		

		echo "document.write('".$metric->getValue()->getValue()."');";

	} else {
		echo "document.write('Aucun indicateur sélectionné.');";
	}
?>
