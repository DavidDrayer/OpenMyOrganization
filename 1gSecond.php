<? include('lib/navLib.php') ?>


<script>
	function my_scrollTo (target) {
		var body = $("html, body");
		body.animate({scrollTop:$(target).offset().top-60}, '500', 'swing');
	}
		
    $(document).ready(function() {
			$(".loginmenu").css("display","none");

		
			$(".popupmenu").click(function() {
			$(".loginmenu").css("display","none");
			$(".content-inside").css("padding-left","5px");
		});
		$(".popupmenu2").click(function() {
			$(".loginmenu").css("display","");
			$(".content-inside").css("padding-left","");
		});
		
				// Bouton de login
		$("#btn_login2").button().click(function (event) {
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
					   location="login.php";
				   } else if (code_html=="ko" ) {
				   		document.cookie="RememberUser='';expires=Thu, 01 Jan 1970 00:00:01 GMT";
					   location="index.php";
				   } else {
				   	alert(code_html);
				   	$(elem).html(old);
				   	$(elem).prop('disabled', false);
				   	location="login.php";
				   }
		       }
		    });
		});
			
		
		$('.footer').scrollToFixed( {
            bottom: 0,
            limit: function() { return $('.footer_anchor').offset().top},
            preFixed: function() { $(this).addClass( "ombre_haut" ); },
            postFixed: function() { $(this).removeClass("ombre_haut"); },
 
      });

        $('.header').scrollToFixed({dialogClass: 'dlgfixed',
    position: "center",
				marginTop:38,
			});
        $('.header').bind('fixed.ScrollToFixed', function() { 
	
				});


        $('.header').bind('unfixed.ScrollToFixed', function() { 

				 });

		$('#btn_login').button().click(function() {
			document.location='/login.php';
		});
		
		$('#btn_new_account').button().click(function() {
			$( "#form-account" ).load('/formulaires/form_newaccount.php');
			$( "#dialog-account" ).dialog( "open" );
			
		});
		
		$( "#form-account" ).submit(function() {
			
			// Envoie le formulaire en AJAX (méthode POST), la destination étant définie par l'élément à l'ID form_target 
			$.post($("#form-account #form_target")[0].value, $("#form-account").serialize()) 
				.done(function(data, textStatus, jqXHR) {
					if (textStatus="success")
					{
						// Traite une éventuelle erreur
						if (data.indexOf("Erreur")>0) {
							eval(data);
						} else {
							// Affiche les données en retour en remplacement du contenu du formulaire (le contenant reste) 
							$("#form-account")[0].innerHTML=data;
							// Intérprète les scripts retournés (à vérifier si ça fonctionne)
							eval($("#form-account").find("script").text());
						}
					}
					else {
						// Problème d'envoi
						alert("Echec!");
					
					}
				}).fail(function() {alert ("Problème d'envoi");});
				// Bloque la procédure standard d'envoi
				return false;			

		});
		
		dialogAccount = $( "#dialog-account" ).dialog({
		  autoOpen: false,
		  dialogClass: 'fixed-dialog',
		  height: 400,
		  width: 550,
		  modal: true,
		  buttons: {
			"Créer le compte": function() {
			  $( "#form-account" ).submit();
			},
			"Fermer": function() {
			  $( "#dialog-account" ).dialog( "close" );
			}
		  },
		  close: function() {
		  }
		});
		

   
    });

</script>
		<div style='height:38px;'></div>
<div class="topheader">
	<div id="dialog-account" class='dialog' title="Créer un nouveau compte">	
	<form name='form-account' id='form-account'>
	</form>
</div>
	<div class="topheader-inside">
		<img src="images/titre_omo.png"/>
	</div>
</div>

       <div class="header"><div class="header-inside">
            <div id='secondlogo'><img src='images/uploads/logo2.png'></div>
			<div class="mainNav"><a href="/">Accueil</a><? displayNav('child','', 2, getRoot($page,1));?></div>
			<div style='background:#FFFFFF; z-index:100;position:absolute;padding-top: 10px;' class='popupmenu loginmenu'>Login</div><div class='loginmenu' style='box-sizing:border-box;padding:10px;position:absolute;z-index:99;box-shadow: 0 0 7px rgba(0,0,0,0.5);background:#FFFFFF; width:300px; height:200px; margin-top:30px; border-radius: 0px 30px 10px 30px'>
<?		

				echo "<form id='form_login'><table border=0 cellspacing=0 cellpadding=0><tr><td style='vertical-align:middle'>User:<br/><input name='user_login' id='user_login' type='text'><br/>Password:<br/><input name='user_password' id='user_password' type='password'> <button id='btn_login2'>Se Connecter</button> <input type='checkbox' id='remember_me' name='remember_me' value='remember_me' style='vertical-align:text-top'><span style='font-size:80%'> Se souvenir de moi</font></td></tr>";
				echo "<tr><td><span id='password_refresh'><br/><a href='inscription.htm' >&gt; Créer un compte</a></span></td></tr></table></form>";
?>					

			</div>
			<div class="secondNav"><a href="" onclick='return false;' class='popupmenu2'>Login</span><a href="http://demo.openmyorganization.com"><span class="long_menu">Découvrez maintenant!</span></a></div>			
			

        </div></div>

        <div class="content"><div class="content-inside">

					<div class="path">Vous êtes ici: <? displayNav('path',' &gt; ', 1, getRoot($page,1));?></div>
				
				
				<?writeZone($dbh, $page.'_1', "txt_zone zone1",true);?>     
                
				<?writeZone($dbh, $page.'_8', "col2_1 ",true);?>     
				<?writeZone($dbh, $page.'_9', "col2_2 ",true);?>     
                <div style="clear:both"></div>
			</div>
        </div></div>
        <div class='footer_anchor'></div>
        <div class="footer"><div class="footer-inside">
			<table style='width:100%;'><tr><td style='width:100%;'>
            <?writeZone($dbh, '1_4', "footer_txt",true);?>   
            </td>
            <td id='module_partage' style='white-space: nowrap; text-align:right'>
				<div class="thirdnav"><? displayNav('child','', 3, getRoot($page,1));?></div>			</td>
			</tr>
			</table>
        </div></div>
			<div align="center" class="signature"><div align="center" class="signature-inside">Dernière mise à jour : <? writeDate(); ?> | Design (c) 2015 &gt; <a href="javascript:loadEditor()">Powered by Liquid Edition</a></div></div>


    
  


