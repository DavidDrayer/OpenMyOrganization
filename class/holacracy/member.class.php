<?php
	namespace holacracy;

class Member extends User
{
		protected $_isAdmin =0 ;	// Nom du User
		
		public function isAdmin ($object = NULL) {
			return $this->_isAdmin;
		}
		public function setAdmin ($admin, $circle = NULL) {
			$this->_isAdmin=$admin;
		}

}
?>