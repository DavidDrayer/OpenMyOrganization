<?

	// Parcours le répertoire upload et affiche chaque image
if(!function_exists("scandir"))
{
  function scandir($dirstr)
  {
   // php.net/scandir (PHP5)
   $files = array();
   $fh = opendir($dirstr);
   while (false !== ($filename = readdir($fh)))
   {
     array_push($files, $filename);
   }
   closedir($fh);
   return $files;
  }
} 	
	
$scan  = array();


$scan = scandir("../../documents",TRUE);


echo "<div style='height:400px; background-color: #EEEEEE; width:250px; text-align:center; overflow:auto;'><table>";

foreach($scan as $key => $val)
{
	if($val=='.'||$val=='..')
 	continue;
	echo"<tr><td style='overflow:hidden; height:70px;' valign='middle' onClick='selectFile(&quot;$val&quot;)'><img src='../images/icons/";
	
	// Choix de l'image
	if (substr($val,-4)=='.pdf') {
		echo "pdf.gif";
	}
	if (substr($val,-4)=='.doc') {
		echo "doc.gif";
	}
	if (substr($val,-4)=='.xls') {
		echo "xls.gif";
	}
	
	echo "' align='middle'> $val</td></tr>\n";
}
 echo "</table></div>";
?>