<?php
	namespace widget;
// Cette classe affiche un browser HTML permettant de parcourir un objet de type "cercle" dans son
// intégralité : liste de rôles, sous-cercles, projets, liste de membres, etc...
class wg_moiBrowser extends Widget
{
	// l'élément role à afficher
	private $_organisation;
	private $_user;
	
	public function __construct($user,$organisation) 
	{
		$this->_organisation=$organisation;
		$this->_user=$user;
	}
	
	//Fonction pour créer les fichiers MOI
	private function _createMoiFile($user,$organisation){
	

	$fichiermoiuser = 'json/gtd-user'.$user->getId().'.json';
	$fichiermoitodouser = 'json/gtdtodo-user'.$user->getId().'.json';
	$fichierdata = 'json/data-user'.$user->getId().'.tmp';
	
	$handlemoiuser = fopen($fichiermoiuser, 'w+');
	$handlemoitodouser = fopen($fichiermoitodouser, 'w+');
	$handledata = fopen($fichierdata, 'w+');
	
	fputs($handlemoiuser,"["); 
	fputs($handlemoitodouser,"["); 
	
	//$this->getTensions($tensions,$handlemoiuser); //Génère pour le .json l'ensemble des tensions de l'utilisateur
	$this->_getActivity($organisation,$user,$handlemoiuser,$handlemoitodouser,$handledata); //Génère pour le .json l'activite des divers rôles avec les projets et actions
	
	$pointeur =ftell($handlemoiuser);
	if($pointeur == 1){}else{$pointeur = $pointeur-1;} //Si pas d'enregistrement on fait rien
	fseek($handlemoiuser,$pointeur);
	fputs($handlemoiuser,"]"); 
	fclose($handlemoiuser);
	
	$pointeur2 =ftell($handlemoitodouser);
	if($pointeur2 == 1){}else{$pointeur2 = $pointeur2-1;} //Si pas d'enregistrement on fait rien
	fseek($handlemoitodouser,$pointeur2);
	fputs($handlemoitodouser,"]"); 
	fclose($handlemoitodouser);
	
	fclose($handledata);
	
	$handledata = fopen($fichierdata, "r");
	$data = fread($handledata,filesize($fichierdata));
	fclose($handledata);
	unlink($fichierdata);
	
	return $data;
	
	}
	
	
	// FONCTIONS A ADAPTER POUR LE WIDGET * Anciennement dans la class USER
	//kda 6.6.2014
	public function getInbox(){
	
	}
	
	//kda 6.6.2014
	/* public function getTensions($tensions,$fp){
				
	$write = '{"key": "tension", "title": "Tensions", "folder": true, "extraClasses": "tensions-class", "children": [';
	fputs($fp,$write);
	//boucle pour parcourir les tensions trouvées pour un user
	//Boucle sur les cercles
	$cmpt = 1;
	$nbtension = count((array)$tensions);
		foreach ($tensions as $tension) {
		$titletension = $tension->getName();
		$titletension = htmlentities($titletension, ENT_NOQUOTES);
		$keytension = "t-".$tension->getId();
		$idtension = $tension->getId();
		$rolenameid = $tension->getRoleId()."-nomdujobavecID=>".$tension->getRoleId(); //Récupérer le nom du job
		$notestension = $tension->getDescription();
		$notestension = htmlentities($notestension, ENT_NOQUOTES);
		$class="tension-class";
		$write = '{"key": "'.$keytension.'", "title": "'.$titletension.'", "ID":"'.$idtension.'", "role":"'.$rolenameid.'", "notes":"'.$notestension.'", "extraClasses": "'.$class.'"}';
		fputs($fp,$write);  //Ecris ds le fichier
		if($cmpt != $nbtension) { $write = ','; fputs($fp,$write);} //ecris la virgule tant que c'est pas fini
		$cmpt++;
		}
	$write = ']},';
	fputs($fp,$write);	
	
	
{"key": "tension", "title": "Tensions", "folder": true, "extraClasses": "tensions-class", "children": [
			{"key": "t-IDtension1", "title": "titre de la tension A", "ID":"IDtensionA", "role":"100-ScrumMaster", "notes":"infos sur la tension A", "extraClasses": "tension-class"},
			{"key": "t-IDtension2", "title": "titre de la tension A", "ID":"IDtensionB", "role":"u-KevinD", "notes":"infos sur la tension B", "extraClasses": "tension-class"}
		]}, 
	
	
	} */
	
