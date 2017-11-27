<? include('lib/navLib.php') ?>

		<script src="/js/mark/jquery.mark.js" charset="UTF-8"></script>
		<style>
			body {font-family: Arial, Helvetica, sans-serif;}
				#tm { width:350px;height:100%; min-height:300px; max-height:800px; margin:20px; margin-top:0px; background:url(img/tm_bkg.jpg) no-repeat center center #78a2bb; background-size:cover; box-shadow: 3px 3px 5px rgba(0,0,0,0.2);  padding-top:100px; box-sizing:border-box}
				#tm_header {position:absolute; top:0px; padding:20px;}
				#tm_index { overflow-y:auto; overflow-x: hidden; font-weight:bold; font-size: 120%; width:100%;height:100%; text-shadow: 1px 2px #000000; padding-left:40px; box-sizing:border-box}
				.tm_h2 {display:none; font-weight:100; padding-left:10px; font-size:80% ; text-shadow:none}
				.tm_h3 {padding-left:10px; font-size:90%}
				#tm_cursor {position:absolute; left:-10px; top:0px; width:64px; height:43px; background:url(img/curseur.png); z-index:99}
				#tm a { display:block;text-decoration:none; color:#FFFFFF;  }
				#tm .tm_h2 a {color:#000000;white-space: nowrap;width:100%;text-overflow:ellipsis;overflow: hidden;}
				mark:focus {background:#00FF00}
		</style>
		<script>
			$(function() {
				// Crée la zone pour ajouter la table des matière
				$("#tm").css('position','absolute');
				$("#tm").css('left','0px');
				$( "<a name='tm_pos' id='tm_pos'></a>" ).insertBefore( "#tm" );
				
				//$("body").prepend("<a name='tm_pos' id='tm_pos'></a><div id='tm' style=' position:absolute;  left:0px;'></div>");
				
				// Header
				$("#tm").append("<div id='tm_header'>Table des matière</div>");
				
				// Ajoute le champ de recherche
				$("#tm_header").append("<div id='tm_search'><input id='tm_search_txt'><input type='button' value='find' id='tm_search_next'><div id='tm_search_occurence'></div></div>");

				// Ajoute un curseur
				$("#tm").append("<div id='tm_cursor'></div>");
			
				// ajoute la zone de l'index
				$("#tm").append("<div id='tm_index'></div>");
				
				// Lit tous les titre
				$( "#texte h1" ).each(function( index ) {
					// Transforme chaque titre en ancre
					$(this).prepend("<a name='idx_"+index+"'></a>");
					// Ajoute l'entrée à la table des matière
					$("#tm_index").append("<a style='display:block' id='tm_idx_"+index+"' href='#idx_"+index+"'>"+$(this).text()+"</a>");
					// ajoute un bloc pour le détail
					$("#tm_index").append("<div class='tm_h2' id='tm_h2_"+index+"'></div>");
					// Parcours tous les élément h2 jusqu'à la prochaine entrée
					$(this).nextUntil("#texte h1","h2").each(function(index2) {
						$(this).prepend("<a name='idx_"+index+"_"+index2+"'></a>");
						// Ajoute l'entrée à la table des matière
						$("#tm_h2_"+index).append("<a style='display:block' href='#idx_"+index+"_"+index2+"' id='tm_idx_"+index+"_"+index2+"'>"+$(this).text()+"</a>");
						// ajoute un bloc pour le détail (niveau 3)
						$("#tm_h2_"+index).append("<div class='tm_h3' id='tm_h3_"+index+"_"+index2+"'></div>");
						// Parcours tous les élément h2 jusqu'à la prochaine entrée
						$(this).nextUntil("h2","h3").each(function(index3) {
							$(this).prepend("<a name='idx_"+index+"_"+index2+"_"+index3+"'></a>");
							// Ajoute l'entrée à la table des matière
							$("#tm_h3_"+index+"_"+index2).append("<a style='display:block' href='#idx_"+index+"_"+index2+"_"+index3+"' id='tm_idx_"+index+"_"+index2+"_"+index3+"'>"+$(this).text()+"</a>");

						});
					});


				});
				setCursor();
				$("#tm_search_next").click(function () {
					tm_search_pos+=1;
					if (tm_search_pos>=$("#texte mark").length) tm_search_pos=0;
					elem=$("#texte mark")[tm_search_pos];
					$(window).scrollTop($(elem).position().top-50);
					$(elem).focus();
				});
				
				$("#tm_search_txt").keyup(function() {
					if ($(this).val().length>1) {
					$("#texte").unmark();
					$("#texte").mark($(this).val(), {
						"accuracy": "complementary",
						"ignoreJoiners": true,
						"acrossElements": true,
						"separateWordSearch": false
					});
					tm_search_pos=-1;
					$("#texte mark").attr("tabindex","0")
					$("#tm_search_occurence").text($("#texte mark").length+" occurences");
				} else {
					$("#texte").unmark();
					$("#tm_search_occurence").text("Tappez le texte à chercher...");
				}
				});
								
				$( window ).scroll(function() {
					if ($("#tm_pos").position().top<$(window).scrollTop()) {
						$("#tm").css('position','fixed');
						$("#tm").css('top','0px');
					} else {
						$("#tm").css('position','absolute');
						$("#tm").css('top','');
					}
					setCursor();
				});
				$("#tm_index").scroll(function() {
					setCursor2();
				});
				
<?
	if (isset($_GET["q"])) {
?>
				$("#texte").mark("<?=$_GET["q"]?>", {
					"accuracy": "complementary",
					"ignoreJoiners": true,
					"acrossElements": true,
					"separateWordSearch": false
				});
				tm_search_pos=-1;
				$("#texte mark").attr("tabindex","0")
				$("#tm_search_occurence").text($("#texte mark").length+" occurences");
<?
}
?>			
				
			});
			oldpos=-1;
			oldpos2=-1;
			function setCursor2() {
					$("#tm_cursor").clearQueue();
					$("#tm_cursor").animate({top: $("#tm_idx_"+pos2).position().top+$("#tm_idx_"+pos2).height()/2-12});					
			}
			
			// Défini la position du curseur
			function setCursor() {
				// Cherche le premier élément H1 qui est affiché
				pos=0;
				pos2=0;
				$( "#texte h1" ).each(function( index ) {
					if ($(this).position().top<$(window).scrollTop()+50-$("#content").position().top) {
						pos=index;
						pos2=index;
						
						// regarde si on est sur un h2
						$(this).nextUntil("#texte h1","h2").each(function(index2) {
							if ($(this).position().top<$(window).scrollTop()+50-$("#content").position().top) {
								pos2=index+"_"+index2;
								
								// regarde si on est sur un h3
								$(this).nextUntil("h2","h3").each(function(index3) {
									if ($(this).position().top<$(window).scrollTop()+50-$("#content").position().top) {
										pos2=index+"_"+index2+"_"+index3;
									}

								});
							}

						});
					}
					
				});
				
				// Affiche ou non les sous-menus
				if (oldpos!=pos) {
					// Cache tous les sous-blocks
					$(".tm_h2").hide(200);
					
					// Affiche celui du block en cours
					$("#tm_h2_"+pos).show(200,function () {
						// Scroll au bon endroit
						
						// Deplace le curseur
						$("#tm_cursor").clearQueue();
						$("#tm_cursor").animate({top: $("#tm_idx_"+pos2).position().top+$("#tm_idx_"+pos2).height()/2-12});					
					
					});
					oldpos=pos;
					oldpos2=pos2;
				} else
				// Positionne le curseur au bon endroit
				if (oldpos2!=pos2) {
					// Scroll au bon endroit
					
					// Déplace le curseur
					$("#tm_cursor").clearQueue();
					$("#tm_cursor").animate({top: $("#tm_idx_"+pos2).position().top+$("#tm_idx_"+pos2).height()/2-12});					

					oldpos2=pos2;
				}
				
			}
		</script>
        <div class="header"><div class="header-inside" id="header">
				<?writeZone($dbh, $page.'_2', "txt_zone",true);?>  
       </div></div>
		<div id='tm'></div>
        <div class="content" id="content"><div class="content-inside" id="texte">
				<?writeZone($dbh, $page.'_1', "txt_zone zone1",true);?>  
       </div></div>
