// *******************************************************************************
// ********************                TREEVIEW.JS             *******************
// *******************************************************************************

// Librairie permettant une gestion simplifié des affichages sous forme d'arbre 
//(treeView) en utilisant un maximum les possibilités des CSS et du modèle objet 
// d'Internet Explorer.
// ATTENTION, cette librairie n'a pas été testée avec autre chose que Internet 
// Explorer. Si cela ne devait pas marcher avec un autre navigateur, il suffit 
// d'ajouter un test avant d'exécuter le ManageTree(), et d'utiliser une autre 
// feuille de style, qui affiche par exemple tous les éléments.

// Version : 1.0.0
// Auteur  : David Dräyer (ddrayer@drayer.com)
// Date    : 27.01.2004

// Copyright : Ce code peut être utilisé à volonté, à condition de ne pas supprimer 
//             les références faites à son auteur

// *******************************************************************************


// Variables internes (par ce moyen, tous les arbres ont le même comportement sur une page)
_onlyOne=true
_remember=true
_oldLink = ""

// Méthodes d'accès aux variables internes. De cette manière, si une opération doit 
// être initée lors de l'accès, cela est possible
function getRememberNodeState () {
	return _remember
}

function getDisplayOnlyOneNode () {
	return _onlyOne
}

function setRememberNodeState (value) {
	_remember=value;
}

function setDisplayOnlyOneNode (value) {
	_onlyOne=value;
}

// Initialise l'arbre en affichant les bonnes images pour les premiers éléments
function initTree (objTree, selected) {
	
	// Parcours les enfants de l'arbre
	for (i=0; i<objTree.children.length; i++) {
		//s'il s'agit d'une treeEntry
		if (objTree.children[i].className=="treeEntry") {
			// parcours alors ses enfants
			for (j=0; j<objTree.children[i].children.length; j++) {
				// Si l'enfant est un treeContent, alors il faut mettre un "+"
				if (objTree.children[i].children[j].className.substr(0,11)=="treeContent" ||  objTree.children[i].children[j].className.substr(0,11)=="treeLoading") {
					objTree.children[i].className="treeEntryMore"
				}
			}
		}
	} 

	// Recherche la DIV sélectionnée
	
}

// Fonction principale qui s'occupe de dérouler l'arbre
function manageTree(elem, onlyOne, remember) {

	// Si les paramètres n'ont pas été spécifiés, prend les valeurs par défaut
	if (manageTree.arguments.length<1) onlyOne=_onlyOne;
	if (manageTree.arguments.length<2) remember=_remember;
	
	// Repère la division qui a généré l'event
//	divElem=event.srcElement;
	srcElem=(elem.target ?elem.target :elem.srcElement)
	if (srcElem.className!="treeView") {
	for (divElem=srcElem;divElem.className.substr(0,9)!="treeEntry";divElem=divElem.parentNode) {

		// Première sélection du noeud
		if (_oldLink==divElem) {
		 	_countClick+=1; 
		} else {
			_countClick=0;
		}
		// S'il s'agit d'un lien, le met en évidence
		if (divElem.tagName=="A") {
			if (_oldLink!="" ) _oldLink.className="";
			divElem.className="activeLink";
			_oldLink=divElem;
		}
	}
	
	// Ferme les autres niveaux de profondeur équivalente, uniquement si un seul niveau doit être visible
	if (onlyOne) {
		for (i=0; i<divElem.parentNode.children.length;i++) {
			if (divElem.parentNode.children[i]!=divElem) {
				divElem.parentNode.children[i].className="treeEntry"
				for (j=0; j<divElem.parentNode.children[i].children.length; j++) {
					if (divElem.parentNode.children[i].children[j].className=="treeContentVisible") {
						divElem.parentNode.children[i].children[j].className="treeContent";
						divElem.parentNode.children[i].className="treeEntryMore"
					} else if (divElem.parentNode.children[i].children[j].className=="treeContent" || divElem.parentNode.children[i].children[j].className=="treeLoading") {
						divElem.parentNode.children[i].className="treeEntryMore"
					}
				}
			}
		}
	}
		divElem.className="treeEntry";
	
	// parcours tous ces enfants directs
	for (i=0; i<divElem.children.length; i++) {
		
		// Si c'est une division de contenu visible, la cache
		if (divElem.children[i].className=="treeContentVisible") {
			if (_countClick>0) {
				divElem.children[i].className="treeContent";
				divElem.className="treeEntryMore";
			}
			
		// Si elle est cachée, l'affiche
		} else if (divElem.children[i].className=="treeContent" || divElem.children[i].className=="treeLoading") {
			
			// Si la division doit être chargée:
			if (divElem.children[i].className=="treeLoading") {
				// Récupère l'URL
				tmpURL = divElem.children[i].innerText
				divElem.children[i].innerHTML="Loading...";
				divElem.children[i].className="treeContentVisible";
				
				// Crée un objet XMLHttp pour aller chercher le complément
				var xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
				xmlhttp.Open("POST", tmpURL, false);
				xmlhttp.Send();
				
				// Ecrit le complément dans le noeud
				divElem.children[i].innerHTML=xmlhttp.responseText;				
			}
			
			// Affiche la division
			divElem.children[i].className="treeContentVisible";
			divElem.className="treeEntryLess"
			
			// Et profite pour ajuster le type des différentes sections, afin d'obtenir les + et les -
			for (j=0;j<divElem.children[i].children.length; j++) {
				if (divElem.children[i].children[j].className=="treeEntryLess" && remember==false) {
					divElem.children[i].children[j].className="treeEntryMore"
				}

					// parcours encore une fois ces enfants pour voir s'il y a du contenu
				for (k=0;k<divElem.children[i].children[j].children.length; k++) {
					if (divElem.children[i].children[j].children[k].className.substr(0,11)=="treeContent" || divElem.children[i].children[j].children[k].className=="treeLoading") {
						// si c'est une simple treeEntry (donc non-initialisé)
						if (divElem.children[i].children[j].className=="treeEntry") {
							divElem.children[i].children[j].className="treeEntryMore"
						}
						if (remember==false) {divElem.children[i].children[j].children[k].className="treeContent"}
					}
				}
			}
		}
	}
	//divElem.className="treeEntry"
	}
}
