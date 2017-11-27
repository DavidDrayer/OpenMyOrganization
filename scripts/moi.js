	// Variables globales
	var noMenu=false;
	
	// Lorsque le browser a été entièrement chargé, démarre les éléments jquery-ui de mise en page
	$(document).ready(function(){
	
		var prj_menu;
		
		// Accordéon des rôles structurels
		$( ".accordion_role" ).accordion({collapsible: true },{ active: false }, {heightStyle: "content"});
		// Fonction spéciale pour rendre les liens cliquables
		$("#accordion h3").delegate( "a", "click", function() {
	
      		window.location = $(this).attr('href');
      		return false;
   		});
		
		// Accordéon des projets
		$( ".accordion" ).accordion({collapsible: true }, {heightStyle: "content"});
		$( ".accordion:has(td.code-empty)" ).accordion({active: false});
		$(".omo-light-accordion").accordion({collapsible: true }, {active: "false"} , {heightStyle: "content"});
		
		// Onglets de gauche (infos générales et liste de membres)
		$( "#tabs_gauche" ).tabs( );
		$( "#tabs_moreinfos" ).tabs( );
		// Système à onglets
			var tabs=$( "#tabs_middle" ).tabs({
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
		
		// Menu des projets éditables
		 $( ".buttonProjet" )
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
            my: "right bottom",
            at: "right top",
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
	
		
		// Une fois tout modifié, cache l'écran de chargement
		$("#main_waiting_screen").css("display","none");
	});