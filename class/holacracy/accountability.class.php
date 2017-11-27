<?php
	namespace holacracy;
	
// Classe Cercle, repr�sentant un cercle poss�dant plusieurs r�les
class Accountability  extends Holacracy
{
	private $_description = "No description";
	private $_role;								// Role associ� � cette redevabilit� 
	private $_role_id;							// ID du r�le associ�


	// Constructeur � 3 arguments: manager, id et texte de base
	private function _construct3 ($manager, $id) {
		$this->_manager=$manager;
		$this->_id=$id;
	}
	
	// Liste des GETTER
	public function getDescription() {
		return $this->_description;
	}
	
	public function setDescription($description) {
		$this->_description=$description;
	}
	
	// Permet de retourner l'ID du r�le d�fini sans avoir besoin de charger l'objet enti�rement
	public function getRoleId() {
		return $this->_role_id;
	}
	
	public function getRole() {
		if (isset($this->_role)) {
			return $this->_role;
		} else {
			if (isset($this->_role_id)) {
				$this->_role=$this->getManager()->loadRole($this->_role_id);
				
				return $this->_role;
				
			} else {
				return NULL;
			}
		}		
	}
	
	public function setRole($role) {
			if (is_numeric($role)) {
			$this->_role_id=$role;	
		} else {
			$this->setManager($role->getManager());
			$this->_role=$role;
			$this->_role_id=$role->getId();
		}
	}
	
}
?>
	