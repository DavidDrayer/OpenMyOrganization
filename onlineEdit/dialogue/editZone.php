<?
	// Se connecte à la base de données
	header('Content-Type: text/html; charset=iso-8859-1',true);
	session_start();
	include("../db.php");
	$dbh =  connectDb(); 
	
	// Charge chaque zone 
	$query="select * from t_zone where zone_code='".str_replace("EDIT_div","",$_GET["id"])."'";
	$result=mysql_query($query);
	
	// Affiche l'éditeur pour chaque zone
	if ($result>0 && mysql_num_rows($result)>0) {
		for ($i=0; $i<mysql_num_rows($result); $i++) {
			echo "<input name='zoneid[]' value='".mysql_result($result, $i, "zone_id")."'>";
			echo "<input name='zoneorder".mysql_result($result, $i, "zone_id")."' value='".(mysql_result($result, $i, "zone_order")>0?mysql_result($result, $i, "zone_order"):mysql_result($result, $i, "zone_id"))."'>";
			echo "<input name='zonecode".mysql_result($result, $i, "zone_id")."' value='".mysql_result($result, $i, "zone_code")."'>";
			echo "<textarea class='tinymce'>".mysql_result($result, $i, "zone_contenu")."</textarea>";
		}
	} else {
		echo $query;
	}
	
?>
<script src="/onlineEdit/plugins/tinymce/tinymce.min.js"></script>	
<script src="/onlineEdit/plugins/tinymce/jquery.tinymce.min.js"></script>
<script>
  $(function() {
    
    		// transforme le textarea en éditeur - DDr 4.6.2014
       $('textarea.tinymce').tinymce({
            // Location of TinyMCE script
			menubar : false,
			plugins: "link",
			toolbar: "undo redo | bold italic | bullist numlist outdent indent | link",
			statusbar : false

        });
        // Résoud des problèmes de Focus lorsque TinyMCE est utilisé dans des boîtes de dialog - DDr
		$(document).on('focusin', function(e) {
		    if ($(event.target).closest(".mce-window").length) {
				e.stopImmediatePropagation();
			}
		});
	});	
</script>