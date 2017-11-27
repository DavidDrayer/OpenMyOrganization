<?
	include_once("../include.php");
	
	function remove_accent ($str) {
		$strMatch = array('/[âäàåãáÂÄÀÅÃÁæÆ]/','/[ß]/','/[çÇ]/','/[Ð]/','/[éèêëÉÊËÈ]/','/[ïîìíÏÎÌÍ]/','/[ñÑ]/','/[öôóòõÓÔÖÒÕ]/','/[_]/','/[ùûüúÜÛÙÚ]/','/[¥_Ý_ýÿ]/','/[_]/');
		$strReplace = array('a', 'b', 'c', 'd', 'e', 'i', 'n', 'o', 's', 'u', 'y', 'z');

		return preg_replace($strMatch, $strReplace, $str);
	}

	echo "<table style='width:100%'><tr><td style='width:100%'><input name='search_field_dialog' id='search_field_dialog' style='width:100%; padding:5px; ' value=\"".(isset($_GET["querystring"])?htmlentities(utf8_decode($_GET["querystring"])):"")."\"/></td>";
	echo "<td style='min-width:100px;'><button style='width:100%'id='search_button_dialog' href='/formulaires/form_search.php?querystring=[search_field_dialog]&context=[search_context]' class='dialogPage' alt='Lancer la recherche'>Rechercher</button></td></tr></table>";
	if (isset($_GET["querystring"]) && strlen(trim(utf8_decode($_GET["querystring"])))>0) {
	if (strlen(trim(utf8_decode($_GET["querystring"])))>2) {
	
	echo "<h3>Résultat de la recherche pour <b><i>".utf8_decode($_GET["querystring"])."</i></b></h3>";
	$filter=new \holacracy\Filter();
	$nbcar=25;
	$filter->addCriteria("keyword",utf8_decode($_GET["querystring"]));
	$filter->addCriteria("organisation",utf8_decode($_GET["context"]));
	$search= $_SESSION["currentManager"]->findAll($filter);
	$currentRole=NULL;
	if (count($search)>0) {
		echo "<table style='width:100%'>";
		$pattern = '/.{0,'.$nbcar.'}'.remove_accent(utf8_decode($_GET["querystring"])).'.{0,'.$nbcar.'}/i';
		$qs=remove_accent(utf8_decode($_GET["querystring"]));
		foreach ($search as $obj) {

			switch (get_class($obj)) {
				case "holacracy\Circle" :
				case "holacracy\Role" :
					//$pattern = '/.{0,'.$nbcar.'}'.utf8_decode($_GET["querystring"]).'.{0,'.$nbcar.'}/i';
					$str="";
					if (preg_match($pattern, remove_accent($obj->getName()), $matches)) {
						$str.= "<div><b>Nom :</b> ".(strpos( $matches[0],$qs)==$nbcar?"[...]":"").preg_replace( '/('.$qs.')/i', "<span class='omo-highlight'>$1</span>", $matches[0]).(strpos( $matches[0],$qs)==strlen($matches[0])-strlen($qs)-$nbcar?"[...]":"")."</div>";
					}
					if (preg_match($pattern, remove_accent($obj->getPurpose()), $matches)) {
						$str.= "<div><b>Raison d'être :</b> ".(strpos( $matches[0],$qs)==$nbcar?"[...]":"").preg_replace( '/('.$qs.')/i', "<span class='omo-highlight'>$1</span>", $matches[0]).(strpos( $matches[0],$qs)==strlen($matches[0])-strlen($qs)-$nbcar?"[...]":"")."</div>";
					}
					if (get_class($obj)=="holacracy\Circle" && preg_match($pattern, remove_accent(strip_tags($obj->getStrategy())), $matches)) {
						$str.= "<div><b>Stratégie :</b> ".(strpos( $matches[0],$qs)==$nbcar?"[...]":"").preg_replace( '/('.$qs.')/i', "<span class='omo-highlight'>$1</span>", $matches[0]).(strpos( $matches[0],$qs)==strlen($matches[0])-strlen($qs)-$nbcar?"[...]":"")."</div>";
					}

					$id=$obj;
				break;
				case "holacracy\Accountability" :
					$str= "<b>Redevabilité : </b>";
					$subject = remove_accent($obj->getDescription());
					preg_match($pattern, $subject, $matches);
					$str.= (strpos( $matches[0],$qs)==$nbcar?"[...]":"").preg_replace( '/('.$qs.')/i', "<span class='omo-highlight'>$1</span>", $matches[0]).(strpos( $matches[0],$qs)==strlen($matches[0])-strlen($qs)-$nbcar?"[...]":"");
					$id=$obj->getRole();
				break;
				case "holacracy\Scope" :
					$str="";
					if (preg_match($pattern, remove_accent($obj->getDescription()), $matches)) {
						$str.= "<div><b>Domaine :</b> ".(strpos( $matches[0],$qs)==$nbcar?"[...]":"").preg_replace( '/('.$qs.')/i', "<span class='omo-highlight'>$1</span>", $matches[0]).(strpos( $matches[0],$qs)==strlen($matches[0])-strlen($qs)-$nbcar?"[...]":"")."</div>";
					}
					if (preg_match($pattern, remove_accent(strip_tags($obj->getPolitiques())), $matches)) {
						$str.= "<div><b>Politique de domaine :</b> ".(strpos( $matches[0],$qs)==$nbcar?"[...]":"").preg_replace( '/('.$qs.')/i', "<span class='omo-highlight'>$1</span>", $matches[0]).(strpos( $matches[0],$qs)==strlen($matches[0])-strlen($qs)-$nbcar?"[...]":"")."</div>";
					}

					$id=$obj->getRole();
				break;
				case "holacracy\Policy" :
					//$pattern = '/.{0,'.$nbcar.'}'.utf8_decode($_GET["querystring"]).'.{0,'.$nbcar.'}/i';
					$str="";
					if (preg_match($pattern, remove_accent($obj->getTitle()), $matches)) {
						$str.= "<div><b>Politique (titre) :</b> ".(strpos( $matches[0],$qs)==$nbcar?"[...]":"").preg_replace( '/('.$qs.')/i', "<span class='omo-highlight'>$1</span>", $matches[0]).(strpos( $matches[0],$qs)==strlen($matches[0])-strlen($qs)-$nbcar?"[...]":"")."</div>";
					}
					if (preg_match($pattern, remove_accent(strip_tags($obj->getDescription())), $matches)) {
						$str.= "<div><b>Politique (description) :</b> ".(strpos( $matches[0],$qs)==$nbcar?"[...]":"").preg_replace( '/('.$qs.')/i', "<span class='omo-highlight'>$1</span>", $matches[0]).(strpos( $matches[0],$qs)==strlen($matches[0])-strlen($qs)-$nbcar?"[...]":"")."</div>";
					}
					$id=$obj->getCircle();
				break;
				
			
				
			}
				if ($id->getId()!=$currentRole) {
					if (!is_null($currentRole))	echo "</td></tr>";
					echo "<tr class='omo-searchresult-title'><th style='text-align:left'>";
					if (get_class($id)=="holacracy\Circle") {
						echo "<span class='omo-role-32'><a href='circle.php?id=".$id->getId()."'>".$id->getName()."</a></span>";
					} else {
						echo "<span class='omo-role-1'><a  href='role.php?id=".$id->getId()."'>".$id->getName()."</a></span>";
					}
					echo "<div style='font-weight:100; padding-left:50px;'>".$id->getOrganisation()->getName().($id->getSuperCircleId()>0?" &gt; ".$id->getSuperCircle()->getName():"")."</div>";

					echo "</th></tr><tr class='omo-searchresult-content'><td style='padding-left:40px;'>";
					$currentRole=$id->getId();
				}
				echo "<div>".$str."</div>";
		}
		echo "</table>";
	} else {
		// Génère un message d'erreur
		echo "<div>La recherche n'a retourné aucun résultat.</div>";
		
	}
	} else {
		echo "<div>La longueur minimum de la chaîne à chercher est de 3 caractères.</div>";
		}
	}
?>
<script>
		$( "#search_button_dialog" )
      .button({
	  	icons: {primary: "ui-icon-search"}
	  })
</script>
