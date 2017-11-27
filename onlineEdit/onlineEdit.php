<?

	function writeDate () {
		if (isset($GLOBALS['dernierModif']))
			echo $GLOBALS['dernierModif'];
		else
			echo '<i>inconnu</i>';
	}
	function writeTexte ($dbh, $key) {
		print (getTexte($dbh, $key));
	}
	
	// Récupère un texte dans la base de donnée pour l'afficher
	function getTexte ($dbh, $key, $defaultText='null') {
		if ($defaultText == 'null') {
			$defaultText="<div class='noContent'>&nbsp;</div>";
		}
		$query = "select *, DATE_FORMAT(zone_dateModification,'%d.%m.%Y %H:%i') as dateFormatee from t_zone where zone_id='".$key."' and lang_id=".$_SESSION["langue"];
		$result = mysql_query($query, $dbh);
		if ($result>0 && mysql_num_rows($result)==0) {
			$query = "select *, DATE_FORMAT(zone_dateModification,'%d.%m.%Y %H:%i') as dateFormatee from t_zone where zone_id='".$key."'";
			$result = mysql_query($query, $dbh);
		}
		if ($result>0 && mysql_num_rows($result)>0) {
			$tmpTxt = mysql_result($result,0,"zone_contenu");
			if ((mysql_result($result,0,"zone_dateModification")!=null) && ((!isset($GLOBALS['dernierModif'])) or (mysql_result($result,0,"zone_dateModification")>$GLOBALS['dernierModifNF'])))  {$GLOBALS['dernierModif']=mysql_result($result,0,"dateFormatee"); $GLOBALS['dernierModifNF']=mysql_result($result,0,"zone_dateModification");}
			if (trim(mysql_result($result,0,"zone_contenu"))!="") {

				$tmpTxt=str_replace('http://'.$_SERVER['HTTP_HOST'].'/','',$tmpTxt);
				$tmpTxt=str_replace('http://localhost/','',$tmpTxt);
				$tmpTxt=str_replace('content.php','index.php',$tmpTxt);
				$tmpTxt=str_replace('','',$tmpTxt);

				$tmpTxt=str_replace('class=noContent','',$tmpTxt);
				$tmpTxt=preg_replace("/([ldLD])\?([a-zA-Zéàè])/","\\1'\\2",$tmpTxt);
				return $tmpTxt;
			} else {
				return $defaultText;
			}
		} else {
			return $defaultText;
		}
	}
	
	function writeZone ($dbh, $no, $class, $editable, $defaultText='null') {
	
		// 28.11.2005 : Chargement extrèmement plus rapide des modules
		$txt=getZone($dbh, $no, $class, $editable, $defaultText);
		print $txt;

	}
	
	function convertZone ($txt, $dbh) {
			// Conversion des URLs internes
$patterns = array();
$patterns[0] = '/<br(\/)?>/';
$patterns[1] = '/<(\/)?p>/';
$patterns[2] = '/<(\/)?div>/';
$patterns[3] = '/\&nbsp;/';

$isEmpty=trim(preg_replace($patterns, "", $txt));


		if ($isEmpty=='') {
				return "<div class='noContent'>&nbsp;</div>";
				exit;
		}
		$pos=-1;
		$oldPos=0;
		$tmpTxt="";
		for ($pos=strpos($txt,"href=\"index.php?page=",$pos+1); $pos!==false; $pos=strpos($txt,"href=\"index.php?page=",$pos+1)) {

			$pos2=strpos($txt,"\">",$pos+1);
			$tmpTxt.=substr($txt,$oldPos,$pos-$oldPos);
			$oldPos=$pos2+2;
			
			// Récupère le No de page
			preg_match ('/page=([0-9]*)/i',substr($txt,$pos,$pos2+2-$pos),$param);
			// Récupère le shortcut dans la base de donnée
			$query="select * from t_page where page_id=".$param[1];
			$result=mysql_query ($query, $dbh);
			
			if ($result>0 && mysql_num_rows($result)>0 && mysql_result($result,0,"page_shortcut")!="") {
				$tmpTxt.="href=\"/".mysql_result($result,0,"page_shortcut")."\">";
				// Affiche l'alternative
			} else {
				// Sinon affiche le lien standard
				$tmpTxt.=substr($txt,$pos,$pos2+2-$pos);
			}
		}
		$tmpTxt.=substr($txt,$oldPos);
		$txt=$tmpTxt;
		$tmpTxt="";
		// Conversion des modules
		$pos=-1;
		$oldPos=0;
		for ($pos=strpos($txt,"<img class=\"module\"",$pos+1); $pos!==false; $pos=strpos($txt,"<img class=\"module\"",$pos+1)) {
			// Fin de la chaine
			$pos2=strpos($txt,"unselectable=\"on\">",$pos+1);
			$tmpTxt.=substr($txt,$oldPos,$pos-$oldPos);
			$oldPos=$pos2+18;
			
			// debug : echo "Module de ".$pos." à ".$pos2." : ".substr($txt,$pos,$pos2+18-$pos);

			// Récupère le nom du module dans la partie 2
			preg_match ('/module.php\?name=([a-z]*)/i',substr($txt,$pos,$pos2+18-$pos),$param);
			if (!isset($GLOBALS[$param[1].'INCLUDED'])) {
				@include ('modules/'. $param[1].'.php');
					if (!function_exists($param[1].'_Print')) {
						@include ('../modules/'. $param[1].'.php');
					}
				
				}
				

				
					if (function_exists($param[1].'_Print')) {
					// Calcul les paramètres
						// récupère la querystring
						preg_match ('/module.php\?([^ ]*)"/',substr($txt,$pos,$pos2+18-$pos),$allParam);
						// divise sur les symbole "&"
						$allParam = explode("&amp;", $allParam[1]);
						foreach($allParam as $key => $val) {
							// divise le nom de la valeur
							preg_match ('/(.*)=(.*)/i', $val, $valValue);
							$GLOBALS[$valValue[1]]=$valValue[2];
						}
					ob_start();
					eval ( $param[1].'_Print();');
					$txtModule=ob_get_contents();
					ob_end_clean ();
					$tmpTxt.=$txtModule;
					
					//Evaluation des mots-clés et autre après l'impression du module
					if (function_exists($param[1].'_GetOpenCommand')) {
						eval ('$GLOBALS["value"]='.$param[1].'_GetOpenCommand();');
						if (isset($GLOBALS["value"]) && $GLOBALS["value"]!=NULL) $GLOBALS["startScript"].=$GLOBALS["value"];
					}
					if (function_exists($param[1].'_GetMetaDescription')) {
						eval ('$GLOBALS["value"]='.$param[1].'_GetMetaDescription();');
						if (isset($GLOBALS["value"]) && $GLOBALS["value"]!=NULL) $GLOBALS["description"]=$GLOBALS["value"];
					}
					if (function_exists($param[1].'_GetKeywords')) {
						eval ('$GLOBALS["value"]='.$param[1].'_GetKeywords();');
						if (isset($GLOBALS["value"]) && $GLOBALS["value"]!=NULL) {
							if (isset($GLOBALS["motCle"])) {
								$GLOBALS["motCle"]=$GLOBALS["value"].",".$GLOBALS["motCle"];
							} else {
								$GLOBALS["motCle"]=$GLOBALS["value"];
							}
						}
					}
					
				} else {
					$tmpTxt .= "<p style=\"color:red; font-weight:bold;\">Le module n'a pas pu être inclus</p>";
					$tmpTxt .= "<p style=\"color:red; font-weight:bold;\">".'modules/'. $param[1].'.php'."</p>";
				}
		}
		$tmpTxt.=substr($txt,$oldPos);
		return $tmpTxt;
	}
	
	function getZone ($dbh, $no, $class, $editable, $defaultText='null') {
		$tmpText = getTexte($dbh, $no, $defaultText);
		
		$tmpText = convertZone($tmpText, $dbh);
		
		// Contrôle s'il y a des modules (type de div)

		if ($editable==true) {
			$tmp= "<div id=\"EDIT_div".$no."\" class=\"".$class."\">";
		} else {
			$tmp= "<div id=\"STATIC_div".$no."\" class=\"".$class."\">";
		}
		$tmp=$tmp.$tmpText;
		$tmp= $tmp."</div>";
		
		return $tmp;
	}
?>
