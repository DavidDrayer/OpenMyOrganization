<?php
	namespace holacracy;

class Document extends Holacracy
{
	private $_title;	// Nom de l'action
	private $_description;	// Description de l'action
	private $_name;
	private $_file;
	private $_url;
	private $_url_edition;
	private $_role;
	private $_role_id;
	private $_user;
	private $_user_id;
	private $_creation_date;
	private $_modification_date;
	private $_delete_date;
	private $_visibility;
	
	
	// Visibilité du document (public, org, cercle, rôle,...)
	public function getVisibility() {
		return $this->_visibility;
	}
	
	public function setVisibility($visibility) {
		$this->_visibility=$visibility;
	}


	public function setRole($object) {
		if (is_object($object)) {
			$this->_role=$object;
			$this->_role_id=$object->getId();
		} else {
			$this->_role_id=$object;
		}
	}
	
	public function getRoleId() {
		return $this->_role_id;
	}
	
	public function getRole() {
		if (!isset($this->_role)) {
			if (isset($this->_role_id)) {
				$this->_role=self::getManager()->loadRole($this->_role_id);
			} 
		}
		return $this->_role;
	}
	
	public function setUser($object) {
		if (is_object($object)) {
			$this->_user=$object;
			$this->_user_id=$object->getId();
		} else {
			$this->_user_id=$object;
		}
	}
	
	public function getUserId() {
		return $this->_user_id;
	}
	
	public function getUser() {
		if (!isset($this->_user)) {
			if (isset($this->_user_id)) {
				$this->_user=self::getManager()->loadUser($this->_user_id);
			} 
		}
		return $this->_user;
	}
	
	
	public function getTitle() {
		return $this->_title;
	}
	
	public function getDescription () {
		return $this->_description;
	}
	
	public function getName() {
		return $this->_name;
	}
	
	public function getFile () {
		return $this->_file;
	}
	
	public function setTitle($title) {
		$this->_title=$title;
	}
	
	public function setDescription ($description) {
		$this->_description=$description;
	}

	public function setName($name) {
		$this->_name=$name;
	}
	
	public function setFile ($file) {
		$this->_file=$file;
	}

	public function getURL() {
		return $this->_url;
	}
	
	public function getEditURL () {
		return $this->_url_edition;
	}
	
	public function setURL($url) {
		$this->_url=$url;
	}
	
	public function setEditURL ($url) {
		$this->_url_edition=$url;
	}

	public function setDeleteDate($date) {
		$this->_delete_date=$date;
	}
	public function getDeleteDate() {
		return $this->_delete_date;
	}
		
	public function delete($bool=true) {
		$this->_delete_date=getdate();
	}
	
	public function isDelete() {
		return (isset($this->_delete_date) && $this->_delete_date!="");
	}
	
	public function setModificationDate($date) {
		$this->_modification_date=$date;
	}
	
	
	public function getModificationDate() {
			return $this->_modification_date;
	}
	

	public function setCreationDate($date) {
		$this->_creation_date=$date;
	}	
	
	public function getCreationDate() {
		return $this->_creation_date;
	}
}
?>
