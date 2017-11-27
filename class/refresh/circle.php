console.log("Mise � jour...");
<?
	// Session et environnement
	include_once("../include.php");
 	$currentTime=new DateTime();
	// Optient la liste des projets qui ont �t� modifi�s dans les 10 derni�res secondes
	$circle=$_SESSION["currentManager"]->loadCircle($_GET["id"]);
	$roles=$circle->getRoles();
	$refresh_roleProject=array();
	$refresh_project=array();
	foreach ($roles as $role) {
		$projects=$role->getProjects(\holacracy\Project::ALL_PROJECTS);
		foreach ($projects as $project) {
			// Le projet a-t-il �t� cr�� ou d�plac�?
			// echo $project->getCreationDate()->format("d.m.Y H:i:s")." : ".($currentTime->getTimestamp()-$project->getCreationDate()->getTimestamp())." - ";
			if ($currentTime->getTimestamp()-$project->getCreationDate()->getTimestamp()<=$_GET["time"] || ($project->getStatusDate()!="" && $currentTime->getTimestamp()-$project->getStatusDate()->getTimestamp()<=$_GET["time"])) {
				// Projet cr�� dans l'intervale
				$refresh_roleProject[$project->getRole()->getId()]="true";
			} else 	if (($project->getModificationDate()!="" && $currentTime->getTimestamp()-$project->getModificationDate()->getTimestamp()<=$_GET["time"])) {
				// Projet cr�� dans l'intervale
				$refresh_project[$project->getId()]="true";
			} 
			// Le projet a-t-il �t� modifi�?
		}
	}
	foreach ($refresh_roleProject as $key => $rp) {
		echo "refreshProjects(".$key.");";
	}
	foreach ($refresh_project as $key => $rp) {
		echo "refreshProject(".$key.");";
	}
	// Pour chaque �l�ment de la liste, cr�e le code ad�quat
 	
?>