	//Recupère l'ensemble des rôles, projets et actions pour un User + le TODO
	private function _getActivity($object,$user,$fp,$fptodo,$fpdata) {
		
		$end = false;
		$circles=$object->getCircles();
		
		$nbcircle = count((array)$circles); 
			
		    //Boucle sur les cercles
			foreach ($circles as $circle) {
			
				$circlename = $circle->getName();
				$circlename = htmlentities($circlename, ENT_COMPAT , "ISO-8859-1");
				$circleID = $circle->getId();
				$circleclass = "class".$circleID;
				
				$write = $circle->getName()."-".$circleclass.";";
				fputs($fpdata,$write);  //Ecris ds le fichier data
							
				$roles = $user->getRoles($circle);
				//Boucle sur les roles	
				foreach ($roles as $role) { 
							
			    $rolename = $role->getName();
				$rolename = htmlentities($rolename, ENT_COMPAT , "ISO-8859-1");

				if($role->getType() == 2 || $role->getType() == 8 || $role->getType() == 4 || $role->getType() == 16) { $titlerole = $rolename." ".$circlename;}
				else { $titlerole = $rolename." <span class='minicirclemoi'>".$circlename."</span>";}
				$titlerole = htmlspecialchars_decode($titlerole);
				$titlerole = str_replace( array( '<br>', '<br />', "\n", "\r",'"' ), array( '', '', '', '<br/>', '&quot;' ), $titlerole );
				
				//Recuperation des redevabilités
				$writeaccountabilites = "";
				$accountabilites = $role->getAccountabilities();
				$nbaccountabilites = count((array)$accountabilites); 
				if($nbaccountabilites >0){$writeaccountabilites = "<ul>";}
					foreach ($accountabilites as $accountability) { 
					$accountabilitytxt = $accountability->getDescription();
					$accountabilitytxt = htmlentities($accountabilitytxt, ENT_COMPAT , "ISO-8859-1");
					$accountabilitytxt = htmlspecialchars_decode($accountabilitytxt);
					$accountabilitytxt = str_replace( array( '<br>', '<br />', "\n", "\r",'"' ), array( '', '', '', '<br/>', '&quot;' ), $accountabilitytxt );
					$writeaccountabilites = $writeaccountabilites."<li>".$accountabilitytxt."</li>";
					}
				if($nbaccountabilites >0){$writeaccountabilites = $writeaccountabilites."</ul>";}
				
				//Recuperation des domaines
				$writedomains = "";
				$domains = $role->getScopes();
				$nbdomains = count((array)$domains); 
				$cmptdomains = 1;
					foreach ($domains as $domain) { 
					$domaintxt = $domain->getDescription();
					$domaintxt = htmlentities($domaintxt, ENT_COMPAT , "ISO-8859-1");
					$domaintxt = htmlspecialchars_decode($domaintxt);
					$domaintxt = str_replace( array( '<br>', '<br />', "\n", "\r",'"' ), array( '', '', '', '<br/>', '&quot;' ), $domaintxt );
					if($cmptdomains==1){$writedomains = $writedomains.$domaintxt;}else{
					$writedomains = $writedomains." , ".$domaintxt;}
					$cmptdomains++;
					}
				
				$purpose = $role->getPurpose();
				$purpose = htmlentities($purpose, ENT_COMPAT , "ISO-8859-1");
				$purpose = htmlspecialchars_decode($purpose);
				$purpose = str_replace( array( '<br>', '<br />', "\n", "\r",'"' ), array( '', '', '', '<br/>', '&quot;' ), $purpose );
				
				$keyrole = "r".$role->getId();
				$folder="true";
				switch($role->getType()){
					case 2 : 
					$class="leadlink-class";
					break;
					case 8 : 
					$class="secretary-class";
					break;
					case 4 : 
					$class="replink-class";
					break;
					case 16 : 
					$class="facilitor-class";
					break;
					default:
					$class="role-class";
					break;
				}

				$write = '{"key": "'.$keyrole.'", "IDcircle":"'.$circleID.'", "purpose":"'.$purpose.'", "accountabilities":"'.$writeaccountabilites.'", "scope":"'.$writedomains.'", "circle": "'.$circlename.'", "title": "'.$titlerole.'", "folder": '.$folder.', "extraClasses": "'.$class.'"';
				fputs($fp,$write);  //Ecris ds le fichier
				
				//on récupère les projets associé au rôle
				$projets = $role->getProjects(\holacracy\Project::ACTIVEMOI_PROJECTS,$user->getId()); 
				$nbprojet = count((array)$projets); 
				
				//on récupère les actions associé au rôle (en fonction des focus ou pas)
				//$fillers=$role->getRoleFillers();
				//if (count($fillers)>1) {//Si plusieurs focus
				//	$actionsSoloMoi = $role->getActionsMoi($user);
				//} else {//Un seul filler
				//$actionsSoloMoi = $role->getActionsMoi();
				//}
				$actionsSoloMoi = $role->getActionsMoi($user);
												
				$nbactionsRoleMoi = count((array)$actionsSoloMoi); 
				
				//on récupère les projets oneday associé au rôle
				$projetsoneday = $role->getProjects(\holacracy\Project::DELAYED_PROJECT,$user->getId()); 
				$nbprojetoneday = count((array)$projetsoneday); 			
				
				//On cree le children par défaut car dossier Oneday
				fputs($fp,', "children": [');
					
					//Boucle sur les projets
					$cmptprojet = 1;
					foreach($projets as $projet){
										
					$idprojet = $projet->getId();
					$notes = $projet->getDescription();
					$notes = htmlentities($notes, ENT_COMPAT , "ISO-8859-1");
					$notes = str_replace( array( '<br>', '<br />', "\n", "\r",'"' ), array( '', '', '', '<br/>', '&quot;' ), $notes );
					
					$titleprojet = $projet->getTitle();
					$titleprojet = htmlentities($titleprojet, ENT_COMPAT , "ISO-8859-1");
					$titleprojet = str_replace( array( '<br>', '<br />', "\n", "\r",'"' ), array( '', '', '', '<br/>', '&quot;' ), $titleprojet );
					
					$typeprojet = 1; //projet
					
					//Pour les projets
					$keyprojet = $keyrole."-p".$projet->getId();
					$statut = $projet->getStatusId();
					switch($statut){
					case 1 : 
					$class="projet-class-run";
					break;
					case 2 : 
					$class="projet-class-wait";
					break;
					case 4 : 
					$class="projet-class-done";
					break;	
					case 8 : 
					$class="projet-class-oneday";
					break;					
					}
					
					$write = '{"key": "'.$keyprojet.'", "IDcircle":"'.$circleID.'", "type":"'.$typeprojet.'", "title": "'.$titleprojet.'", "ID": "'.$idprojet.'", "extraClasses": "'.$class.'", "notes":"'.$notes.'", "statut" : "'.$statut.'"';	
					fputs($fp,$write);  //Ecris ds le fichier
					$actionsMoi = $projet->getActionsMoi(); //on récupère les actions associées au projet
					
					$nbactionMoi = count((array)$actionsMoi); 
						if($nbactionMoi == 0){ fputs($fp,"}");
						} else{ fputs($fp,', "children": [');
						}
						//Boucle sur les actions rattachées à un projet
						$cmptaction = 1;
						foreach($actionsMoi as $actionMoi){
																							
						$idactionmoi = $actionMoi->getId();
						$notes = $actionMoi->getDescription();
						$notes = htmlentities($notes, ENT_COMPAT , "ISO-8859-1");
						$notes = str_replace( array( '<br>', '<br />', "\n", "\r",'"' ), array( '', '', '', '<br/>', '&quot;' ), $notes );
						
						$titleactionMoi = $actionMoi->getTitle();
						$titleactionMoi = htmlentities($titleactionMoi, ENT_COMPAT , "ISO-8859-1");
						$titleactionMoi = str_replace( array( '<br>', '<br />', "\n", "\r",'"' ), array( '', '', '', '<br/>', '&quot;' ), $titleactionMoi );
						
						$keyactionmoi = $keyprojet."-a".$actionMoi->getId(); 
						$statut = $actionMoi->getStatusId();
						
						$typeaction = 2; //action
						
						switch($statut){
						case 1 : 
						$class="action-class-run";
						break;
						case 2 : 
						$class="action-class-wait";
						break;
						case 8 : 
						$class="action-class-trigger";
						break;	
						case 16 : 
						$class="action-class-delete";
						break;							
						}
											
						$write = '{"key": "'.$keyactionmoi.'", "effort":"", "time":"", "title": "'.$titleactionMoi.'", "type":"'.$typeaction.'", "ID": "'.$idactionmoi.'", "extraClasses": "'.$class.'", "notes":"'.$notes.'", "statut" : "'.$statut.'"}';	
						fputs($fp,$write);  //Ecris ds le fichier
						
						if($statut != 8){
						$writetodo = '{"key": "'.$idactionmoi.'", "IDcircle":"'.$circleID.'", "father":"'.$titleprojet.'", "effort":"", "time":"", "title": "'.$titleactionMoi.'", "type":"'.$typeaction.'", "ID": "'.$keyactionmoi.'", "extraClasses": "'.$class.' '.$circleclass.' all", "notes":"'.$notes.'", "statut" : "'.$statut.'"}';	
						fputs($fptodo,$writetodo);  //Ecris ds le fichier
						fputs($fptodo,",");
						}
						
						if($nbactionMoi != $cmptaction){ fputs($fp,",");
						} //Si c'est pas le dernier, on met une , pour le suivant
					
						$cmptaction++;
						}
						if($nbactionMoi == 0){ } else{ fputs($fp,"]}");
						}
					
					if($nbprojet != $cmptprojet){fputs($fp,",");
					} //Si c'est pas le dernier, on met une , pour le suivant
					$cmptprojet++;
					}
									
					//Boucle sur les actions solo
					$cmptactionsolo = 1;
					foreach($actionsSoloMoi as $actionSolo){
					if($cmptactionsolo == 1 && $nbprojet != 0){fputs($fp,",");}
					
					$idactionmoi = $actionSolo->getId();
					$notes = $actionSolo->getDescription();
					$notes = htmlentities($notes, ENT_COMPAT , "ISO-8859-1");
					$notes = str_replace( array( '<br>', '<br />', "\n", "\r",'"' ), array( '', '', '', '<br/>', '&quot;' ), $notes );
					
					$titleactionMoi = $actionSolo->getTitle();
					$titleactionMoi = htmlentities($titleactionMoi, ENT_COMPAT , "ISO-8859-1");
					$titleactionMoi = str_replace( array( '<br>', '<br />', "\n", "\r",'"' ), array( '', '', '', '<br/>', '&quot;' ), $titleactionMoi );
					$typeaction = 2; //action
					
					$keyactionmoi = $keyrole."-a".$actionSolo->getId(); 
					$statut = $actionSolo->getStatusId();
					
					switch($statut){
					case 1 : 
					$class="action-class-run";
					break;
					case 2 : 
					$class="action-class-wait";
					break;
					case 8 : 
					$class="action-class-trigger";
					break;	
					case 16 : 
					$class="action-class-delete";
					break;						
					}
					
					$write = '{"key": "'.$keyactionmoi.'", "effort":"", "time":"", "title": "'.$titleactionMoi.'", "type":"'.$typeaction.'", "ID": "'.$idactionmoi.'", "extraClasses": "'.$class.'", "notes":"'.$notes.'", "statut" : "'.$statut.'"}';	
					fputs($fp,$write);  //Ecris ds le fichier
									
					if($statut != 8){
					$writetodo = '{"key": "'.$idactionmoi.'", "father": "'.$titlerole.'", "IDcircle":"'.$circleID.'", "effort":"", "time":"", "title": "'.$titleactionMoi.'", "type":"'.$typeaction.'", "ID": "'.$keyactionmoi.'", "extraClasses": "'.$class.' '.$circleclass.' all", "notes":"'.$notes.'", "statut" : "'.$statut.'"}';	
					fputs($fptodo,$writetodo);  //Ecris ds le fichier
					fputs($fptodo,",");
					}
		
					
					if($nbactionsRoleMoi != $cmptactionsolo){ fputs($fp,",");} //Si c'est pas le dernier, on met une , pour le suivant
					
					$cmptactionsolo++;
					}
					
					//Creation du dossier ONEDAY
					$keyoneday = $keyrole."-o".$role->getId();
					$titleoneday = "Oneday";
					$class = "dossier-oneday";
					
					//Si pas de projet, ni de role, children, sinon ,
					if($nbprojet == 0 && $nbactionsRoleMoi == 0){} else{fputs($fp,',');}
					
					//On crée le dossier Oneday
					$write = '{"key": "'.$keyoneday.'", "title": "'.$titleoneday.'", "extraClasses": "'.$class.'"';	
					fputs($fp,$write);  //Ecris ds le fichier
					if($nbprojetoneday == 0){ fputs($fp,"}");} else{ fputs($fp,', "children": [');}
					
					//Boucle sur les projets oneday
					$cmptprojetoneday = 1;
					foreach($projetsoneday as $projetoneday){
					
					$idprojet = $projetoneday->getId();
					$notes = $projetoneday->getDescription();
					$notes = htmlentities($notes, ENT_COMPAT , "ISO-8859-1");
					$notes = str_replace( array( '<br>', '<br />', "\n", "\r",'"' ), array( '', '', '', '<br/>', '&quot;' ), $notes );
					
					$titleprojet = $projetoneday->getTitle();
					$titleprojet = htmlentities($titleprojet, ENT_COMPAT , "ISO-8859-1");
					$titleprojet = str_replace( array( '<br>', '<br />', "\n", "\r",'"' ), array( '', '', '', '<br/>', '&quot;' ), $titleprojet );
					
					$typeprojet = 1; //projet
					
					//Pour les projets
					$keyprojet = $keyrole."-p".$projetoneday->getId();
					$statut = $projetoneday->getStatusId();
					$class="projet-class-oneday";
				
					$write = '{"key": "'.$keyprojet.'", "type":"'.$typeprojet.'", "title": "'.$titleprojet.'", "ID": "'.$idprojet.'", "extraClasses": "'.$class.'", "notes":"'.$notes.'", "statut" : "'.$statut.'"}';	
					fputs($fp,$write);  //Ecris ds le fichier
					
					if($nbprojetoneday != $cmptprojetoneday){ fputs($fp,",");} else{ fputs($fp,"]}"); } //Si c'est pas le dernier, on met une , pour le suivant
					
					$cmptprojetoneday++;
					}
					
					if($nbprojet == 0 && $nbactionsRoleMoi == 0){ fputs($fp,']}');} else{ fputs($fp,']}');}								
				fputs($fp,",");
				}
				
				//Rappel fonction pour le cercle suivant
				$this->_getActivity($circle,$user,$fp,$fptodo,$fpdata);
			}
	}
	
