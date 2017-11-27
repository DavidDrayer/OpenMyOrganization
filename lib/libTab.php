<?
function writeSelectDB($label,$name,$query,$selected="",$required=false,$separateur="",$format="") {
	unset($_POST[$name]);
	$result=mysql_query($query,$GLOBALS["dbh"]);
	echo "<div>";
	if (isset($GLOBALS["displayTab_errResult"][$name])) {
		echo "<div class='form_probleme'>";
	}
	echo "<span class='label'>".$label.":</span> ".$separateur;
	echo "<select  id='$name' name='$name'";
	if ($format!="") {
		if (strpos($format,":")) {
			echo " style='$format'";
		} else {
			echo " class='$format'";
		}	}
	echo ">";
	echo "<option value=''>-- Choisissz --</option>";
	for ($i=0; $i<mysql_num_rows($result); $i++) {
		echo "<option";
		if ($selected==mysql_result($result,$i,0)) echo " selected";
		echo " value='".mysql_result($result,$i,0)."'>".mysql_result($result,$i,1)."</option>";
		
	};
	
	echo "</select>";
	if (isset($GLOBALS["displayTab_errResult"][$name])) {
		echo "</div>";
		echo "<div class='form_problemeTxt'>".$GLOBALS["displayTab_errResult"][$name]."</div>";
		unset ($GLOBALS["displayTab_errResult"][$name]);
	}
	echo "</div>";
}

function writeRange ($label,$name,$value="",$selected="",$required=false,$separateur="",$format="") {
	unset($_POST[$name."1"]);
	unset($_POST[$name."2"]);
	echo "<div>";
	if (isset($GLOBALS["displayTab_errResult"][$name])) {
		echo "<div class='form_probleme'>";
	}
	echo "<span class='label'>".$label.":</span> ".$separateur;
	echo "Min: <select  id='".$name."1' name='".$name."1'";
	if ($format!="") {
		if (strpos($format,":")) {
			echo " style='$format'";
		} else {
			echo " class='$format'";
		}	}
	echo ">";
	for ($i=0; $i<count($value); $i++) {
		echo "<option";
		
		if (count($value[$i])>1) {
			if ($selected[0]==$value[$i][0]) echo " selected";
			echo " value='".$value[$i][0]."'>".$value[$i][1]."</option>";
		} else {
			if ($selected[0]==$value[$i]) echo " selected";
			echo ">".$value[$i]."</option>";
		}
	};
	echo "</select> &nbsp; Max: ";
		echo "<select  id='".$name."2' name='".$name."2'";
	if ($format!="") {
		if (strpos($format,":")) {
			echo " style='$format'";
		} else {
			echo " class='$format'";
		}	}
	echo ">";
	for ($i=0; $i<count($value); $i++) {
		echo "<option";
		
		if (count($value[$i])>1) {
			if ($selected[1]==$value[$i][0]) echo " selected";
			echo " value='".$value[$i][0]."'>".$value[$i][1]."</option>";
		} else {
			if ($selected[1]==$value[$i]) echo " selected";
			echo ">".$value[$i]."</option>";
		}
	};
	echo "</select>  ";
	if (isset($GLOBALS["displayTab_errResult"][$name])) {
		echo "</div>";
		echo "<div class='form_problemeTxt'>".$GLOBALS["displayTab_errResult"][$name]."</div>";
		unset ($GLOBALS["displayTab_errResult"][$name]);
	}
	echo "</div>";

}
function writeSelect($label,$name,$value="",$selected="",$required=false,$separateur="",$format="") {
	unset($_POST[$name]);
	echo "<div>";
	if (isset($GLOBALS["displayTab_errResult"][$name])) {
		echo "<div class='form_probleme'>";
	}
	echo "<span class='label'>".$label.":</span> ".$separateur;
	echo "<select  id='$name' name='$name'";
	if ($format!="") {
		if (strpos($format,":")) {
			echo " style='$format'";
		} else {
			echo " class='$format'";
		}	}
	echo ">";
	for ($i=0; $i<count($value); $i++) {
		echo "<option";
		
		if (count($value[$i])>1) {
			if ($selected==$value[$i][0]) echo " selected";
			echo " value='".$value[$i][0]."'>".$value[$i][1]."</option>";
		} else {
			if ($selected==$value[$i]) echo " selected";
			echo ">".$value[$i]."</option>";
		}
	};
	
	echo "</select>";
	if (isset($GLOBALS["displayTab_errResult"][$name])) {
		echo "</div>";
		echo "<div class='form_problemeTxt'>".$GLOBALS["displayTab_errResult"][$name]."</div>";
		unset ($GLOBALS["displayTab_errResult"][$name]);
	}
	echo "</div>";

}
function writeTextArea($label,$name,$value="",$required=false,$separateur="",$format="") {
	unset($_POST[$name]);
	echo "<div>";
	if (isset($GLOBALS["displayTab_errResult"][$name])) {
		echo "<div class='form_probleme'>";
	}
	echo "<span class='label'>".$label.":</span> ".$separateur;
	echo "<textarea name='$name' id='$name'";
	if ($format!="") {
		if (strpos($format,":")) {
			echo " style='$format'";
		} else {
			echo " class='$format'";
		}	}
	echo ">$value</textarea>";
	if (isset($GLOBALS["displayTab_errResult"][$name])) {
		echo "</div>";
		echo "<div class='form_problemeTxt'>".$GLOBALS["displayTab_errResult"][$name]."</div>";
		unset ($GLOBALS["displayTab_errResult"][$name]);
	}	
	echo "</div>";
}
function writeText($label,$name,$value="",$required=false,$separateur="",$format="") {
	unset($_POST[$name]);
	echo "<div>";
	if (isset($GLOBALS["displayTab_errResult"][$name])) {
		echo "<div class='form_probleme'>";
	}
	echo "<span class='label'>".$label.":</span> ".$separateur;
	echo "<input type='text' value=\"$value\" name='$name' id='$name'";
	if ($format!="") {
		if (strpos($format,":")) {
			echo " style='$format'";
		} else {
			echo " class='$format'";
		}
	}
	echo ">";
	if (isset($GLOBALS["displayTab_errResult"][$name])) {
		echo "</div>";
		echo "<div class='form_problemeTxt'>".$GLOBALS["displayTab_errResult"][$name]."</div>";
		unset ($GLOBALS["displayTab_errResult"][$name]);
	}
	echo "</div>";
}

