<?
	$GLOBALS['galeriePicasaINCLUDED']=true;


function galeriePicasa_getParams() {
		$x=array(array("nom"=>"user","label"=>"Compte Picasa","type"=>"string"), 
						array("nom"=>"album","label"=>"Album","type"=>"string"), 
						array("nom"=>"max","label"=>"Maximum","type"=>"int","validite"=>array(1,20)), 
						array("nom"=>"thumbsize","label"=>"Taille des images","type"=>"enum","values"=>array(
						 			array("label"=>"32 pixels coupés","value"=>"32"),
						 			array("label"=>"48 pixels coupées","value"=>"48"),
						 			array("label"=>"64 pixels coupées","value"=>"64"),
						 			array("label"=>"72 pixels coupées","value"=>"72"),
						 			array("label"=>"144 pixels coupées","value"=>"144c"),
						 			array("label"=>"160 pixels coupées","value"=>"160"),
									)),
						array("nom"=>"lienPicasa","label"=>"Lien vers Picasa","type"=>"checkbox"),
						);
		return $x;
	}

	
		
	function galeriePicasa_Print() {
		if (isset($GLOBALS["picasa_count"])) {
			$GLOBALS["picasa_count"]+=1;
		} else {
			$GLOBALS["picasa_count"]=1;
		}
	if ($GLOBALS["picasa_count"]==1) {
	?>
	
	<link href="/modules/galeriePicasa/fancyBox/jquery.fancybox.css?v=2.0.5" rel="stylesheet" type="text/css" media="screen" />
	<link href="/modules/galeriePicasa/fancyBox/helpers/jquery.fancybox-thumbs.css?v=2.0.5" rel="stylesheet" type="text/css" media="screen" />
		
	<script src="/modules/galeriePicasa/fancyBox/jquery.fancybox.js?v=2.0.5" type="text/javascript"></script>
	<script src="/modules/galeriePicasa/fancyBox/helpers/jquery.fancybox-thumbs.js?v=2.0.5" type="text/javascript"></script>
	<script src="/modules/galeriePicasa/jquery.picasagallery.js" type="text/javascript"></script>


  <?
  }
  ?>
  <script type="text/javascript">
 $(document).ready( function() { 


	
	$('#picasagallery<?=$GLOBALS["picasa_count"]?>').picasagallery( {
		username: '<?=$GLOBALS["user"]?>', // Your Picasa public username
		album: '<?=$GLOBALS["album"]?>',
		link_to_picasa: <?=(isset($GLOBALS["lienPicasa"]) && $GLOBALS["lienPicasa"]=="true"?"true":"false") ?>, // true to display link to original album on Google Picasa
		thumbnail_width: '<?=$GLOBALS["thumbsize"]?>', // width of album and photo thumbnails
		thumbnail_cropped: true, // use cropped format (square)
		inline: false, // true to display photos inline instead of using the fancybox plugin
	} ); 	
	
	
} );       
</script>

		<div class="picasagallery" id="picasagallery<?=$GLOBALS["picasa_count"]?>"></div>

<?

	}
?>