	public function display() {
	
	//Lancement de la fonction pour créer le MOI.Json et récupération du tableau des cercles de l'ORG
	$circlesORG = $this->_createMoiFile($this->_user,$this->_organisation);

	?>

<html>
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
	<title><? echo $this->_organisation->getName()." | MOI de ".$this->_user->getFirstName()." ".$this->_user->getLastName() ?></title>
		<script src="/plugins/jquery-2.1.0.min.js"></script>
		<script src="/plugins/jquery-ui-1.10.3.custom/js/jquery-ui-1.10.3.custom.min.js"></script>
	
	<!-- Le code javascript !-->
<script src="plugins/jquery.mCustomScrollbar.min.js"></script>
<script src="/scripts/moi.js"></script>
<!--<script src="/scripts/moi-outline.js"></script> !-->

<!-- Le CSS !-->

<link href="style/jquery.mCustomScrollbar.css" rel="stylesheet" type="text/css" />

<!-- Fancytree !-->
 <script src="plugins/fancytree/lib/jquery-ui-contextmenu/jquery.ui-contextmenu.js" type="text/javascript"></script>
<link href="plugins/fancytree/skin-win7/ui.fancytree.css" rel="stylesheet" type="text/css">
<script src="plugins/fancytree/src/jquery.fancytree.js" type="text/javascript"></script>
<script src="plugins/fancytree/src/jquery.fancytree.dnd.js" type="text/javascript"></script> <!-- pour drag and drop !-->
<script src="plugins/fancytree/src/jquery.fancytree.edit.js" type="text/javascript"></script> <!-- pour editer !-->
<script src="plugins/fancytree/src/jquery.fancytree.gridnav.js" type="text/javascript"></script>  <!-- pour la grille !-->
<script src="plugins/fancytree/src/jquery.fancytree.table.js" type="text/javascript"></script> <!--  pour la table !-->
<script src="plugins/fancytree/src/jquery.fancytree.childcounter.js" type="text/javascript"></script>  <!--  le compteur d'action par projet !-->
<script src="plugins/fancytree/src/jquery.fancytree.filter.js" type="text/javascript"></script> <!--  pour le filtre de recherche !-->
<script src="plugins/fancytree/src/jquery.fancytree.themeroller.js" type="text/javascript"></script>   <!--  themecss !-->
<link href="plugins/fancytree/skin-themeroller/ui.fancytree.css" rel="stylesheet" type="text/css"> <!--  themecss !-->

<!-- Pizza pie charts !-->
<link href="plugins/pizza/css/pizza.css" rel="stylesheet" type="text/css" />
<script src="plugins/pizza/js/vendor/snap.svg.js"></script>
<script src="plugins/pizza/js/pizza.js"></script>
<style>
			body {margin:68px 0px 0px 0px; padding:5px; }
			body, table {font-size:12px; font-family: "Verdana","Arial","sans-serif"; }

			.header {position:fixed; background:#EF3456;  top:0px; left:0px; right:0px; height:30px;z-index:5}
		
			.omo-maindiv { width:100%; max-width:1280px; height:100%; margin-left: auto;  margin-right: auto; overflow: hidden; }
			.omo-leftcol { width:20%;  float:left; height:100%}
			.omo-right { float:right; width:23%; height:100%;  margin-left:4px; }
			.moreinfos { padding:3%;  background:#aec4d8; }
			.omo-middle { height:100%; overflow: hidden; }

			.omo-cols {overflow:hidden}
			.col1 {width:50%; float:left; padding-right:5px;}
			.col2 {overflow:hidden; }
			
			.ellipsis {width: 100%; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;}

			.omo-logo {background:url('/images/user.png') left 50% no-repeat; padding-left:23px; min-height:18px; display:inline-block;}
			.omo-navv{ position:fixed; bottom:0px; right:25px; height:70px;}
			#omo-btnhaut {width:31px; height:31px; background:url('/images/btn_up_off.png')}
			#omo-btnhaut:hover {background:url('/images/btn_up_on.png')}
			#omo-btnbas {width:31px; height:31px; background:url('/images/btn_down_off.png')}
			#omo-btnbas:hover {background:url('/images/btn_down_on.png')}
			#tabs_gauche .ui-state-active, #tabs_moreinfos .ui-state-active{width:100%;}
			#tabs_moreinfos .ui-tabs-nav {margin-left:-1px;}
			
			.omo-text-scrollv {
				width:100%; max-height:200px; overflow:auto; background-color:#FFFFFF; padding:5px; margin-bottom:10px;
			    scrollbar-3dlight-color:gold;
			    scrollbar-arrow-color:blue;
			    scrollbar-base-color:red;
			    scrollbar-darkshadow-color:blue;
			    scrollbar-face-color:red;
			    scrollbar-highlight-color:yellow;
			    scrollbar-shadow-color:blue;
				
				  -moz-border-radius: 5px;
		        -webkit-border-radius: 5px;
		        border-radius: 5px;			
			}
			
			
			/* Let's get this party started */ ::-webkit-scrollbar { width: 12px; } /* Track */ ::-webkit-scrollbar-track { -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3); -webkit-border-radius: 10px; border-radius: 10px; } /* Handle */ ::-webkit-scrollbar-thumb { -webkit-border-radius: 10px; border-radius: 10px; background: rgba(208,224,235,0.8); -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.5); } ::-webkit-scrollbar-thumb:window-inactive { background: rgba(208,224,235,0.4); }
			
			@media all and (min-width: 1000px)  {
				.omo-leftcol {height:100%}	
				.omo-navv {display:none;}
				.omo-accordion-info {float:right; display:block; }
			}
			@media all and (min-width: 800px) and (max-width: 1030px)  {

				.omo-leftcol { width:300px; }	
				.col1 {float:inherit; width:100%}
				.omo-tab-label {display:none}
				.omo-navv {display:none;}
				.omo-accordion-info {float:inherit; display:block; }
	
			}
			@media all and (max-width: 799px)  {
				.omo-leftcol {float:inherit; width:100%;overflow:hidden}	
				.omo-rightcol {float:inherit; width:100%;}	
				.omo-maindiv {overflow: auto;height:auto; }
				.omo-accordion-info {float:right; display:block; }
				
			}
			@media all and (max-width: 710px)  {	
				.col1 {float:inherit; width:100%}
				.omo-accordion-info {float:inherit; display:block; }
				.omo-tab-label {display:none}
				
			}
		</style>
		<link rel="stylesheet" href="style/templates/<?=$_SESSION["template"]?>/circle.css" />
		
		<!-- styles needed by jScrollPane -->
		<link href="/style/templates/blue/jquery-ui-1.10.4.custom.css" rel="stylesheet" type="text/css" />	
		 
		<!-- <script src="/scripts/circle.js"></script> !-->
		
		<script>
			function moveUp() {
				newpos=parseInt(($(window).scrollTop()-100)/$(window).height())*$(window).height();
				$(window).scrollTop(newpos);
			}
			function moveDown() {
				newpos=parseInt($(window).scrollTop()/$(window).height()+1)*$(window).height();
				$(window).scrollTop(newpos);
			}
			$(document).ready(function(){
				$("#omo-btnhaut").click(moveUp);
				$("#omo-btnbas").click(moveDown);
				$("#tabs_gauche").tabs({heightStyle: "fill"});
				$("#tabs_moreinfos").tabs({heightStyle: "fill"});
				$("#tabs_middle").tabs({heightStyle: "fill"});
				
				$(window).resize(function () {
						$("#tabs_gauche").tabs({heightStyle: "fill"});
						$("#tabs_middle").tabs({heightStyle: "fill"});
						$("#tabs_moreinfos").tabs({heightStyle: "fill"});
				
				})
				
			});
		</script>
	</head>
<body>	


<?php
echo "<div id='main_waiting_screen'>".\widget\Widget::FULL_WAITING_SCREEN."</div>";?>

	<div class='header'><a href="organisation.php?id=<? echo $this->_organisation->getId();?>"><?$this->_displayNav($this->_organisation);?></a></div>

<div class='omo-maindiv'>
<div class='omo-leftcolmoi'>
		
	<div id="tabs_gauche">
	<ul><li><a name="tabsG-1" href="#tabsG-1"><?php echo $_SESSION["currentUser"]->getFirstName()." ".$_SESSION["currentUser"]->getLastName();?></a></li></ul>  
		<!-- Onglet 1, avec les différentes infos sur le cercle -->  
		<div id="tabsG-1">
		  <!--
			Projets en cours</br>
			Projets en attente</br>
			Actions en cours</br>
			Actions en attente</br>
		!-->
		</div>
	</div>
</div>

<!-- Onglet pour le MOI -->  
<div class="omo-onglet">
<? include "wg_moibrowser/onglet_outline.php" ?>
</div>
</div>
</body>
</html>
<?php
	}
}

?>