function writeCalendar($label,$name,$value="",$required=false,$separateur="",$format="") {
	unset($_POST[$name]);
	echo "<div>";
	if (isset($GLOBALS["displayTab_errResult"][$name])) {
		echo "<div class='form_probleme'>";
	}
	echo "<span class='label'>".$label.":</span> ".$separateur;
	echo "<input type='text' value=\"$value\" name='$name' id='$name'";
	if ($format!="") {
		if (strpos($format,":")) {
			echo " style='$format'";
		} else {
			echo " class='$format'";
		}
	}
	echo ">";
	if (isset($GLOBALS["displayTab_errResult"][$name])) {
		echo "</div>";
		echo "<div class='form_problemeTxt'>".$GLOBALS["displayTab_errResult"][$name]."</div>";
		unset ($GLOBALS["displayTab_errResult"][$name]);
	}
	echo "</div>";
}

function writeHidden($name, $value="") {
	unset($_POST[$name]);
	echo "<input type='hidden' value='$value' name='$name' id='$name'>";

}

function displayTab ($etapeArray, $label, $autoSave=false, $navBack=0, $saveAllValues=false) {

// Faut-il sauver
if (isset($_POST["oldEtape"]) && ($autoSave || $_POST["btn_enregistrer"]!=$label[2])) {
	// Oui. Y a-t-il une fonction
	if (count($etapeArray[$_POST["oldEtape"]])>3) {
		eval("\$GLOBALS[\"displayTab_errResult\"]=".$etapeArray[$_POST["oldEtape"]][3].";");
	}
	
}

echo ("<script>\n");
echo ("		function commentaire_changeForm(e) {\n");
if (!$autoSave) echo ("			document.commentaire_formulaire.btn_enregistrer.value='Enregistrer';\n");
echo ("		}\n");
echo ("	</script>\n");
			if (isset($_POST["max"]) && $_POST["max"]!="") {
				$max=$_POST["max"];
			}
			if (isset($GLOBALS["displayTab_errResult"]) && $GLOBALS["displayTab_errResult"]!="") {
				$etape=$_POST["oldEtape"];
			} else
			if (isset($_POST["oldEtape"])) {
				if (isset($_POST["btn_enregistrer"]) ) {
					if ($_POST["btn_enregistrer"]!=$label[2]) {
						$etape=$_POST["oldEtape"]+1;
						$max=max(@$max,$etape);
					} else {
						$etape=$_POST["oldEtape"];
						$max=max(@$max,$etape+1);
					}
				} else {
					if (isset($_POST["nextEtape"]) && $_POST["nextEtape"]!="") {
						$etape=$_POST["nextEtape"];
					} else {
						$etape=$_POST["oldEtape"];
					}
					$max=max(@$max,$etape);
				}
			} else {
				$etape=0;
				if ($navBack==2) {
					$max=99;
				} else {
					$max=0;
				}
			}

?>	
<script language="javascript">
	function checkForm() {
			if (document.commentaire_formulaire) {
				for (var i=0; i<document.commentaire_formulaire.elements.length; i++) {
					if (document.commentaire_formulaire.elements[i].type == "select-one") {
						// Existe-t-il un élément pour écrire
						if (document.getElementById(document.commentaire_formulaire.elements[i].name+"_liste")) {
							for (var j=0; j<document.commentaire_formulaire.elements[i].options.length;j++) {
								document.getElementById(document.commentaire_formulaire.elements[i].name+"_liste").value+=(document.commentaire_formulaire.elements[i].options[j].value)+","
							}
						}
					}
				}
			}
			return true;
		
	}
</script>
<?
			echo "<form name='commentaire_formulaire' method='post' onkeydown='commentaire_changeForm(event);' onclic='commentaire_changeForm(event);' enctype='multipart/form-data' onSubmit='return checkForm();'>";
			echo "<table style='border:2px solid #FF6600; width:100%' cellspacing='0' cellpadding='5'><tr><td rowspan=2 style='background-color:#FF6600; width:220px;'>";
			echo "<input type='hidden' name='max' value='$max'>";
			echo "<input type='hidden' name='oldEtape' value='$etape'>";
			echo "<input type='hidden' name='nextEtape' value=''>";
			echo "<table cellspacing=0 cellpadding=0 width=100%>";
			for ($i=0; $i<count($etapeArray); $i++ ) {
				echo "<tr>";
				if ($i==$etape) {
					echo "<td style='background:url(images/tab_rond2.jpg) left top no-repeat #FEA554; width:36px; height:36px; padding-top:5px; text-align:center; font-size:20px; border-bottom:3px solid #FF6600'>".($i+1)."</td><td style='background:url(images/tab_rond3.jpg) right top no-repeat #FEA554; padding-top:7px; border-bottom:3px solid #FF6600'><div ><b>".$etapeArray[$i][0]."</b></div>";
					echo "<div style='padding-bottom:10px; padding-top:10px;'>".$etapeArray[$i][1]."</div>";
				} else {
					if ((($navBack==1 && $i<=$max) || $navBack==2) && $etape!=count($etapeArray)-1) {
						echo "<td style='background:url(images/tab_rond1.jpg) left top no-repeat #FF6600; width:36px; height:36px; padding-top:5px; text-align:center; font-size:20px'>".($i+1)."</td><td  style='padding-top:7px; '><b><a href='#' onclick='document.commentaire_formulaire.nextEtape.value=".$i."; if (checkForm()) {document.commentaire_formulaire.submit()}; return false;' style='color:#ffffff'>".$etapeArray[$i][0]."</a></b>";
					} else {
						echo "<td style='background:url(images/tab_rond1.jpg) left top no-repeat #FF6600; width:36px; height:36px; padding-top:5px; text-align:center; font-size:20px'>".($i+1)."</td><td style='padding-top:7px; '><b>".$etapeArray[$i][0]."</b>";
					}
				}
				echo "</td></tr>";
				
			}
			echo "</table>";
			echo "</td><td>";
			eval($etapeArray[$etape][2]);
			if (isset($GLOBALS["displayTab_errResult"]) && count($GLOBALS["displayTab_errResult"])>0) {
				echo "<div class='form_problemeTxt'>Erreurs dans le formulaire</div><div class='form_probleme'>";
				foreach ($GLOBALS["displayTab_errResult"] as $txt) {
					echo "<li>".$txt;
				}
				echo "</div>";
			}	

			echo "</td></tr><tr><td style='vertical-align:bottom; text-align:right'>";
			if ($etape!=count($etapeArray)-1) {
			echo "<input type='submit' class='bouton' name='btn_enregistrer' value='";
			
				if ($etape==count($etapeArray)-2) {
					echo $label[3];
				} else
				
				if ($etape<$max || $autoSave) {
					echo $label[1];
				} else {
					echo $label[2];
				}
				
				echo "'>";
			} 
			echo "</td></tr></table>";
			if ($saveAllValues) {
				foreach ($_POST as $key => $value) {
					if ($key!="oldEtape" && $key!="max" && $key!="nextEtape" && substr($key,0,4)!="btn_") {
						echo "<input type='hidden' name='".$key."' value=\"".str_replace("\"","''",$value)."\"> ";
					}
				}
			}
			echo "</form>";
}
?>
