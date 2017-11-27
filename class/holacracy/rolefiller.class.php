<?php
	namespace holacracy;


class RoleFiller extends User
{
	private $_focus;
	private $_user_id;
	private $_role_id;



	public function getFocus() {
		return $this->_focus;
	}  
	public function setFocus($focus) {
		$this->_focus=$focus;
	}  
	public function getUserId() {
		return $this->_user_id;
	}
	
	public function setUserId($id) {
		$this->_user_id=$id;
	}
	public function getRoleId() {
		return $this->_role_id;
	}
	public function setRoleId($id) {
		$this->_role_id=$id;
	}
	
	public function delete() {
		/* Laissé tombé pour le moment: la fonction "checkIntegrity" doit suffire.
		$projects = $this->getManager()->loadRole($this->getRoleId())->getProjects();
		foreach ($projects as $project) {
			if ($project->getUserId()==$this->getUserId()) {
				$project->setUser(NULL);
				$this->getManager()->save($project);
			}
		}*/
	}
}
?>
