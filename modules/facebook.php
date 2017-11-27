<?
	$GLOBALS['facebookINCLUDED']=true;

function facebook_GetDescription() {
	Return "Affiche un formulaire permettant de s'inscrire à la newsletter.<br><br> Le formulaire contient les champs suivants:<li>Nom du visiteur</li><li>Adresse e-mail du visiteur</li><br>Paramètres du modules:<br> <li>Couleur de texte</li><li>Couleur de fond</li> ";
}
function facebook_GetKeywords() {
	Return "facebook";
}
function facebook_GetTitle() {
	Return "Facebook";
}
function facebook_GetCredit() {
	Return "Ecrit par David Dräyer";
}
	
	function facebook_getParams() {
		$x=array(
					array("nom"=>"couleur_fond","label"=>"Couleur du fond","type"=>"string"),
				 array("nom"=>"couleur_texte","label"=>"Couleur du texte","type"=>"string"),
				 array("nom"=>"btn_admin","label"=>"Administration","type"=>"button")
				 
				 );
		return $x;
	}
		
function facebook_Print() {
?>
	<div class='module_facebook'>
	<div id="fb-root"></div>
	<script>(function(d, s, id) {
	  var js, fjs = d.getElementsByTagName(s)[0];
	  if (d.getElementById(id)) return;
	  js = d.createElement(s); js.id = id;
	  js.src = "//connect.facebook.net/fr_FR/sdk.js#xfbml=1&appId=367514373435550&version=v2.0";
	  fjs.parentNode.insertBefore(js, fjs);
	}(document, 'script', 'facebook-jssdk'));</script>
<div class="fb-like-box" data-href="https://www.facebook.com/aikidoverbal" data-width="229" data-colorscheme="light" data-show-faces="true" data-header="false" data-stream="false" data-show-border="false"></div>
</div>
<?
		}
?>
