	// Variables globales
	var noMenu=false;
	

	function deleteProject(project){
		$("#proj_"+project)[0].outerHTML="";
	}
	
	function refreshUser() {
		
	}
	function deleteAction(id) {
		$("#acti_"+id).hide();
	}
	function refreshMembers(circle) {
		//alert ("RefreshMembers");

		$("#ui-tabs-1").load("/class/widget/wg_meetingbrowser/refresh.php?refresh=member&circle="+circle);
	}

	function refreshChecklist(circle) {
		$("#tabs-4").load("/class/widget/wg_meetingbrowser/refresh.php?refresh=checklist&circle="+circle);
	}
	
	function refreshMetrics(circle) {
		$("#tabs-5").load("/class/widget/wg_meetingbrowser/refresh.php?refresh=metrics&circle="+circle);
	}

	function refreshRole(role) {
		$("#tab_role_"+role+" h3 span.reload").load("/class/widget/wg_circlebrowser/refresh.php?refresh=role&role="+role);
		$("#tab_role_"+role+" div").load("/class/widget/wg_circlebrowser/refresh.php?refresh=roleContent&role="+role);
	}
	

	
	function checkUpdate(circle,time) {
		$.getScript("/refresh/circle.php?id="+circle+"&time="+time) .done(function( script, textStatus ) {
			  })
			  .fail(function( jqxhr, settings, exception ) {
			  	console.log (jqxhr.responseText);
			    console.log( "Triggered ajaxError handler." );
			});
	}
	
	// Lorsque le browser a été entièrement chargé, démarre les éléments jquery-ui de mise en page
	$(document).ready(function(){
	
		$(document).tooltip({
			  content: function () {
				  return $(this).prop('title');
			  }
		  });
		
		// Accordéon des politiques
		$("#accordion_pol").accordion({collapsible: true },{ active: false }, {heightStyle: "content"});
		// Accordéon des rôles structurels
		$( ".accordion_role" ).accordion({collapsible: true },{ active: false }, {heightStyle: "content"});
		// Fonction spéciale pour rendre les liens cliquables
		$("h3").delegate( "a", "click", function() {
	
      		window.location = $(this).attr('href');
      		return false;
   		});
   		
		
		// Accordéon des projets
		$( ".accordion" ).accordion({collapsible: true }, {heightStyle: "content"});
		$( ".accordion:has(tr.code-empty)" ).accordion({active: false});
		$(".omo-light-accordion").accordion({collapsible: true }, {active: "false"} , {heightStyle: "content"});
		
		// Onglets de gauche (infos générales et liste de membres)
		$( "#tabs_gauche" ).tabs( );
		// Système à onglets
			var tabs=$( "#tabs_droite" ).tabs({
				activate: function( event, ui ) {
					switch (ui.oldTab.index()) {
						case 3 : $("#menu_button_3").css("display","none"); break;
						case 4 : $("#menu_button_4").css("display","none");break;
					} 
					switch (ui.newTab.index()) {
						case 3 : $("#menu_button_3").css("display","");break;
						case 4 : $("#menu_button_4").css("display","");break;
					} 
				}
			},{
				create: function( event, ui ) {
					switch (ui.tab.index()) {
						case 3 : $("#menu_button_3").css("display","");break;
						case 4 : $("#menu_button_4").css("display","");break;
					} 
				}
			});
			tabs.on( "tabsactivate", function( event, ui ) {
				history.replaceState({page: 'toto'}, '', document.location.pathname+document.location.search+"#tabs-"+(ui.newTab.index()+1));   } );

		// Active les ToolTips, avec affichage après 1 seconde environ, et avec style de flèche
		$(".tooltip").tooltip(
			{content: function () {
              return $(this).prop('title');
          },
		 show: { delay: 800 } ,
		position: {
	        my: "center bottom-10",
	        at: "center top",
	        using: function( position, feedback ) {
	          $( this ).css( position );
	          $( "<div>" )
	            .addClass( "arrow" )
	            .appendTo( this );
	        }
	      }
	    });
	    
	
		$( "#btn_addMember").button();
		

		


		
		handlerIn = function() {
			if (noMenu==false) {
				x=$(this).attr('class').match(/add_project_([\d]+)/);
				if (x) {
					$(this).closest( "table" ).find(".add_project_"+x[1]).fadeTo(0,1);
				} else {
					$(this).closest( "table" ).find(".add_project").fadeTo(0,1);
				}
			}
			
		}
		
		handlerOut = function() {
			x=parseInt($(this).attr('class').match(/add_project_([\d]+)/) );
			if (x) {
				$(this).closest( "table" ).find(".add_project_"+x[0]).fadeTo(0,0.2);
			} else {
				$(this).closest( "table" ).find(".add_project").fadeTo(0,0.2);
			}
		}
		
		// Code pour les projets
		codeProjects();
		
		// A intégrer peut-être dans codeProjects
					$(".action-list").on("click","input", function() {
						
						if (!$(this).is(':checked')) {
							// Restaure l'état
							$(this).closest("div").stop(true,true);
							$(this).closest("div").css("text-decoration","");
							$(this).closest("div").css("color","");
							$(this).closest("div").animate({opacity:1},0);
							$(this).closest("div").removeClass("checkedimg");
						} else {
							// Cache la ligne
							$(this).closest("div").css("text-decoration","line-through");
							$(this).closest("div").animate({opacity:1},2000,function() {
								// Efface réellement (en ajax) l'élément
								$.ajax({
									url: "/ajax/project.php?action=deleteAction&id="+$(this).attr("name")
								}).done(function(data) {
									alert(data);
									 if ( console && console.log ) {
									      console.log( "Sample of data:", data.slice( 0, 300 ) );
									    }							
									});
								// Supprime l'élément visuellement
								$(this).addClass("checkedimg");
								//$(this).css("display","none")
							});
						}
					});	


		
		//$(".add_project").hover( function() {$(this).fadeTo(0,1);}, function() {$(this).fadeTo(0,0.2);}); //.click(function () { openDialog("/ajax/project.php?action=FormAddProject&role_id=1","Ajouter un projet")});


		$div=$("#code-header-project")
		$div.width($div.parent("div").outerWidth()-24);		
		$("#code-header-project table").width($div.parent("div").width()-16);		
		//$div.offset({top: $div.parent("div").offset().top, left:$div.parent("div").offset().left});		
	
		
		// Une fois tout modifié, cache l'écran de chargement
		$("#main_waiting_screen").css("display","none");
	});
