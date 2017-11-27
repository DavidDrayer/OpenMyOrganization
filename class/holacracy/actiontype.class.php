<?php
	namespace holacracy;

class ActionType extends Holacracy
{
	private $_description ;	// Description du type d'action
	private $_form ;	// Nom du formulaire de traitement
	
	public function __construct()
	{
	    $ctp = func_num_args();
	    $args = func_get_args();
	    switch($ctp)
	    {
	        case 1:	// Un seul argument, c'est le manager
	            $this->_construct1($args[0]);
	            break;
	        case 4:	// Deux arguments, manager et ID pour l'initialisation des données
	            $this->_construct4($args[0],$args[1],$args[2],$args[3]);
	            break;
	         default:
	            break;
	    }
	}
	
	// Constructeur vide, mais objet lié à un manager
	private function _construct1 ($manager) {
		$this->_manager= $manager;
	}
	
	// Constructeur lié à la base de donnée, et initialisé à partir de la base de donnée
	public function _construct4 ($manager, $id, $description,$form) {
		$this->_manager=$manager;
		$this->_id=$id;
		$this->_description=$description;
		$this->_form=$form;
	}
	
	public function getDescription () {
		return $this->_description;
	}

	public function getForm () {
		return $this->_form;
	}

	public function getId () {
		return $this->_id;
	}
}
?>