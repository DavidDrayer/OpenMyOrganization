		<!-- Dialogue pour autre conenu -->	
		<div id="dialogStd" title="Dialogue" style='display:none'>
 		<div id='dialogLoading'><?=\widget\Widget::WAITING_SCREEN?></div>
		 <div id="dialogStdContent">Please wait...</div>
		</div>
		
		<!-- Dialogue pour retrouver son mot de passe -->
		<div id="dialogPassword" title="Mot de passe oublié"  style='display:none'>
 		 <form id='formLostPassword'>Entrez votre nom d'utilisateur ou votre e-mail:<br/><input type='text' name="user_lostPassword" id="user_lostPassword"/></form>
		</div>
		
		
		<script>
		
		 var menu;
		 
		console.log("Chargement de nav.php");
		 
		// A faire toute à la fin
		 $(document).ready(function(){
		 
  
		 // Bouton d'aide
		 $( "#help_button" )
      .button({
	  	text:false, icons: {primary: "ui-icon-info"}
	  })
      .click(function() {
      	window.open('/help.php?key='+encodeURIComponent(document.location), 'help', 'width=800, height=700');
  		return false;
      })
      
      $(".omo-help").click(function() {
      	window.open($(this).attr("href"), 'help', 'width=800, height=700');
  		return false;
      })
      
		 // Boutons des notifications
	$( "#notif_button" )
      .button({
		text: true,
	  	icons: {primary: "ui-icon-star"}
	  })
      .click(function() {
        alert( "Ouverture de la page des notifications" );
      })
      .next()
        .button({
          text: false,
          icons: {
            primary: "ui-icon-triangle-1-s"
          }
        })
        .click(function() {
          if (menu) menu.hide();
          menu = $( this ).parent().next().show().position({
            my: "right top",
            at: "right bottom",
            of: this
          });
          $( document ).one( "click", function() {
            menu.hide();
          });
          return false;
        })
        .parent()
          .buttonset()
          .next()
            .hide()
            .menu();
		
		 // Boutons du profil
	$( "#profil_button" )
      .button()
      .next()
        .button({
          text: false,
          icons: {
            primary: "ui-icon-triangle-1-s"
          }
        })
        .click(function() {
          if (menu) menu.hide();
          menu = $( this ).parent().next().show().position({
            my: "right top",
            at: "right bottom",
            of: this
          });
          $( document ).one( "click", function() {
            menu.hide();
          });
          return false;
        })
        .parent()
          .buttonset()
          .next()
            .hide()
            .menu();

	// Boutons du recherche
	$( "#search_button" )
      .button({
	  	icons: {primary: "ui-icon-search"}
	  })
      .next()
        .button({
          text: false,
          icons: {
            primary: "ui-icon-triangle-1-s"
          }
        })
        .click(function() {
          if (menu) menu.hide();
          menu = $( this ).parent().next().show().position({
            my: "right top",
            at: "right bottom",
            of: this
          });
          $( document ).one( "click", function() {
            menu.hide();
          });
          return false;
        })
        .parent()
          .buttonset()
          .next()
            .hide()
            .menu();
		
		// Dialogue pour report de bug et suggestion
		$( "#dialogReport" ).dialog({      
			autoOpen: false,
      		modal: true,
			width:620,
	      	height:475,
			buttons: {
        "Envoyer": function() {
          	$.post("ajax/report.php", $("#formReportBugSug").serialize(), function(data) {
          	if (data=="ok") {
          		// Change les boutons
          		$( "#dialogReport" ).dialog({ buttons: [ { text: "Fermer", click: function() { $( this ).dialog( "close" ); } } ] });

          		// Change le contenu du dialogue
          		$("#formReportBugSug").html("Merci pour votre participation à faire évoluer le logiciel OMO");
          	} else {
         		alert(data);
         	}
       });
        },
        "Annuler": function() {
          $( this ).dialog( "close" );
        }
      }});
		
		
		// Ouverture d'une page
		openDialog = function (url, title) {
			console.log ("Open dialog");
			// Adapte l'URL si nécessaire, en remplacant les paramètres entre crochet par la valeur de l'élément (ID)
			var myRe = /\[[\w_-]*\]/g;
			var str = url
			var myArray;
			while ((myArray = myRe.exec(str)) !== null)
			{
			  url=url.replace(myArray[0],$("#"+myArray[0].substr(1,myArray[0].length-2)).val());
			}
		
			var stdDialog = $( "#dialogStd" ).dialog({ 
				closeOnEscape: false,
    			beforeclose: function (event, ui) { return false; },
    			dialogClass: "noclose"  ,   
	      		modal: true,
	      		width:800,
	      		height:500,
	      		buttons: {"Fermer": function() {
		          stdDialog.dialog("close");
		        }}
	      	});
			stdDialog.dialog( "option" , "title" ,title);
			// Affiche l'élément de chargement
			$("#dialogStdContent").html("");
			//$("#dialogLoading").html("<?=\widget\Widget::WAITING_SCREEN?>");
			$("#dialogLoading").css("display","");
 			stdDialog.dialog( "open" );

			// Défini son contenu par ajax en le récupérant de l'url
			$("#dialogStdContent").load(encodeURI(url), function() {
 				//stdDialog.dialog( "open" );
 				// Cache l'élément de chargement
 				$("#dialogLoading").css("display","none");

			});		
		}

		 // Si c'est une première connexion, affiche une fenêtre de validation des CG
<?php 
		if (isset($_SESSION["currentUser"]) && $_SESSION["currentUser"]->getLastConnexion()=="") {
		
			// Affiche le dialogue de confirmation de l'accueil, avec les conditions générales
			echo "openDialog('/formulaires/form_welcome.php','Bienvenue dans le site OMO')";
		} else {
			// Affiche la fenêtre avec les news et les trucs et astuces
			if (isset($_SESSION["currentUser"]) && !isset($_COOKIE["hintWindow"])) {
				setcookie("hintWindow", "ok");	
				echo "openDialog('/formulaires/form_welcome.php','Saviez-vous que...')";

			}
		}	
?>

		// Opérations de type AJAX selon un url fixe
		$('body').on('click', '.ajax', function(event) {
			event.preventDefault();
			event.stopPropagation();
			if (!$(this).attr("check") || confirm($(this).attr("check"))) { 
			// Appel la fonction et affiche le résultat sous forme d'info
			$.getScript( $(this).attr("href") )
			  .done(function( script, textStatus ) {
				// A effectué le script correctement
			  })
			  .fail(function( jqxhr, settings, exception ) {
			  	console.log (jqxhr.responseText);
			    console.log( "Triggered ajaxError handler." );
			});
			}
			return false;		
		});
		
		// Différentes pages à ouvrir sous forme de dialogue
		// Utilisation : <a href="URL A OUVRIR" alt="TEXTE DU TITRE DE LA FENETRE" class="dialogPage">TEXTE</a>
		$('html').on('click', '.dialogPage', function(event) {
			event.preventDefault();
			event.stopPropagation();
			console.log ("html click");
			// Affiche un dialogue modal
			openDialog ($(this).attr("href"),$(this).attr("alt"));
		});
		$('.dialogPage').click(function(event) {
			event.preventDefault(); event.stopPropagation();
			console.log ("dialogPage click");
			openDialog ($(this).attr("href"),$(this).attr("alt"));
		});

		
		// Page à ouvrir sous forme de boîte de confirmation
		// Utilisation : <a href="URL A APPELER EN AJAX" alt="QUESTION A L'UTILISATEUR" class="confirm">TEXTE</a>
		$(".confirm").click(function(event) {
			event.preventDefault();
			// Affiche la boîte de confirmation
			if (typeof($(this).attr("alt"))== 'undefined' || confirm($(this).attr("alt"))) {
				$.ajax( $(this).attr("href")).done(function(data) {
    				location.reload();
  				});
			}
		});

		// Bouton de login
		$("#btn_login").click(function (event) {
			event.preventDefault();
			$(this).prop('disabled', true);
			var old=$(this).html();
			var elem=this;
			// fixe la valeure minimum de la largeur et de la hauteur du bouton, pour éviter le changement de taille - DDr 4.6.2014
			$(this).css("min-width",$(this).outerWidth());
			$(this).css("min-height",$(this).outerHeight());
			$(this).html("En cours...");
			$.ajax({
		       url : "ajax/login.php",
		       type : "POST",
		       data : $("#form_login").serialize(),
		       success : function(code_html, statut){ 
		           if (code_html=="ok" ) {
					   location.reload();
				   } else if (code_html=="ko" ) {
				   		document.cookie="RememberUser='';expires=Thu, 01 Jan 1970 00:00:01 GMT";
					   location.reload();
				   } else {
				   	alert(code_html);
				   	$(elem).html(old);
				   	$(elem).prop('disabled', false);
				   }
		       }
		    });
		});
		// Création du dialogue std
		$( "#dialogStd" ).dialog({      
			autoOpen: false
      	});
		
		// Dialogue de mot de passe oublié
		$( "#dialogPassword" ).dialog({      
			autoOpen: false,
      		modal: true,
			buttons: {
        "Envoyer": function() {
          	$.post("ajax/login.php", $("#formLostPassword").serialize(), function(data) {
          	if (data=="ok") {
          		// Change les boutons
          		$( "#dialogPassword" ).dialog({ buttons: [ { text: "Fermer", click: function() { $( this ).dialog( "close" ); } } ] });

          		// Change le contenu du dialogue
          		$("#formLostPassword").html("Mot de passe envoyé avec succès");
          	} else {
         		alert(data);
         	}
       });
        },
        "Annuler": function() {
          $( this ).dialog( "close" );
        }
      }});
      
      	// Ouverture du mot de passe oublié
      	$("#openFormPassword").click(function(event) {
		  	$( "#dialogPassword" ).dialog( "open" );
		  });
		  
		// Ouverture d'un report de bug
      	$("#openReportBugSug").click(function(event) {
		  	$( "#dialogReport" ).dialog( "open" );
		  });  
	});
	</script>
