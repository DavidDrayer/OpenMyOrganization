	// Variables globales
	var noMenu=false;
	
	function refreshProjects(role) {
		//alert ("RefreshProjects");
		$("#project_role_"+role).load("/class/widget/wg_rolebrowser/onglet_projets.php?action=refresh&role="+role,codeProjects);
		
		
	}
	function refreshProject(project) {
	$("#proj_"+project).load("/class/widget/wg_rolebrowser/onglet_projets.php?action=refresh&project="+project,codeProjects);
	}
	function deleteProject(project){
		$("#proj_"+project)[0].outerHTML="";
	}

	function refreshChecklist(circle) {
		$("#tabs-4").load("/class/widget/wg_rolebrowser/refresh.php?refresh=checklist&circle="+circle);
	}
	
	function refreshMetrics(circle) {
		$("#tabs-5").load("/class/widget/wg_rolebrowser/refresh.php?refresh=metrics&circle="+circle);
	}
	

	
	function codeProjects() {
			var prj_menu;
				
		// Boutons pour les projets importants
		$(".omo-project-important1").off("click").click(function(event) {
			// Défini le projet comme plus important pour moi
			var tmpVal=parseInt($(this).parent().parent().attr('id').match(/[\d]+$/));
			$.post( "/ajax/project.php", { action: "SetNoImportant", proj_id: parseInt($(this).parent().parent().attr('id').match(/[\d]+$/))})
			.done(function( data ) {
				refreshProject(tmpVal);
			});
		});
		$(".omo-project-important2, .omo-project-important3").off("click").click(function() {
				// Défini le projet comme important pour moi
			var tmpVal=parseInt($(this).parent().parent().attr('id').match(/[\d]+$/));
			$.post( "/ajax/project.php", { action: "SetImportant", proj_id: parseInt($(this).parent().parent().attr('id').match(/[\d]+$/))})
			.done(function( data ) {
				refreshProject(tmpVal);
			});
		});

			
		// Menu des projets éditables
		 $( ".buttonProjet" ).off('click')
        .button({
          text: false,
          icons: {
            primary: "ui-icon-triangle-1-s"
          }
        })
        .click(function() {
          if (menu) menu.hide();
          if (prj_menu) prj_menu.hide();
          prj_menu = $( this ).parent().next().show().position({
            my: "right top",
            at: "right bottom",
            of: this
          });
          $( document ).one( "click", function() {
            prj_menu.hide();
          });
          return false;
        })
        .parent()
          .next()
            .hide()
            .menu();	
            
 
		//var dontmove=true;
		var oldList, newList, item, scroll_interval;	
		// Nouveau code pour déplacer les éléments
		$( ".project" ).sortable({
		cancel: ".nodrag",
		connectWith: ".project",
		placeholder: "myplaceholder",
		containment: "#tabs-6",
		sort:function(event,uiHash){
			// Scroll vers le haut si possible
			if (event.pageY<$("#tabs-6").offset().top) {
				clearInterval(scroll_interval);
				scroll_interval=setInterval(function(){ $("#tabs-6").scrollTop($("#tabs-6").scrollTop()-5); }, 5);
				
			} else	if (event.pageY>$("#tabs-6").offset().top+$("#tabs-6").height()) {
				clearInterval(scroll_interval);
				scroll_interval=setInterval(function(){ $("#tabs-6").scrollTop($("#tabs-6").scrollTop()+5); }, 5);
				
			} else {
				clearInterval(scroll_interval);
			}
		},
		start: function(event, ui) {
                item = ui.item;
                newList = oldList = ui.item.parent();
            },
		change: function(event, ui) {  
                if(ui.sender) newList = ui.placeholder.parent();
            },
		stop: function(event, ui) { 
				
				clearInterval(scroll_interval);
				if (newList.closest("div").attr("role")=="no") {
					if (confirm("Voulez-vous proposer ce projet à ce rôle (nécessite une validation de la personne en charge)?")) {
					$.post( "/ajax/project.php", { action: "TransferProject", proj_position:ui.item.index() , role_id: parseInt(newList.closest("div").attr("id").match(/[\d]+$/)), proj_id: parseInt(ui.item.attr('id').match(/[\d]+$/)), typr_id: parseInt(newList.attr('class').match(/[\d]+/) )})
						.done(function( data ) {
							ui.item.remove();
							//refreshProject(parseInt(ui.item.attr('id').match(/[\d]+$/)));
							//alert( "Data Loaded: " + data );
						});	
						
					} else {
						$(this).sortable('cancel')
					}
				} else {   
					$.post( "/ajax/project.php", { action: "ChangeStatus", proj_position:ui.item.index() , role_id: parseInt(newList.closest("div").attr("id").match(/[\d]+$/)), proj_id: parseInt(ui.item.attr('id').match(/[\d]+$/)), typr_id: parseInt(newList.attr('class').match(/[\d]+/) )})
						.done(function( data ) {
							refreshProject(parseInt(ui.item.attr('id').match(/[\d]+$/)));
							//alert( "Data Loaded: " + data );
						});	
					//console.log("Déplacé de " + oldList.index() +"-"+oldList.parent().index()+ " to " + newList.index()+"-"+newList.parent().index()+"-"+item.index());
				}
            }
 

		

		 
		}).disableSelection();		
		
			$('.project_main').off('dblclick').dblclick(function(e) {
				e.stopPropagation();
				e.preventDefault();
	  			openDialog("/ajax/project.php?action=FormViewProject&proj_id="+$(this).find(".project_title").attr("proj_id"),$(this).find(".project_title").text())
				console.log("Test 2 ");
				return false;
			});		
	}
	
	function checkUpdate(role,time) {
		$.getScript("/refresh/role.php?id="+role+"&time="+time) .done(function( script, textStatus ) {
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
		
		$(".omo-help-title").click(function () {
			$(this).next(".omo-more-help").toggle();
		});
		$(".omo-warning-title").click(function () {
			$(this).next(".omo-more-warning").toggle();
		});
				
		// Accordéon des politiques
		$(".accordion_pol").accordion({collapsible: true });
		// Accordéon des rôles structurels
		$( ".accordion_role" ).accordion({collapsible: true },{ active: false }, {heightStyle: "content"});
		// Fonction spéciale pour rendre les liens cliquables
		$("#accordion h3").delegate( "a", "click", function() {
	
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
						case 4 : $("#menu_button_5").css("display","none"); break;
						case 3 : $("#menu_button_4").css("display","none");break;
					} 
					switch (ui.newTab.index()) {
						case 4 : $("#menu_button_5").css("display","");break;
						case 3 : $("#menu_button_4").css("display","");break;
					} 
				}
			},{
				create: function( event, ui ) {
					switch (ui.tab.index()) {
						case 2 : $("#menu_button_3").css("display","");break;
						case 3 : $("#menu_button_4").css("display","");break;
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
					$(".project .action-list").on("click","input", function() {
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
