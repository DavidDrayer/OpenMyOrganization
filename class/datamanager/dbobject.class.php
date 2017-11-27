<?php
	namespace datamanager;

class DbObject
{
	private static $_manager;					// Manager de l'objet
	protected $_id;							// ID de l'�l�ment dans la base
	private $_hash;
	private $_active=true;
	private $_modified=false;

	public static function setManager(\datamanager\genericmanager $manager) {

		self::$_manager=$manager;
	}
	public static function getManager() {
		

		return self::$_manager;
	}

	// constructeur g�n�rique orientant sur un constructeur avec le bon nombre de param�tres (_construct1, _construct2, _construct3,...)
	public function __construct()
	{
		$this->_hash=spl_object_hash($this);
		// Constructeur g�n�rique appelant le constructeur avec le bon nombre de param�tres.
	    $ctp = func_num_args();
	    $args = func_get_args();
	    $paramString="";
	    for ($i=0; $i<$ctp; $i++) {$paramString.=($i>0?",":"")."\$args[$i]";}
	    eval("\$this->_construct$ctp(".$paramString.");");
	}

	// constructeur g�n�rique sans param�tre (aucune action)
	private function _construct0() {
	}
	
	// constructeur g�n�rique � 2 param�tres (manager et id)
	private function _construct1($manager) {
		self::$_manager=$manager;
	}
	
	// constructeur g�n�rique � 2 param�tres (manager et id)
	private function _construct2($manager, $id) {
		self::$_manager=$manager;
		$this->_id=$id;
	}

	public function getId() {
		return $this->_id;
	}
	public function setId($id=null) {
		$this->_id=$id;
	}
	public function isActive() {
		return $this->_active;
	}
	public function setActive($active) {
		$this->_active=$active;
	}
	public function delete() {
		$this->_active=false;
		$this->_id=null;
	}
	public function checkForSave() {
		return false;
	}
	public function getChilds() {
	}
	// Fonction pour la sauvegarde ou pas
	public function isModified() {
		return $this->_modified;
	}
	public function setModified($bool) {
		$this->_modified=$bool;
	}
	

}
?>
