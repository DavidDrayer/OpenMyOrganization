<?php
	namespace holacracy;

class Status extends Holacracy
{
	private $_label;	// Libellé du Status
	private $_color;	// Couleur d'affichage
	
	function getLabel() {
		return $this->_label;
	}
	
	function getColor() {
		return $this->_color;
	}
	
	function setLabel($label) {
		$this->_label=$label;
	}
	
	function setColor($color) {
		$this->_color=$color;
	}
}