	// inclure d'autres scripts
	document.writeln("<script type='text/javascript' src='/scripts/std_project.js'></script>");

	
	// Variables globales
	var noMenu=false;
	
	function refreshMembers(orga) {
		$("#organisation_members").load("ajax/deleteuserorganisation.php?orga="+orga);
	}
	function refreshProjects(role) {
		//alert ("RefreshProjects");
		$("#project_role_"+role).load("/class/widget/wg_organisationbrowser/onglet_projets.php?action=refresh&role="+role,codeProjects);
		
		
	}
	function refreshProject(project) {
	$("#proj_"+project).load("/class/widget/wg_organisationbrowser/onglet_projets.php?action=refresh&project="+project,codeProjects);
	}
	function deleteProject(project){
		$("#proj_"+project)[0].outerHTML="";
	}


	// Lorsque le browser a été entièrement chargé, démarre les éléments jquery-ui de mise en page
	$(document).ready(function(){
		
		// Accordéon des projets
		$( ".accordion" ).accordion({collapsible: true }, {heightStyle: "content"});
		$( ".accordion:has(tr.code-empty)" ).accordion({active: false});
		$(".omo-light-accordion").accordion({collapsible: true }, {active: "false"} , {heightStyle: "content"});

		$(".omo-help-title").click(function () {
			$(this).next(".omo-more-help").toggle();
		});
		$(".omo-warning-title").click(function () {
			$(this).next(".omo-more-warning").toggle();
		});		
		// Code pour les projets
		codeProjects();
				
		// Accordéon des rôles structurels
		$( ".accordion_role" ).accordion({collapsible: true },{ active: false }, {heightStyle: "content"});
		// Fonction spéciale pour rendre les liens cliquables
		$("h3").delegate( "a", "click", function() {
	
      		window.location = $(this).attr('href');
      		return false;
   		});
  		$(".omo-light-accordion").accordion({collapsible: true }, {active: "false"} , {heightStyle: "content"});

		
		$(".omo-edit-btn").click(function() {
				document.location="editorg.php?id="+$( this ).attr("omo-param");
				return false;
		});
		
		$(".omo-delete-btn").click(function() {
				if (confirm("Êtes-vous sûr de vouloir supprimer cette organisation? Cette opération est irréversible."))
				//document.location="editorg.php?id="+$( this ).attr("omo-param");
				return false;
		});
		
		$("#omo-btn-view").click(function() {
			if ($("#select_vieworg").val()=="") {
				alert("Choisissez une organisation à afficher");
			} else {
				document.location="organisation.php?id="+$("#select_vieworg").val();
			}
		});
		
		
		$("#btn_new_org").click(function() {
				document.location="editorg.php";
				return false;
		});
		
		// Une fois tout modifié, cache l'écran de chargement
		$("#main_waiting_screen").css("display","none");
		$( document ).tooltip({
          content: function () {
              return $(this).prop('title');
          }
      });
	});
