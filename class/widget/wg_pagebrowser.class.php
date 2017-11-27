<?php
	namespace widget;
	
// Cette classe affiche un browser HTML permettant de parcourir un objet de type "role" dans son
// intégralité : redevabilites, perimetres, raison d'etre, etc...
class wg_pageBrowser extends Widget
{
	// l'élément role à afficher
	private $_page;
	
	// Constructeur nécessitant le role à afficher
	// Entrée : le role à afficher
	// Sortie : un objet de type wg_circleBrowser
	public function __construct(\ui\Page $page) 
	{
		$this->_page=$page;
	}
	
	public function display() {
		echo $this->_page->getContent();
	}
}