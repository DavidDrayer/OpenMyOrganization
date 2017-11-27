<?php
	namespace holacracy;

class Gouvernance extends Holacracy
{

	private $_date; 						// Date
	private $_circle;   					// Cercle convoquant la réunion
	
	private $_tensions = array();						// Liste des tensions associciées à cette réunion
	
	public function __construct()
	{
	    $ctp = func_num_args();
	    $args = func_get_args();
	    switch($ctp)
	    {
	        case 2:	// Un seul argument, c'est le manager
	            $this->_construct2($args[0],$args[1]);
	            break;
	         default:
	            break;
	    }
	}
	
	// Constructeur vide, mais objet lié à un manager
	private function _construct2 ($manager,$id) {
		$this->_manager= $manager;
		$this->_id= $id;
	}
	
	public function getDescription () {
		return $this->_description;
	}

	public function getDate () {
		return $this->_date;
	}
	public function setDate ($date) {
		$this->_date=$date;
	}
	
	public function attachTo($circle) {
		$this->_manager=$circle->getManager();
		$this->_circle=$circle;
		$this->_circleId=$circle->getId();
	}
	public function getCircle() {
		if (!is_null($this->_circle)) {
			return $this->_circle;
		} else {
			if (!is_null($this->_circleId)) {
				$this->_circle=$this->_manager->loadCircle($this->_circleId);
				return $this->_circle;
			} 
		}
	}
	
	public function getTension() {
			if (count($this->_tensions)==0) {
			$this->_tensions=$this->_manager->loadTension($this);	
		}
		return $this->_tensions;	
	}	
}
?>