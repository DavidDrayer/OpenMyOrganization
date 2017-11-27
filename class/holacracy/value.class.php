<?php
	namespace holacracy;


class Value extends Holacracy
{
	protected $_organisation;  		// organisation
	protected $_organisation_id;  	// organisation sous la forme d'un id 
	protected $_label;  		// texte
	private $_principles = array();
	private $_principles_loaded = false;
	
	// Fonctions pour accéder aux propriétés de l'objet (GET et SET)
	public function getLabel () {
		return $this->_label;
	}

	public function getPrinciples($donotloadDB = 0) {
		if (!$this->_principles_loaded && $this->_id>0 && !$donotloadDB) {
			$tmp_array=$this->getManager()->loadPrincipleListe($this);
			for ($i=0; $i<count($tmp_array);$i++) {
				if (!in_array($tmp_array[$i], $this->_principles)) {
					$this->_principles[]=$tmp_array[$i];
				}
			}
			$this->_principles_loaded=true;
		}
		return $this->_principles;	
	}

	public function getOrganisation () {
		if (is_null($this->_organisation) && $this->_organisation_id>0) {
			$this->_organisation=self::getManager()->loadOrganisation($this->_organisation_id);
		}
		return $this->_organisation;
	}
	public function getOrganisationId () {
		return $this->_organisation_id;
	}
	public function setLabel ($label) {
		$this->_label=$label;
	}

	public function setOrganisation ($org) {
		if ($this->_organisation_id!=$org->getId()) $this->setModified(true);
		$this->_organisation=$org;
		$this->_organisation_id=$org->getId();
	}
	public function setOrganisationId ($orgId) {
		if ($this->_organisation_id!=$orgId) $this->setModified(true);
		$this->_organisation_id=$orgId;
		$this->_organisation=null;
	}
	
}
?>
