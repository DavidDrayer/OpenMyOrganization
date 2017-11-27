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
