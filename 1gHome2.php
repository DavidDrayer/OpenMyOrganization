<? include('lib/navLib.php') ?>


<script>
	function my_scrollTo (target) {
		var body = $("html, body");
		body.animate({scrollTop:$(target).offset().top-60}, '500', 'swing');
	}
    $(document).ready(function() {
		$('.footer').scrollToFixed( {
            bottom: 0,
            limit: function() { return $('.footer_anchor').offset().top},
            //preFixed: function() { $(this).css('color', 'blue'); },
            //postFixed: function() { $(this).css('color', ''); },
        });

        $('.header').scrollToFixed();
        $('.header').bind('fixed.ScrollToFixed', function() { 
				$("#secondlogo").stop().animate({
					width: $("#secondlogo img").outerWidth(true)
					}, 500);
				});
				//$(this).css('color', 'red'); 

        $('.header').bind('unfixed.ScrollToFixed', function() { 
				$("#secondlogo").stop().animate({
					width: "0px"
					}, 500);
				 });

 


	

  
    });
    $(window).load(function() {
		$('.banner').css("background-color","transparent");
		$('.banner').css("background-image","none");

		$('#onglet1').bind('unfixed.ScrollToFixed', function() { 
			// Change la largeur du text
			$('#onglet1 .onglet_txt').css('width', '300px')
			$('#onglet1 .btn_up').css('display', 'none')
			
			// Animation de l'onglet suivant:
			
			 $( "#onglet2" ).stop().animate({
				height: "38px"
				}, 500);
			});	
				
		$('#onglet1').bind('fixed.ScrollToFixed', function() { 
			// Change la largeur du text
			$('#onglet1 .onglet_txt').css('width', '200px')
			$('#onglet1 .btn_up').css('display', '')

			// Animation de l'onglet suivant:
			
			 $( "#onglet2" ).stop().animate({
				height: "0px"
				}, 500);
			});

		$('#onglet2').bind('unfixed.ScrollToFixed', function() { 
			// Change la largeur du text
			$('#onglet2 .onglet_txt').css('width', '300px')
			$('#onglet2 .btn_up').css('display', 'none')
		});
		$('#onglet2').bind('fixed.ScrollToFixed', function() { 
			// Change la largeur du text
			$('#onglet2 .onglet_txt').css('width', '200px')
			$('#onglet2 .btn_up').css('display', '')
		});


		$('#onglet1').scrollToFixed ({
			bottom: $('.footer').outerHeight(true),
			limit: function() { return $('#anchor_onglet1').offset().top+$('#anchor_onglet1').outerHeight()},
			zIndex: 999,
		});
		
		$('#onglet2').scrollToFixed ({
			
			bottom: $('.footer').outerHeight(true),
			limit: function() { return $('#anchor_onglet2').offset().top+$('#anchor_onglet2').outerHeight()},
			zIndex: 998,
		});
	});
</script>
		
 <div class="banner">
   <div class="parallax-window" data-parallax="scroll" data-image-src="/images/uploads/banner.jpg">
   <div id='mainlogo'><img src='images/uploads/logo.png'></div>
   </div>     
 </div>

 
        <div class="header">
            <div id='secondlogo'><img src='images/uploads/logo2.png'></div>
            <div class="secondNav"><table align="right"  cellspacing="3"><tr><td style='vertical-align:top'><? displayNav('child','</td><td>', 3, getRoot($page,1));?></td></tr></table></div>
			<div class="mainNav"><? displayNav('child','', 2, getRoot($page,1));?></div>
			

        </div>

        <div class="content">
			<div class="path">Vous êtes ici: <? displayNav('path',' &gt; ', 1, getRoot($page,1));?></div>
				
				<?writeZone($dbh, $page.'_1', "txt_zone zone1",true);?>     
                
                    <a name='anchor_onglet1' id='anchor_onglet1' style='position:absolute'></a>
                    <div class='onglet onglet_couleur' id='onglet1'  ><div class="onglet_top"><a href="#" onclick='my_scrollTo("#anchor_onglet1"); return false;'><div class='btn_up' style='display:none'></div><?writeZone($dbh, $page.'_6', "onglet_txt",true);?></a></div></div>
                    
                    <div class='zone_couleur'>
				<?writeZone($dbh, $page.'_2', "txt_zone zone2",true);?>                   
                  <a name='anchor_onglet2' id='anchor_onglet2' style='position:absolute'></a>
                   <div id='onglet2' class='onglet onglet_blanc'><div class="onglet_top"><a href="#" onclick='my_scrollTo("#anchor_onglet2"); return false;'><div class='btn_up' style='display:none'></div><?writeZone($dbh, $page.'_7', "onglet_txt",true);?></a></div></div>
                     </div><div class='zone_blanc'>
			<?writeZone($dbh, $page.'_3', "txt_zone zone3",true);?>   
			</div>
        </div>
        <div class='footer_anchor'></div>
        <div class="footer">
            <?writeZone($dbh, $page.'_4', "footer_txt",true);?>   
        </div>
        <div class="footer-content">
			<?writeZone($dbh, $page.'_5', "footer_content",true);?>   
        </div>
			<div align="center" class="signature">Dernière mise à jour : <? writeDate(); ?> | Design (c) 2015 &gt; <a href="javascript:loadEditor()">Powered by Liquid Edition</a></div>

    
  


