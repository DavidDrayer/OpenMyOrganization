function concatContent(event) {
	if (event.srcElement.parentNode.parentNode.childNodes[2].className!="menuContent") {
		event.srcElement.parentNode.parentNode.childNodes[2].className="menuContent"
		event.srcElement.src="images/icons/collapseDown.gif"
	} else {
		event.srcElement.parentNode.parentNode.childNodes[2].className="ongletContent"
		event.srcElement.src="images/icons/collapseUp.gif"
	}
}

function concatMenu(event) {
	if (event.srcElement.parentNode.parentNode.className=="menu") {

		event.srcElement.parentNode.parentNode.className="menuMinimize"
		event.srcElement.src="images/icons/collapseDown.gif"
	} else {
		event.srcElement.parentNode.parentNode.className="menu"
		event.srcElement.src="images/icons/collapseUp.gif"
	}
}

function concatTable(event) {
	if (event.srcElement.parentNode.parentNode.className=="table") {

		event.srcElement.parentNode.parentNode.className="tableMinimize"
		event.srcElement.src="images/icons/collapseDown.gif"
	} else {
		event.srcElement.parentNode.parentNode.className="table"
		event.srcElement.src="images/icons/collapseUp.gif"
	}
}

function afficherOnglet(elem, name, numero) {

	// cache tous les onglets de ce type
	for (i=1; i<=10;i++) {
		if (eval("self."+name+i)) {
			eval("self."+name+i).className='ongletContenuCache';
		}
	}
	
	// déplace le tabulateur de l'onglet à la bonne place et change son texte
	eval(name+numero).getElementsByTagName("DIV")[0].style.left=115*(numero-1)
	eval(name+numero).getElementsByTagName("DIV")[0].innerHTML=elem.innerHTML
	
	// affiche l'onglet en question
	document.getElementById(name+numero).className='ongletContenuVisible';
	
	// fin
	return false;
}
