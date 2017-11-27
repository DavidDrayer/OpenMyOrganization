	<?

		function displayPage ($parent,$dbh, $height, $count=0) {
			$query="";
			if ($parent==0) {
				$query = "select * from t_page where page_parent is null";
			} else {
				$query = "select * from t_page where page_parent=$parent";
			}
			$result = mysql_query($query, $dbh);
			
			if ($result>0 && mysql_num_rows($result)>0) {
				if ($parent==0) {
					echo '<div id="myThirdTree" class="treeView" onClick="manageTree(event,false,true)" style="height:'.$height.'px; overflow:auto">';
				} else {
					if ($count<3) {
						echo "<div class='treeContentVisible'>";
					} else {
						echo "<div class='treeContent'>";
					}
				}
			
				for ($i=0; $i<mysql_num_rows($result); $i++) {
					$result2=getPageInfo(mysql_result($result,$i,"page_id"));
					echo '<div class="treeEntry">';
					echo '	<img src="../images/icons/treeURL.gif"/> <a href="#" onclick="setLink('.mysql_result($result,$i,"page_id").'); return false;">';
					if (mysql_result($result,$i,"page_active")==1) {
						echo mysql_result($result2,0,"page_titre");
					} else {
						echo '<span style="color:#999999">'.mysql_result($result2,0,"page_titre").'</span>';
					}
					echo '</a>';
					displayPage (mysql_result($result,$i,"page_id"),$dbh,$height, $count+1);
					echo '</div>';
				}
			echo "</div>";
			}
		}
		displayPage (0,$dbh, $height);

	?>
		
	