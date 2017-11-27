<?

	function displayLanguage () {
		echo "<form name='fomulaire' method='post'><input type='submit' value='1' name='langue'><input type='submit' value='2' name='langue'></form>";
	}
	

	function displayPage ($parent,$level=0, $selected) {
			$query="";
			if ($parent=='null') {
				$query = "select * from t_page where page_parent is null and page_active=1 and page_visible=1 order by t_page.page_priority desc";
			} else {
				$query = "select * from t_page where page_parent=$parent and page_active=1 and page_visible=1 order by t_page.page_priority desc";
			}
			$result = mysql_query($query, $GLOBALS['dbh']);

			
			
			if ($result>0 && mysql_num_rows($result)>0) {
				if ($level==0) {
					echo '<div id="myThirdTree" class="treeView">';
				} else {
					echo "<div class='treeindex'>";
				}
			
				for ($i=0; $i<mysql_num_rows($result); $i++) {
					if (mysql_result($result,$i,"page_shortcut")!="") {
						$link="/".mysql_result($result,$i,"page_shortcut");
					} else {
						$link="index.php?page=".mysql_result($result,$i,"page_id");
					}
					$result2=getPageInfo(mysql_result($result,$i,"page_id"));
					echo '<div class="treeEntry" id="level_'.$level.'">';
					echo '	<div class="treeTitle" id="title_'.mysql_result($result,$i,"page_id").'">';
					if (mysql_result($result,$i,"page_id")==$GLOBALS['page']) {
						echo '<span class="nav_current">'.mysql_result($result2,0,"page_nom").'</span>';
					} else
					if (mysql_result($result,$i,"page_id")==$selected) {
						echo '<span class="nav_selected"><a href=".$link.">';
						echo mysql_result($result2,0,"page_nom");
						echo '</a></span>';
					} else
					if (mysql_result($result,$i,"page_active")==1) {
						echo "<a href=".$link.">";
						echo mysql_result($result2,0,"page_nom");
						echo '</a>';
					} else {
						echo '<span class="nav_inactive">'.mysql_result($result2,0,"page_nom").'</span>';
					}
					echo '</div>';
					displayPage (mysql_result($result,$i,"page_id"),$level+1, $selected);
					echo '</div>';
				}
			echo "</div>";
			}
		}


	// récupère la racine d'un certain niveau
	function getRoot ($pageId, $level) {
		$query = "select * from t_page where t_page.page_id=".$pageId."";
		$result2 = mysql_query($query, $GLOBALS['dbh']);
		if ($result2>0 && mysql_num_rows($result2)>0) {
			if (mysql_result($result2,0,"page_parent")!=null) {
				$valeurRacine=getRoot(mysql_result($result2,0,"page_parent"),$level);
				$GLOBALS['counterLevel']+=1;
				if ($valeurRacine<9999) return $valeurRacine;
			}  else {
				$GLOBALS['counterLevel']=0;
			}
			
			if ($GLOBALS['counterLevel']==$level) {
				return mysql_result($result2,0,"page_id");
			}
			return 9999;
			
		}
	}
	// Affiche les enfants d'une page spéciale
	function afficheEnfants ($pageId, $selectedPage, $separateur) {
	
		if ($pageId=='null') {
			$query = "select * from t_page  where page_parent is null and page_active=1 and page_visible=1 order by t_page.page_priority desc";
		} else {
			$query = "select * from t_page  where page_parent=$pageId and page_active=1 and page_visible=1 order by t_page.page_priority desc";
		}
		$result2 = mysql_query($query, $GLOBALS['dbh']);

		if ($result2>0 && mysql_num_rows($result2)>0) {
			for ($i=0; $i<mysql_num_rows($result2); $i++) {
			    	if (mysql_result($result2,$i,"page_shortcut")!="") {
						$link="/".mysql_result($result2,$i,"page_shortcut");
					} else {
						$link="index.php?page=".mysql_result($result2,$i,"page_id");
					}

				$result3=getPageInfo(mysql_result($result2,$i,"page_id"));

				if ($i>0) echo $separateur;
				if ($GLOBALS['page']==mysql_result($result2,$i,"page_id")) 
					echo "<span class='nav_current'><span class='short_menu'>".mysql_result($result3,0,"page_nom")."</span><span class='long_menu'>".mysql_result($result3,0,"page_titre")."</span></span>";
				else
				if ( $selectedPage==mysql_result($result2,$i,"page_id")) 
					echo "<span class='nav_current'><a href='".$link."'><span class='short_menu'>".mysql_result($result3,0,"page_nom")."</span><span class='long_menu'>".mysql_result($result3,0,"page_titre")."</span></a></span>";
				else
					echo "<a href='".$link."'><span class='short_menu'>".mysql_result($result3,0,"page_nom")."</span><span class='long_menu'>".mysql_result($result3,0,"page_titre")."</span></a>";
			}
		}

	}

	// fonction récursive
	function afficheParent($id, $dbh, $separateur) {
		$query = "select * from t_page, t_pageinfo where t_page.page_id=t_pageinfo.page_id and t_pageinfo.lang_id=".$_SESSION["langue"]." and t_page.page_id=".$id."";
		$result2 = mysql_query($query, $dbh);

			if ($result2>0 && mysql_num_rows($result2)==0) {
				$query=str_replace("and t_pageinfo.lang_id=".$_SESSION["langue"],"",$query);
				$result2 = mysql_query($query, $GLOBALS['dbh']);
			}

		if ($result2>0 && mysql_num_rows($result2)>0) {
			$GLOBALS['racine']=mysql_result($result2,0,"page_id");
			if (mysql_result($result2,0,"page_parent")!=null) {
				afficheParent(mysql_result($result2,0,"page_parent"),$dbh, $separateur);
			}
			if (mysql_result($result2,0,"page_id")==$GLOBALS['page']) {
				echo '<span class="nav_current">'.mysql_result($result2,0,"page_nom").'</span>';
			} else
			if (mysql_result($result2,0,"page_active")==1) {
				if (mysql_result($result2,0,"page_shortcut")!="") {
						$link="/".mysql_result($result2,0,"page_shortcut");
					} else {
						$link="index.php?page=".mysql_result($result2,0,"page_id");
					}
				echo "<a href='".$link."' title='Retourner sur la page:\n".mysql_result($result2,0,"page_titre")."'>".mysql_result($result2,0,"page_nom")."</a>".$separateur;
			} else {
				if (mysql_result($result2,0,"page_visible")==1) {
					echo '<span class="nav_inactive">'.mysql_result($result2,0,"page_nom").$separateur.'</span>';
				}
			}
		}
	}

	// Récupère les infos sur le ou les parents
	$racine=$page;
	$racineSousMenu=$page;
	
	// Affiche la navigation
	function displayNav ($typeNav, $separateur='', $id=0, $selected=0) {
		if ($id=='root') {
			$id='null';
		} else
		if ($id==0) $id=$GLOBALS['page'];
		if ($typeNav=="path") {
			afficheParent($GLOBALS['page'], $GLOBALS['dbh'], $separateur);
		}
		if ($typeNav=="child") {
			afficheEnfants($id, $selected, $separateur);
		}
		if ($typeNav=="tree") {
			$height=0;
			displayPage ($id,$GLOBALS['dbh'], $height, $selected);
		}
	}
?>
