<?
	session_start();
include_once("../../lib/libMiniature.php");
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
$files = array();
$dirs  = array();

$path = '../../modules'; 
$t = 0; 
$f = 0; 
$images_arr['name'] = array(); 
$images_arr['time'] = array(); 
if (@$handle = opendir($path)) { 
while (false!== ($file = readdir($handle))) { 
	if($file!= "." && $file!= "..") { 
		$fName = $file; 
		$file = $path.'/'.$file; 
		if(is_file($file)){ 
			$images_arr['time'][$t++] = filemtime($file); //<---- here it is, just a seperate key in the array to store filetimes 
			$images_arr['name'][$f++] = $fName; 
		}; 
	}; 
}; 
closedir($handle); 
asort( $images_arr['time'] ); 
asort( $images_arr['name'] ); 
} 





echo "<div style='height:400px; background-color: #EEEEEE; width:150px; text-align:center; overflow:auto;'><table>";
$tmpstring="";
//foreach($scan as $key => $val) {
foreach ($images_arr['time'] as $key=>$ftime){ 
	$val = $images_arr['name'][$key]; 
	if($val=='.'||$val=='..'||strpos($val,"Edit.php")>0||strpos($val,".php")<=0)
 	continue;
 	// Tente d'inclure le module et regarde si les fonctions existent
 
 	list($filename, $ext)=@split("[/.]",$val);
 	echo "\n<!-- include ".$val." -->\n";
 	@include ("../../modules/".$val);
 	
	echo "<tr><td>";
 	$txt=$filename;
	if (function_exists($filename.'_GetTitle')) {
		eval ('$txt='.$filename.'_GetTitle();'); 
		$txt=htmlentities($txt, ENT_QUOTES);
	}	
	if (function_exists($filename.'_GetCredit')) {
		eval ('$credit='.$filename.'_GetCredit();'); 
		$credit=htmlentities($credit, ENT_QUOTES);

	} else $credit="";
	if (function_exists($filename.'_GetDescription')) {
		eval ('$description='.$filename.'_GetDescription();'); 
		$description=htmlentities($description, ENT_QUOTES);
	} else $description=htmlentities("<i>Aucune information sur ce module</i>");
	
	echo "<a href='#' onclick='selectModule(\"$filename\",\"$credit\",\"$description\"); return false;'>".$txt."</a>";
	echo "</td></tr>";
   //list($filename, $ext)=split("[/.]",$val);
 //	if (checkMiniature ("../../images/uploads/", $filename, $ext, 100, 100, "mini")) {
 //		$tmpstring="<tr><td style='overflow:hidden; height:100px; width:100px' align='center' valign='middle'><img  alt='$val'  src='../../images/uploads/mini/".$filename.".jpg'  onClick='selectImage(&quot;$val&quot;)'></td></tr>\n".$tmpstring;
 //	} else {
 //		$tmpstring="<tr><td style='overflow:hidden; height:100px; width:100px' align='center' valign='middle'><img  alt='$val' style='display:none;' src='../../images/uploads/$val' onload='this.style.display=&quot;&quot; ; if (this.width&lt;this.height) {this.height=100} else {this.width=100};' onClick='selectImage(&quot;$val&quot;)'></td></tr>\n".$tmpstring;
 //	}

}
 echo $tmpstring;
 echo "</table></div>";
?>