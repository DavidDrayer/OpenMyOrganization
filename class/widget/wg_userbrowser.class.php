<?php
	namespace widget;
	
// Cette classe affiche un browser HTML permettant de parcourir un objet de type "user" dans tous ses détails
class wg_userBrowser extends Widget
{
	// l'élément role à afficher
	private $_user;
	
	// Constructeur nécessitant le user à afficher
	// Entrée : le user à afficher
	// Sortie : un objet de type wg_UserBrowser
	public function __construct(\holacracy\User $user) 
	{
		$this->_user=$user;
	}
	
	public function display() {
	
	include_once($_SERVER['DOCUMENT_ROOT'] . "/plugins/libMiniature.php");

	echo "<div id='userBrowser' >";
	//$this->_displayNav($this->_user);
	?>
	<H1><?php echo $this->_user->getFirstName()." ".$this->_user->getLastName(); ?></H1>
	<?php
		echo "<table width='100%'><tr><td>";
		if (checkMini("/images/user/".$this->_user->getId().".jpg",150,150,"mid",1,5)) {
			echo "<img style='border:1px solid black' src='/images/user/mid/".$this->_user->getId().".jpg'/>";
		} else if (checkMini("/images/user/0.jpg",150,150,"mid",1,5)) {
			echo "<img style='border:1px solid black' src='/images/user/mid/0.jpg'/>";
		}
		// Affiche quelques infos et le menu USER			
		echo "</td><td width='40%'>";
		echo "<fieldset><legend><div id='mask1'></div><span>Contact</span><div id='mask2'></div></legend>";

		// Affiche tous les contacts
		$hasmail=false;
		foreach ($this->_user->getContacts() as $contact) {
			echo "<div><span class='omo-label'>".$contact->getLabel().":</span><span class='omo-value'>".$contact->getFormatedValue()."</span></div>";
			if ($contact->getType()->getId()==3) $hasmail=true;
		}
		if (!$hasmail) {
			echo "<div><span class='omo-label'>Courriel:</span><span class='omo-value'>".$this->_user->getEmail()."</span></div>";
		}
		echo "</fieldset>";
		
		if (isset($_GET["organisation"]) || isset($_GET["circle"]) || isset($_GET["role"])) {
			if (isset($_GET["organisation"])) {
				echo "<fieldset><legend><div id='mask1'></div><span>Dans cette organisation</span><div id='mask2'></div></legend>";
	
				$roles=$this->_user->getRoles($this->_user->getManager()->loadOrganisation($_GET["organisation"]),\holacracy\Role::STANDARD_ROLE | \holacracy\Role::LINK_ROLE);
				$myroles=$_SESSION["currentUser"]->getRoles($this->_user->getManager()->loadOrganisation($_GET["organisation"]),\holacracy\Role::STANDARD_ROLE | \holacracy\Role::LINK_ROLE);
	
			}
			if (isset($_GET["circle"])) {
				echo "<fieldset><legend><div id='mask1'></div><span>Dans ce cercle</span><div id='mask2'></div></legend>";
				$roles=$this->_user->getRoles($this->_user->getManager()->loadCircle($_GET["circle"]));
				$myroles=$_SESSION["currentUser"]->getRoles($this->_user->getManager()->loadCircle($_GET["circle"]));
			}
			if (isset($_GET["role"])) {
				echo "<fieldset><legend><div id='mask1'></div><span>Dans le cercle</span><div id='mask2'></div></legend>";
				$roles=$this->_user->getRoles($this->_user->getManager()->loadRole($_GET["role"])->getSuperCircle());
				$myroles=$_SESSION["currentUser"]->getRoles($this->_user->getManager()->loadRole($_GET["role"])->getSuperCircle());
			}
			if (count($roles)>0) {
				echo "<ul>";
				foreach ($roles as $role) {
					if ($role->getUserId()!=$this->_user->getId()) {
						// Resterait à préciser pourquoi c'est secondaire: attribué d'office au 1er lien, second lien ou focus
						if ($role->getType()==\holacracy\Role::CIRCLE) {
							echo "<li><span class='omo-role-".$role->getType()."'><a href='role.php?id=".$role->getId()."'>".$role->getName()."</a></span></li>";
						} else {
							echo "<li><span class='omo-role-".$role->getType()."'><a href='role.php?id=".$role->getId()."'>".$role->getName()."</a></span></li>";
						}
					} else {
						echo "<li><span class='omo-role-".$role->getType()."'><b><a href='role.php?id=".$role->getId()."'>".$role->getName()."</a></b></span></li>";
					}
			
				}
				echo "</ul>";
			} else {
				echo "Aucun rôle";
			}
			echo "</fieldset>";
		}
		echo "</td><td style='width:60%'>";
		
		// Affichage du formulaire de contact - DDr 4.6.2014
		if ($_SESSION["currentUser"]->getId()!=$this->_user->getId()) {
		echo "<fieldset><legend><div id='mask1'></div><span>Envoyer un message</span><div id='mask2'></div></legend>";
		echo "<form name='formulaire' id='formulaire'>";
		echo "<input type='hidden' id='form_target' value='/formulaires/form_message.php'>";
		echo "<input type='hidden' name='action' value='sendMessage'>";
		echo "<input type='hidden' name='msg_from' value='".$_SESSION["currentUser"]->getId()."'>";
		echo "<input type='hidden' name='msg_to' value='".$this->_user->getId()."'>";

		echo "De<br/>";
		echo "<select class='fill'  name='msg_fromRole' id='msg_fromRole'><option value=''>Choisissez...</option>";
		foreach ($myroles as $role) {
			echo "<option value='".$role->getId()."'>Rôle <b>[".$role->getName()."]</b></option>";
			
		}
		echo "<option value='0'>".$_SESSION["currentUser"]->getFirstName()." ".$_SESSION["currentUser"]->getLastName()."</option>";
		echo "</select>";
		
		echo "À<br/>";
		echo "<select class='fill' name='msg_toRole' id='msg_toRole'><option value=''>Choisissez...</option>";
		foreach ($roles as $role) {
			echo "<option value='".$role->getId()."'>Rôle [".$role->getName()."]</option>";
			
		}
		echo "<option value='0'>".$this->_user->getFirstName()." ".$this->_user->getLastName()."</option>";
		echo "</select>";
		echo "Titre<br/>";
		echo "<input class='fill' name='msg_title' id='msg_title'>";
		echo "Message<br/>";
		echo "<textarea class='fill' rows='7' name='msg_text' id ='msg_text'></textarea>";
		echo "<button>Envoyer</button>";
		echo "</form>";
		echo "</fieldset>";
		}
		echo "</td></tr></table>";
		
?>
		<script type="text/javascript">	
        //$( "#dialogStd" ).dialog({ buttons: [{ text: "Ajouter", click: function() { $( "#formulaire").submit(); } }, {text: "Annuler", click: function() { $( this ).dialog( "close" ); }} ] });

	  $("#formulaire").submit(function() {
		// Envoie le formulaire en AJAX (méthode POST), la destination étant définie par l'élément à l'ID form_target 
	 	$.post($("#form_target")[0].value, $("#formulaire").serialize()) 
	        .done(function(data, textStatus, jqXHR) {
	            if (textStatus="success")
	            {
	            	// Traite une éventuelle erreur
	            	if (data.indexOf("Erreur")>0) {
	            		eval(data);
	            	} else {
	            	
	            	
		            	// Affiche les données en retour en remplacement du contenu du formulaire (le contenant reste) 
		                $("#formulaire")[0].innerHTML=data;
		                // Intérprète les scripts retournés (à vérifier si ça fonctionne)
		                eval($("#formulaire").find("script").text());
	                }
				}
	            else {
	            	// Problème d'envoi
	            	alert("Echec!");
	            
	            }
	            
	        });
	        // Bloque la procédure standard d'envoi
	        return false;
	});

        </script>
<?		
		
		
	}
}

?>
