// *******************************************************************************
// ********************                TREEVIEW.JS             *******************
// *******************************************************************************

// Librairie permettant une gestion simplifi� des affichages sous forme d'arbre 
//(treeView) en utilisant un maximum les possibilit�s des CSS et du mod�le objet 
// d'Internet Explorer.
// ATTENTION, cette librairie n'a pas �t� test�e avec autre chose que Internet 
// Explorer. Si cela ne devait pas marcher avec un autre navigateur, il suffit 
// d'ajouter un test avant d'ex�cuter le ManageTree(), et d'utiliser une autre 
// feuille de style, qui affiche par exemple tous les �l�ments.

// Version : 1.0.0
// Auteur  : David Dr�yer (ddrayer@drayer.com)
// Date    : 27.01.2004

// Copyright : Ce code peut �tre utilis� � volont�, � condition de ne pas supprimer 
//             les r�f�rences faites � son auteur

// *******************************************************************************


// Variables internes (par ce moyen, tous les arbres ont le m�me comportement sur une page)
_onlyOne=true
_remember=true
_oldLink = ""

// M�thodes d'acc�s aux variables internes. De cette mani�re, si une op�ration doit 
// �tre init�e lors de l'acc�s, cela est possible
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

// Initialise l'arbre en affichant les bonnes images pour les premiers �l�ments
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

	// Recherche la DIV s�lectionn�e
	
}

// Fonction principale qui s'occupe de d�rouler l'arbre
function manageTree(elem, onlyOne, remember) {

	// Si les param�tres n'ont pas �t� sp�cifi�s, prend les valeurs par d�faut
	if (manageTree.arguments.length<1) onlyOne=_onlyOne;
	if (manageTree.arguments.length<2) remember=_remember;
	
	// Rep�re la division qui a g�n�r� l'event
//	divElem=event.srcElement;
	srcElem=(elem.target ?elem.target :elem.srcElement)
	if (srcElem.className!="treeView") {
	for (divElem=srcElem;divElem.className.substr(0,9)!="treeEntry";divElem=divElem.parentNode) {

		// Premi�re s�lection du noeud
		if (_oldLink==divElem) {
		 	_countClick+=1; 
		} else {
			_countClick=0;
		}
		// S'il s'agit d'un lien, le met en �vidence
		if (divElem.tagName=="A") {
			if (_oldLink!="" ) _oldLink.className="";
			divElem.className="activeLink";
			_oldLink=divElem;
		}
	}
	
	// Ferme les autres niveaux de profondeur �quivalente, uniquement si un seul niveau doit �tre visible
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
			
		// Si elle est cach�e, l'affiche
		} else if (divElem.children[i].className=="treeContent" || divElem.children[i].className=="treeLoading") {
			
			// Si la division doit �tre charg�e:
			if (divElem.children[i].className=="treeLoading") {
				// R�cup�re l'URL
				tmpURL = divElem.children[i].innerText
				divElem.children[i].innerHTML="Loading...";
				divElem.children[i].className="treeContentVisible";
				
				// Cr�e un objet XMLHttp pour aller chercher le compl�ment
				var xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
				xmlhttp.Open("POST", tmpURL, false);
				xmlhttp.Send();
				
				// Ecrit le compl�ment dans le noeud
				divElem.children[i].innerHTML=xmlhttp.responseText;				
			}
			
			// Affiche la division
			divElem.children[i].className="treeContentVisible";
			divElem.className="treeEntryLess"
			
			// Et profite pour ajuster le type des diff�rentes sections, afin d'obtenir les + et les -
			for (j=0;j<divElem.children[i].children.length; j++) {
				if (divElem.children[i].children[j].className=="treeEntryLess" && remember==false) {
					divElem.children[i].children[j].className="treeEntryMore"
				}

					// parcours encore une fois ces enfants pour voir s'il y a du contenu
				for (k=0;k<divElem.children[i].children[j].children.length; k++) {
					if (divElem.children[i].children[j].children[k].className.substr(0,11)=="treeContent" || divElem.children[i].children[j].children[k].className=="treeLoading") {
						// si c'est une simple treeEntry (donc non-initialis�)
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
