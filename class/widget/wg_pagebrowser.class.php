<?php
	namespace widget;
	
// Cette classe affiche un browser HTML permettant de parcourir un objet de type "role" dans son
// int�gralit� : redevabilites, perimetres, raison d'etre, etc...
class wg_pageBrowser extends Widget
{
	// l'�l�ment role � afficher
	private $_page;
	
	// Constructeur n�cessitant le role � afficher
	// Entr�e : le role � afficher
	// Sortie : un objet de type wg_circleBrowser
	public function __construct(\ui\Page $page) 
	{
		$this->_page=$page;
	}
	
	public function display() {
		echo $this->_page->getContent();
	}
}