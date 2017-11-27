		<!-- Dialogue pour autre conenu -->	
		<div id="dialogStd" title="Dialogue" style='display:none'>
 		<div id='dialogLoading'><?=\widget\Widget::WAITING_SCREEN?></div>
		 <div id="dialogStdContent">Please wait...</div>
		</div>
		
		<!-- Dialogue pour retrouver son mot de passe -->
		<div id="dialogPassword" title="Mot de passe oublié"  style='display:none'>
 		 <form id='formLostPassword'>Entrez votre nom d'utilisateur ou votre e-mail:<br/><input type='text' name="user_lostPassword" id="user_lostPassword"/></form>
		</div>

		<!-- Dialogue pour payer -->
		<div id="dialogPay" title="Soutenez OMO"  style='display:none'>
			Merci pour votre soutien. Nous donnons le meilleur de nous-même pour mettre à disposition de tous un outil permettant d'offrir une transparence complète au sein de votre organisation.<br/><b>Veuillez choisir le montant de votre contribution:</b><br/>&nbsp;
 		<div style='text-align:center;'>

		
		<form action="https://www.paypal.com/cgi-bin/webscr" id='formPay' method="post" target="popupPay">
		<input type="hidden" name="cmd" value="_donations">
		<input type="hidden" name="business" value="VSVXYMPB9FV6S">
		<input type="hidden" name="lc" value="CH">
		<input type="hidden" name="item_name" value="<?=utf8_encode("Soutien au developpement de OMO")?>">
		<input type="hidden" name="item_number" value="12-23-DavidD">
		<select  name="amount" style='width:60%; text-align:center;'>
		<option value="10.00">10 CHF</option>
		<option value="25.00">25 CHF</option>
		<option value="50.00">50 CHF</option>
		<option value="100.00" selected>100 CHF</option>
		<option value="250.00">250 CHF</option>
		<option value="500.00">500 CHF</option>
		<option value="1000.00">1000 CHF</option>
		</select><br/>
		<input type="hidden" name="currency_code" value="CHF">
		<input type="hidden" name="no_note" value="1">
		<input type="hidden" name="no_shipping" value="1">
		<input type="hidden" name="rm" value="1">
		<input type="hidden" name="return" value="http://www.openmyorganization.com/success.htm?id=23">
		<input type="hidden" name="bn" value="PP-DonationsBF:btn_donateCC_LG.gif:NonHosted">
		<input type="image" src="https://www.paypalobjects.com/fr_FR/CH/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal, le réflexe sécurité pour payer en ligne">
		<img alt="" border="0" src="https://www.paypalobjects.com/fr_FR/i/scr/pixel.gif" width="1" height="1">
		</form>

		
		</div>
 
 		</div>
		
		
		<script>
		
		 var menu;
		var myVar;
		 
		// A faire toute à la fin
		 $(document).ready(function(){	
			 
			 $("#chk_nomorewindow").click(function () {
				$.ajax({
				  url: "/ajax/crowdfunding.php?action=check",
				})					
			 });
			 
			 $("#formPay").submit(function () {
					w=800; h=640;
				     var y = window.top.outerHeight / 2 + window.top.screenY - ( h / 2)
					var x = window.top.outerWidth / 2 + window.top.screenX - ( w / 2)
					var myWindow = window.open("", "popupPay", 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+w+', height='+h+', top='+y+', left='+x);
					$( "#dialogPay" ).dialog("close");
				 //var myWindow = window.open("", "popupPay", "width=800,height=640");
			 });
			 $("#btn_paymore").click(function () {
				 window.open("crowdfunding.php", '_blank');
			 });
			 $("#btn_pay").click(function () {
				 
				var payDialog = $( "#dialogPay" ).dialog({ 
					closeOnEscape: false,  
					modal: true,
					width:400,
					height:300,
					buttons: {"Fermer": function() {
					  payDialog.dialog("close");
					}}
				});
				payDialog.dialog( "open" );

			 });
			 $(".getmoney_text").mouseenter(function() {clearTimeout(myVar)});
			 $(".getmoney_text").mouseleave(function() {myVar = setTimeout(function () {clearTimeout(myVar);$(".getmoney").animate({top: 0-$(".getmoney_text").outerHeight()}, 1000);}, 1000);});
			 
<? if (!isset($_COOKIE["noCrowdfunding"])) { ?>
			 myVar = setTimeout(function () {$(".getmoney").animate({top: 0-$(".getmoney_text").outerHeight()}, 1000);}, 2500);
<? } else { ?>
	$(".getmoney").css({ top: (-$(".getmoney_text").outerHeight())+'px' });
<? } ?>			 
			 //$(".getmoney").animate({top: "0"}, 2500).animate({top: 0-$(".getmoney_text").outerHeight()}, 1000);
			 $(".getmoney_onglet").click(function() {
				$(".getmoney").animate({top: -$(".getmoney_text").offset().top-$(".getmoney_text").outerHeight()}, 1000);			 
			 });
			$("#nav_select").change (function() {

				if ($(this).val().substr(0, 2)=="c_") {
					document.location="/circle.php?id="+$(this).val().substr(2);
				} else if ($(this).val().substr(0, 2)=="r_") {
					document.location="/role.php?id="+$(this).val().substr(2);
				} else {
					document.location="/circle.php?id="+$(this).val();
				}
			});		
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
      
      $('#map_onglet').click(function() {

		if ($( "#circle_map" ).position().left<100) {
		    $( "#circle_map" ).animate({
				left: '100%'
			  }, 500, function() {
				// Animation complete.
			 });

		} else {
		    $( "#circle_map" ).animate({
				left: '50'
			  }, 500, function() {
				// Animation complete.
			 });

		}
	});
      
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
		
		var tryAjax=0;
		
		function loopAjax(url) {
			tryAjax+=1;
			// Nouvelle fonction avec timeout
			$.ajax({
				url: url,
				error: function(jqXHR, textStatus){
					if(textStatus === 'timeout')
					{     
						//Try again
						if (tryAjax>5) {
						
							alert('Connexion lente: abandon après '+tryAjax+' tentatives');
							tryAjax=0;
							$( "#dialogStd" ).dialog( "close" );
						 } else {
							 loopAjax(url);
						 }         
						
					} else {
						alert ('Problème de chargement du formulaire');
						tryAjax=0;
						$( "#dialogStd" ).dialog( "close" );
					}

				},
				success: function(response, status, xhr){
					tryAjax=0;
					$("#dialogStdContent").html(response)
					$("#dialogLoading").css("display","none");
				},
				timeout:500+tryAjax*100 //1 second timeout
			});				
		};
		
		// Ouverture d'une page
		openDialog = function (url, title) {
			console.log ("Open dialog");
			// Adapte l'URL si nécessaire, en remplacant les paramètres entre crochet par la valeur de l'élément (ID)
			url+=(url.indexOf("?")>0?"&":"?")+"display=3";
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

			loopAjax(encodeURI(url));

		

			// Défini son contenu par ajax en le récupérant de l'url
			/* $("#dialogStdContent").load(encodeURI(url), function(response, status, xhr) {
				if ( status == "error" ) {
					alert ('Problème de chargement du formulaire');
					stdDialog.dialog( "close" );
				} else {
 					// Cache l'élément de chargement
					$("#dialogLoading").css("display","none");
				}

			});	*/	
		}

		 // Si c'est une première connexion, affiche une fenêtre de validation des CG
<?php 
		if (isset($_SESSION["currentUser"]) && $_SESSION["currentUser"]->getLastConnexion()=="") {
		
			// Affiche le dialogue de confirmation de l'accueil, avec les conditions générales
			//echo "openDialog('/formulaires/form_welcome.php','Bienvenue dans le site OMO')";
		} else {
			// Affiche la fenêtre avec les news et les trucs et astuces
			if (isset($_SESSION["currentUser"]) && !isset($_COOKIE["hintWindow"])) {
				setcookie("hintWindow", "ok");	
				//echo "openDialog('/formulaires/form_welcome.php','Saviez-vous que...')";

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
			lien=$(this).attr("href");
			lien+="&display=3";
			openDialog (lien,$(this).attr("alt"));
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
