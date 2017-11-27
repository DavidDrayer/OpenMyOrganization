<?php
	namespace widget;
	
// Cette classe affiche un browser HTML permettant de parcourir un objet de type "role" dans son
// intégralité : redevabilites, perimetres, raison d'etre, etc...
class wg_meetingBrowser extends Widget
{
	// l'élément meeting à afficher
	private $_meeting;
	
	// Constructeur nécessitant le role à afficher
	// Entrée : le role à afficher
	// Sortie : un objet de type wg_circleBrowser
	public function __construct(\holacracy\Meeting $meeting) 
	{
		$this->_meeting=$meeting;
	}
	
	public function display() {

		// Contrôle que l'on ai le droit de visualiser cette page
		if (isset($this->_organisation) && !is_array($this->_organisation)) {
		if ( isset($_SESSION["currentUser"])) {
			$isMember=$this->_organisation->isMember($_SESSION["currentUser"]);
			$isAdmin=$this->_organisation->isAdmin($_SESSION["currentUser"]);
			$visibility=$this->_organisation->getVisibility();
			if ($isMember || $isAdmin || $visibility==2) {
				// Ok, ça marche... y a-t-il des choses à faire?
			} else {
				// La visualisation n'est pas souhaitée, redirige sur la page de login.
				
				header("location:index.php");
				exit;
			}
		} else {
			// Le visiteur sera créé après... mais la page est-elle en mode publique ou semi-publique?
			$visibility=$this->_organisation->getVisibility();
			if (!$visibility==2) {
				header("location:index.php");
				exit;
			}
		}}
	
	?>
	
<html>
	<head>
		<title><? echo $this->_meeting->getOrganisation()->getName()." | ".$this->_meeting->getMeetingType() ?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />

	
		<!-- styles needed by jScrollPane -->
		<link href="/style/templates/blue/jquery-ui-1.10.4.custom.css" rel="stylesheet" type="text/css" />	
		<link rel="stylesheet" href="style/templates/<?=$_SESSION["template"]?>/meeting.css" />

		<script src="/plugins/jquery-2.1.0.min.js"></script>
		<script src="/plugins/jquery-ui-1.10.3.custom/js/jquery-ui-1.10.3.custom.min.js"></script>
		<script src="/scripts/meeting.js"></script>

   <script type="text/javascript" src="/js/jquery.ddslick.min.js" ></script>		
		<script>
			
// Script pour afficher l'heure courante
function date_time(id)
{
		date = new Date;
        h = date.getHours();
        if(h<10)
        {
                h = "0"+h;
        }
        m = date.getMinutes();
        if(m<10)
        {
                m = "0"+m;
        }
        result = h+':'+m;
        document.getElementById(id).innerHTML = result;
        
        // Affiche la time graph
        <?
        if ($this->_meeting->getOpeningTime()!="") {
			echo "realstartdate=new Date('".$this->_meeting->getOpeningTime()->format("m/d/Y H:i")."');";
			echo "planifiedstartdate=new Date('".$this->_meeting->getDate()->format("m/d/Y")." ".substr($this->_meeting->getStartTime(),0,5)."');";
		} else {
			echo "realstartdate=new Date('".$this->_meeting->getDate()->format("m/d/Y")." ".substr($this->_meeting->getStartTime(),0,5)."');";
			echo "planifiedstartdate=new Date('".$this->_meeting->getDate()->format("m/d/Y")." ".substr($this->_meeting->getStartTime(),0,5)."');";
		}
        
        if ($this->_meeting->getClosingTime()!="") {
			echo "planifiedenddate=new Date('".$this->_meeting->getDate()->format("m/d/Y")." ".substr($this->_meeting->getEndTime(),0,5)."');";
			echo "date=new Date('".$this->_meeting->getClosingTime()->format("m/d/Y H:i")."');";

		} else {
			echo "planifiedenddate=new Date('".$this->_meeting->getDate()->format("m/d/Y")." ".substr($this->_meeting->getEndTime(),0,5)."');";
			echo "date = new Date;";
		}
 ?>
		if (planifiedenddate<date) {
			enddate=date;
			depassement=date-planifiedenddate;
			date=planifiedenddate;
		} else {
			enddate=planifiedenddate;
			depassement=0;
		}
 
		if (planifiedstartdate<realstartdate) {
			startdate=planifiedstartdate;
			retard=realstartdate-planifiedstartdate;
		} else {
			startdate=realstartdate;
			retard=0;
		}
 
         

        
        full=enddate-startdate;
        current=date-realstartdate;
		percentlate2=100/full*depassement;
		percentlate=100/full*retard;
        percent=100/full*current;
        if (percentlate2>100) percentlate2=100;
        if (percentlate2<0) percentlate2=0;
        if (percentlate>100) percentlate=100;
        if (percentlate<0) percentlate=0;
        if (percent>100) percent=100;
        if (percent<0) percent=0;
        $("#inner_time_graph_late2").width(percentlate2+"%");
       $("#inner_time_graph_late").width(percentlate+"%");
        $("#inner_time_graph").width(percent+"%");
        
        setTimeout('date_time("'+id+'");','1000');
        return true;
}
			
			
			// Script inclu pour y intégrer une référence au meeting courant (impossible dans un fichier inclus)
				function refreshProjects(role) {
		//alert ("RefreshProjects");
		$("#project_role_"+role).load("/class/widget/wg_meetingbrowser/onglet_projets.php?action=refresh&meeting=<?=$this->_meeting->getId()?>&role="+role,codeProjects);
		
		
	}
	function refreshProject(project) {
	$("#proj_"+project).load("/class/widget/wg_meetingbrowser/onglet_projets.php?action=refresh&meeting=<?=$this->_meeting->getId()?>&project="+project,codeProjects);
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
		</script>
		
		
		
		<script src="plugins/tinymce/tinymce.min.js"></script>	
		<script src="plugins/tinymce/jquery.tinymce.min.js"></script>	
		
		<!-- Smartsupp Live Chat script -->
		<script type="text/javascript">
		var _smartsupp = _smartsupp || {};
		_smartsupp.key = '0494c3f917ace7e29405137cc471f391bbd3416a';
		window.smartsupp||(function(d) {
			var s,c,o=smartsupp=function(){ o._.push(arguments)};o._=[];
			s=d.getElementsByTagName('script')[0];c=d.createElement('script');
			c.type='text/javascript';c.charset='utf-8';c.async=true;
			c.src='//www.smartsuppchat.com/loader.js?';s.parentNode.insertBefore(c,s);
		})(document);
		</script>
				
		<script>



			var updateTimer = null;
			function sendContent(txt,id) {
				    if (updateTimer) clearTimeout(updateTimer);
				    updateTimer = eval("setTimeout(function() { $.post( 'ajax/meeting.php', { action: 'sendScratch', id: '"+id+"', txt: '"+escape(txt)+"' } );}, 500)");
				
			}
		
			function refreshScratch(meeting) {
				$("#scratchpad").load("/class/widget/wg_meetingbrowser/refresh.php?refresh=scratchpad&meeting="+meeting);

			}
			function refreshChat(meeting) {
				$("#chat").load("/class/widget/wg_meetingbrowser/refresh.php?refresh=chat&meeting="+meeting, function() {$("#chat").scrollTop($("#chat")[0].scrollHeight);});
			}
			function refreshTension(meeting, scroll=false) {
				$("#tension").load("/class/widget/wg_meetingbrowser/refresh.php?refresh=tension&meeting="+meeting, function() {if (scroll) $("#tension").scrollTop($("#tension")[0].scrollHeight);});
			}
			function refreshHistory(meeting) {
				$("#history").load("/class/widget/wg_meetingbrowser/refresh.php?refresh=history&meeting="+meeting);
			}

		
		
			function checkUpdate(meeting,time) {
				$.getScript("/refresh/meeting.php?id="+meeting+"&time="+time) .done(function( script, textStatus ) {
					  })
					  .fail(function( jqxhr, settings, exception ) {
					  	console.log (jqxhr.responseText);
					    console.log( "Triggered ajaxError handler." );
					});
			}
			function moveUp() {
				newpos=parseInt(($(window).scrollTop()-100)/$(window).height())*$(window).height();
				$(window).scrollTop(newpos);
			}
			function moveDown() {
				newpos=parseInt($(window).scrollTop()/$(window).height()+1)*$(window).height();
				$(window).scrollTop(newpos);
			}
			$(document).ready(function(){
				
				// Affiche l'heure
				date_time('date_time');
				
				// Select du type de tension
				$('#type_tension').ddslick({width:'100%',height:90});
				

				$("#tabsG-1").on( "click", ".chkbx_tension", function(event) {
					// Envoi l'info
						event.preventDefault(); event.stopPropagation();
					$.post( 'ajax/meeting.php', { action: 'checkTension', id: $(this).attr("val") , val: $(this).prop('checked') } )  .done(function( data ) {
						
						if (data.trim()!="") alert( "Data Loaded: " + data );
						refreshTension(<?=$this->_meeting->getId()?>);
					});				
				});
				$("#tabsG-1").on( "click", '.dialogPage2', function(event) {
						event.preventDefault(); event.stopPropagation();
						openDialog ($(this).attr("href"),$(this).attr("alt"));
				});


			
				$("#omo-btnhaut").click(moveUp);
				$("#omo-btnbas").click(moveDown);
				$("#tabs_gauche").tabs({heightStyle: "fill"});
				$("#tabs_droite").tabs({heightStyle: "fill"});
				
				$(window).resize(function () {
						$("#tabs_gauche").tabs({heightStyle: "fill"});
						$("#tabs_droite").tabs({heightStyle: "fill"});
				
				})
				
			$("#btn_sendTension").button().click(function( event ) {
		        event.preventDefault();
		        $.post( 'ajax/meeting.php', { action: 'sendTension', id: '<?=$this->_meeting->getId()?>', txt: $("#txt_tension").val(), user: $("#select_tension_user").val() , role: $("#select_tension_role").val(), type: $("#type_tension").data('ddslick').selectedData.value} )  .done(function( data ) {
    if (data.trim()!="") alert( "Data Loaded: " + data );
    refreshTension(<?=$this->_meeting->getId()?>,true);
  });
		        
		        $("#txt_tension").val("");
		    });;

		
			// Envoie une ligne de CHAT
			$("#btn_sendChat").button().click(function( event ) {
		        event.preventDefault();
		        $.post( 'ajax/meeting.php', { action: 'sendChat', id: '<?=$this->_meeting->getId()?>', txt: $("#txt_chat").val() } );
		        refreshChat(<?=$this->_meeting->getId()?>);
		        $("#txt_chat").val("");
		    });;
	
		       $('.tinymce').tinymce({
		            // Location of TinyMCE script
					menubar : false,
					height : "90%",
					theme_advanced_resize_vertical : true,
					plugins: "link",
					toolbar: "bold italic strikethrough | bullist numlist outdent indent | link",
					statusbar : false,
					
					setup : function(ed) {
		                  ed.on('keyup', function(e) {
		                     sendContent(ed.getContent(),<?=$this->_meeting->getId()?>);
		                  });
		            }
		
		        });
				
				// Boutons
				$("#btn_openMeeting").button();
				$("#btn_closeMeeting").button();
				$("#btn_editTension").button();
				$("#btn_setSecretary").button();
			
				function resizeUi() {
				    var h = $(window).height();
				    var w = $(window).width();
				    $(".omo-main-table").css('height', h-10 );
				};
				var resizeTimer = null;
				$(window).bind('resize', function() {
				    if (resizeTimer) clearTimeout(resizeTimer);
				    resizeTimer = setTimeout(resizeUi, 100);
				});
				resizeUi();
				
				$("#chat").scrollTop($("#chat")[0].scrollHeight);

				
				// Une fois tout modifié, cache l'écran de chargement
				$("#main_waiting_screen").css("display","none");
			});
		</script>
	</head>
	<body>	
		<? echo "<div id='main_waiting_screen'>".\widget\Widget::FULL_WAITING_SCREEN."</div>";?>
		<div class='header'><?$this->_displayNav($this->_meeting);?></div>
		<div class='omo-maindiv' >
			<div class='omo-leftcol' >
			
<div id="tabs_gauche">
  <ul>
    <li><a name="tabsG-1" href="#tabsG-1"><? print T_("Tensions"); ?></a></li>
    <li><a name="tabsG-2" href="#tabsG-2"><? print T_("Scratchpad"); ?></a></li>
    <li><a name="tabsG-3" href="#tabsG-3"><? print T_("Chat"); ?></a></li>
    </ul>
     <div id="tabsG-1" style="position:relative; height:100%;">
		<div id='tension' class="unselectable"  unselectable="on" style='overflow-y:auto; margin-bottom:5px;padding:5px;height: -moz-calc(100% - (130px));  height: -webkit-calc(100% - (130px));  height: calc(100% - (130px));border:1px solid rgba(255, 255, 255, 1); background-color:rgba(255, 255, 255, 0.5);'>

<?
	$meeting=$this->_meeting;
	include_once("wg_meetingbrowser/content_tension.php");
?>	
	 </div>
<?
	$isMember=(isset($_SESSION["currentUser"]) && $_SESSION["currentUser"]->isMember($this->_meeting->getCircle()));
	$isSecretary=(isset($_SESSION["currentUser"]) && $_SESSION["currentUser"]->getId()>1 && $this->_meeting->getSecretaryId()==$_SESSION["currentUser"]->getId());
	// Le meeting est-il en cours?
	$isInProcess=($this->_meeting->getOpeningTime()!=null && $this->_meeting->getClosingTime()==null);


	// Version du secrétaire, permet d'assigner une autre personne à une tension
	
	if ($isSecretary && $isInProcess) {
	 
	 // Ajoute le type de tension (tension, opportunités, info)
	 echo "<table style='width:100%'><tr><td style='width:15%'>";
	 echo "<select name='type_tension' id='type_tension'>";
	 echo "<option title='Tension' value=1 data-imagesrc='/images/icon_tension.png'></option>";
	 echo "<option title='Point info' value=2  data-imagesrc='/images/icon_info.png'></option>";
	 echo "<option title='Urgent' value=3  data-imagesrc='/images/icon_important.png'></option>";
	 echo "<option value=0 data-imagesrc='/images/icon_point.png' selected=''></option>";
	 echo "</select>";
	 echo "</td><td style='width:85%'>";
	 echo "<input id='txt_tension' style='width: 100%;' ";
	 if (!isMember || $this->_meeting->getClosingTime()!="") echo "disabled";
	 echo ">";
	 echo "</td></tr></table>";
 

	 echo "<select name='select_tension_user' id='select_tension_user'>";
	 echo "<option value=''>Choisissez un membre...</option>";
	 $members=$this->_meeting->getCircle()->getMembers();
	 
	foreach($members as $membre) {
		echo "<option value='".$membre->getId()."'";
		if ($membre->getId()==$_SESSION["currentUser"]->getId()) echo " selected ";
		echo "> &nbsp;".$membre->getFirstName()." ".$membre->getLastName()."</option>";
	}
	 
	 echo "</select>";
	 echo "<button id='btn_sendTension' ";
	 if ( $this->_meeting->getClosingTime()!="") echo "disabled";
	 echo " >Ajouter</button>";

	} else {
	// Version simple, je rajoute des tensions pour moi, evt en précisant mon rôle

	 // Ajoute le type de tension (tension, opportunités, info)
	 echo "<table style='width:100%'><tr><td style='width:15%'>";
	 echo "<select name='type_tension' id='type_tension' ";
	 if (!$isMember || $this->_meeting->getClosingTime()!="") echo "disabled";
	 echo ">";
	 echo "<option title='Tension' value=1 data-imagesrc='/images/icon_tension.png'></option>";
	 echo "<option title='Point info' value=2  data-imagesrc='/images/icon_info.png'></option>";
	 echo "<option title='Urgent' value=3  data-imagesrc='/images/icon_important.png'></option>";
	 echo "<option value=0 data-imagesrc='/images/icon_point.png' selected=''></option>";
	 echo "</select>";
	 echo "</td><td style='width:85%'>";
	 echo "<input id='txt_tension' style='width: 100%;' ";
	 if (!$isMember || $this->_meeting->getClosingTime()!="") echo "disabled";
	 echo ">";
	 echo "</td></tr></table>";
	 
	 echo "<select name='select_tension_role' id='select_tension_role' ";
	 if (!$isMember || $this->_meeting->getClosingTime()!="") echo "disabled";
	 echo ">";
	 echo "<option value=''>Choisissez votre rôle...</option>";
	 	 $roles=$_SESSION["currentUser"]->getRoles($this->_meeting->getCircle());
	 
	foreach($roles as $role) {
		echo "<option value='".$role->getId()."'";
		echo "> &nbsp;".$role->getName()."</option>";
	}

	 echo "</select>";
	 echo "<button id='btn_sendTension' ";
	 if (!$isMember || $this->_meeting->getClosingTime()!="") echo "disabled";
	 echo " >Ajouter</button>";
	}
?>	
	
	
	 </div>		 
     <div id="tabsG-2" style="position:relative; height:100%;">
<?
	
	

	// Est-ce le secrétaire du cercle ou de cette rencontre?
	if ($this->_meeting->getSecretaryId()==$_SESSION["currentUser"]->getId() && $this->_meeting->getOpeningTime()!="" && !$this->_meeting->getClosingTime()!="") {
		// Affiche un scratchpad éditable
?>
	 <div id='editable_scratchpad' class='tinymce' style= 'height:90%;border:1px solid black; background-color:#FFFFFF; padding:5px;'><?=$this->_meeting->getScratchpad()?></div>
<?		
		
	} else {
?>     
	 <div id='scratchpad' class='unselectable'  unselectable="on" style='height:99%; border:1px solid rgba(255, 255, 255, 1); background-color:rgba(255, 255, 255, 0.5); padding:5px; overflow-y:auto'><?=$this->_meeting->getScratchpad()?></div>
<?
	}
?>
	 </div>
     <div id="tabsG-3">
	 
	 <div id='chat' class="unselectable"  unselectable="on" style='overflow-y:auto; margin-bottom:5px;padding:5px;height: -moz-calc(100% - (130px));  height: -webkit-calc(100% - (130px));  height: calc(100% - (130px));border:1px solid rgba(255, 255, 255, 1); background-color:rgba(255, 255, 255, 0.5);'>
<?
	$meeting=$this->_meeting;
	include_once("wg_meetingbrowser/content_chat.php");
?>	
	
	 </div>
	 <textarea id='txt_chat' style='height:80px; width: 100%; resize: none; ' <? if ($this->_meeting->getOpeningTime()=="" || $this->_meeting->getClosingTime()!="") echo "disabled";?>></textarea><button id='btn_sendChat' <? if ($this->_meeting->getOpeningTime()=="" || $this->_meeting->getClosingTime()!="") echo "disabled";?> >Envoyer</button>
	 </div>

</div>			
			
			

			</div>
			<div class='omo-rightcol'>
				
				
			<div id="tabs_droite">
 <ul>
    <li><a name="tabs-1" href="#tabs-1"><span class='omo-pv'><span class='omo-tab-label'>Réunion</span></span></a></li>
    <li><a name="tabs-2" href="#tabs-2"><span class='omo-role'><span class='omo-tab-label'><? print T_("R&ocirc;les"); ?></span></span></a></li>
    <li><a name="tabs-3" href="#tabs-3"><span class='omo-politic'><span class='omo-tab-label'><? print T_("Politiques"); ?></span></span></a></li>
    <li><a name="tabs-4" href="#tabs-4"><span class='omo-checklist'><span class='omo-tab-label'><? print T_("Check-lists"); ?></span></span></a></li>
    <li><a name="tabs-5" href="#tabs-5"><span class='omo-metrics'><span class='omo-tab-label'><? print T_("Indicateurs"); ?></span></span></a></li>
    <li><a name="tabs-6" href="#tabs-6"><span class='omo-projects'><span class='omo-tab-label'><? print T_("Projets"); ?></span></span></a></li>
	
  </ul>
  
<!-- Onglet 1, avec les différents rôles -->  
<div id="tabs-1">
	<div class='omo-box omo-lightbackground ui-corner-all' style='height: -moz-calc(100% - (36px));  height: -webkit-calc(100% - (36px));  height: calc(100% - (35px)); overflow-y:auto'> 
				<fieldset><legend>Informations générales sur la rencontre.</legend>
<?
	echo "<div style='float:right'><a href='http://appear.in/".md5("omo_meeting_".$this->_meeting->getId()."_".$this->_meeting->getOrganisation()->getId())."' target='_new'><img src='/style/templates/images/appearin.png' title='Ouvrir un espace de rencontre virtuel pour cette réunion.'></a></div>";

	echo "<p>Date: ".$this->_meeting->getDate()->format("d.m.Y")."</p>";
	echo "<p>".($this->_meeting->getOpeningTime()!=""?"<b>Heure de début: ".$this->_meeting->getOpeningTime()->format("H:i")."</b> (convoqué à : ".substr($this->_meeting->getStartTime(),0,5).")":"Heure de convoquation: ".substr($this->_meeting->getStartTime(),0,5))."</p>";
	echo "<p>".($this->_meeting->getClosingTime()!=""?"<b>Heure de fin: ".$this->_meeting->getClosingTime()->format("H:i")."</b> (planifié à : ".substr($this->_meeting->getEndTime(),0,5).")":"Heure de fin: planifié à ".substr($this->_meeting->getEndTime(),0,5))."</p>";
	//echo "<p>Heure de fin: ".$this->_meeting->getEndTime()."</p>";
	echo "<p>Lieu: ".($this->_meeting->getLocation()!=""?$this->_meeting->getLocation():"<i>indéfini</i>")."</p>";
	// Si le secrétaire est défini, affiche le secrétaire 
	//if () {
	//echo "<p>Secrétaire: ".($this->_meeting->getLocation()!=""?$this->_meeting->getLocation():"<i>indéfini</i>")."</p>";
	//}
	
	if ($this->_meeting->getClosingTime()!="") {
		// terminé?
		echo '<span id="date_time" style="font-size:50px;display:none"></span>';
		// Affichage des statistiques
		
	} else {
		echo '<span id="date_time" style="font-size:50px;"></span>';
	}
	echo '<div id="time_graph" style="position:relative; border:1px solid black; height:10px; overflow:hidden"><div id="inner_time_graph_late" style="display:inline-block;background:#FF0000; height:100%"></div><div id="inner_time_graph" style="display:inline-block;background:#FFFF00; height:100%"></div><div id="inner_time_graph_late2" style="display:inline-block;background:#FF0000; height:100%"></div></div>';
	
	

	
?>
				</fieldset><p>
				
<?	
	if ($this->_meeting->getClosingTime()!="") {
		echo "La réunion a été clôturée le ".$this->_meeting->getClosingTime()->format("d.m.Y ")."à".$this->_meeting->getClosingTime()->format(" H:i");
	}
	// Uniquement pour le 1er lien et le secrétaire
	// Affichage des boutons
	if (isset($_SESSION["currentUser"]) && $_SESSION["currentUser"]->isMember($this->_meeting->getCircle())) {
	if ($this->_meeting->getSecretaryId()==$_SESSION["currentUser"]->getId()) {

		// Bouton pour démarrer la rencontre
		if ($this->_meeting->getOpeningTime()!="") {
			if ($this->_meeting->getClosingTime()!="") {
				//echo T_(utf8_encode("La réunion a été clôturée le ")).$this->_meeting->getClosingTime()->format("d.m.Y ".T_(utf8_encode("à"))." H:i");
			} else {
				// Différence entre une réunion de triage et une réunion de gouvernance
				if ($this->_meeting->getMeetingTypeId()==1) {
					echo "<a id='btn_editTension' href='formulaires/form_gouvernance.php?meeting=".$this->_meeting->getId()."&circle=".$this->_meeting->getCircle()->getId()."' class='dialogPage' alt='".T_("Traiter une tension")."'>".T_("Traiter une tension")."</a>";
				} else if ($this->_meeting->getMeetingTypeId()==3) {
					echo "<a id='btn_editTension' href='formulaires/form_mixte.php?meeting=".$this->_meeting->getId()."&circle=".$this->_meeting->getCircle()->getId()."' class='dialogPage' alt='".T_("Traiter une tension")."'>".T_("Traiter une tension")."</a>";
				} else{
					echo "<a id='btn_editTension' href='formulaires/form_triage.php?meeting=".$this->_meeting->getId()."&circle=".$this->_meeting->getCircle()->getId()."' class='dialogPage' alt='".T_("Traiter une tension")."'>".T_("Traiter une tension")."</a>";
				
				}
			echo "<a class='ajax' id='btn_closeMeeting' href='ajax/meeting.php?action=close&id=".$this->_meeting->getId()."' alt='".T_("Terminer la reunion")."' check='".T_("Etes-vous sur de vouloir terminer la reunion? Vous ne pourrez plus rien modifier par la suite.")."'>".T_("Cl&ocirc;turer la reunion")."</a>";
			}
		} else {
			echo "<a class='ajax' id='btn_openMeeting' href='ajax/meeting.php?action=start&id=".$this->_meeting->getId()."' alt='".T_("Commencer la reunion")."'>".T_("Commencer la reunion")."</a>";
		}
	} else {
		// Ce n'est pas le secrétaire officiel... mais si la réunion n'est pas ouverte, il y a la possibilité de le remplacer
		if ($this->_meeting->getOpeningTime()=="") {
			echo "<a class='ajax' id='btn_setSecretary' href='ajax/meeting.php?action=setsecretary&id=".$this->_meeting->getId()."' check='"."Êtes vous certain de vouloir remplacer le secrétaire?"."' title='Secrétaire actuellement défini: ".($this->_meeting->getSecretaryId()>0?$this->_meeting->getSecretary()->getUserName():"aucun")."'>".T_("Remplacer le secr&eacute;taire")."</a>";
		}
	}}

?>	</p>	<fieldset><legend>Historique de la réunion</legend>	
				
	<div id='history'>
<?	
	$historique=$this->_meeting->getHistory();
	include_once("wg_meetingbrowser/content_history.php");
?>
	</div></fieldset>


	

				</div>
</div>
  <div id="tabs-2">
  <fieldset><legend><div id="mask1"></div><span class='omo-structural'><? print T_("R&ocirc;les structurels"); ?></span><div id="mask2"></div></legend>
  <div id="accordion3">
 
<?php
	// Affichage des rôles structurels
	$this->listeRole($this->_meeting->getCircle()->getRoles(\holacracy\Role::STRUCTURAL_ROLES,\holacracy\Circle::TYPE_ORDER),2);
?>
  </div>
  </fieldset>
<?
	$liste=$this->_meeting->getCircle()->getRoles(\holacracy\Role::CIRCLE);
	if (count($liste)>0) {
?>
    <fieldset><legend><div id="mask1"></div><? print T_("Sous-Cercles"); ?><div id="mask2"></div></legend>
  <div id="accordion">
 
<?php
	// Affichage des autres rôles (non-structurels)
	$this->listeRole ($liste);
?>
  </div>
  </fieldset>
<?
	}

	// Affiche si nécessaire la liste des liens transverse non assignés (uniquement pour le premier lien)
	$liste=$this->_meeting->getCircle()->getLinks(\holacracy\Circle::SOURCE_LINK , false);
	if (count($liste)>0) {
?>
    <fieldset><legend><div id="mask1"></div><? print T_("Liens transverses"); ?><div id="mask2"></div></legend>
  <div id="accordion">
 
<?php
	// Affichage des autres rôles (non-structurels)
	echo "Il y a ".count($liste). " ".(count($liste)>1?"liens transverses qui ne sont pas assignés à des rôles":"lien transverse qui n'est pas assigné à un rôle");
?>
  </div>
  </fieldset>
<?
	}
?>
   <fieldset><legend><div id="mask1"></div><? print T_("R&ocirc;les"); ?><div id="mask2"></div></legend>
  <div id="accordion">
 
<?php
	// Affichage des autres rôles (non-structurels)
	$liste=$this->_meeting->getCircle()->getRoles(\holacracy\Role::STANDARD_ROLE | \holacracy\Role::LINK_ROLE);
	if (count($liste)>0) 
		$this->listeRole ($liste);
	else
		print T_("Aucun r&ocirc;le");
?>
  </div>
  </fieldset>
  
</div>

<!-- Autres onglets  -->  
  <div id="tabs-3">
     <? include "wg_meetingbrowser/onglet_politique.php" ?>
  </div>

  <div id="tabs-4">
     <? 
     	$checklist=$this->_meeting->getCircle()->getChecklist();
	 	include "wg_meetingbrowser/onglet_checklist.php"; 
	?>
  </div>
  <div id="tabs-5">
    <? 
		$metrics=$this->_meeting->getCircle()->getAllMetrics();
		include "wg_meetingbrowser/onglet_metrics.php"; 
	?>
  </div>
  <div id="tabs-6">
    <? include "wg_meetingbrowser/onglet_projets.php" ?>
  </div>
			  </div>
				
			</div>
			
							
				
				

			
					
		</div>	
	</div> 
	</body>
</html>
<script>
		window.setInterval(function() {checkUpdate(<?=$this->_meeting->getId()?>,3)}, 3000);
</script>
	<?php
	}
}

?>
