 $(function() {

    $( "#dialog-item" ).dialog({
	  autoOpen: false,
      resizable: false,
      height: 160,
      width: 250,
      modal: true,
      buttons: {
        "Ajouter un item": function() {
		 var $box = $("#dialog-item");		
        $( this ).dialog( "close" ); //Fermer la fenetre 
        },
        Annuler: function() {
        $( this ).dialog( "close" );
        }
      },
	  close: function() {
      }
    });
	
	$( "#dialog-action" ).dialog({
	  autoOpen: false,
      resizable: false,
      height: 160,
      width: 280,
      modal: true,
      buttons: {
        "Ajouter une action": function() {
		 var $box = $("#dialog-action");		
        $( this ).dialog( "close" ); //Fermer la fenetre 
        },
        Annuler: function() {
        $( this ).dialog( "close" );
        }
      },
	  close: function() {
      }
    });
	
	$( "#dialog-project" ).dialog({
	  autoOpen: false,
      resizable: false,
      height: 160,
      width: 280,
      modal: true,
      buttons: {
        "Ajouter une action": function() {
		 var $box = $("#dialog-project");		
        $( this ).dialog( "close" ); //Fermer la fenetre 
        },
        Annuler: function() {
        $( this ).dialog( "close" );
        }
      },
	  close: function() {
      }
    });
	
	$( "#dialog-tension" ).dialog({
	  autoOpen: false,
      resizable: false,
      height: 160,
      width: 280,
      modal: true,
      buttons: {
        "Ajouter une action": function() {
		 var $box = $("#dialog-tension");		
        $( this ).dialog( "close" ); //Fermer la fenetre 
        },
        Annuler: function() {
        $( this ).dialog( "close" );
        }
      },
	  close: function() {
      }
    });

var isG = false;
 
$(document).keydown(function(e)
{
	if (e.which == 71 || e.keyCode == 71)
	{ 
		isG = true; // si la touche G a été pressée
	}
}).keyup(function(e)
{
	if ($('input:focus').length > 0 || $('textarea:focus').length > 0 || isG != true)
	{ 
		isG = false; // Si on se trouve dans un input, une textarea ou si on n'a pas pressé la touche G, on ne peut pas faire des raccourcis clavier
		return false;
	}
 
	if (e.keyCode == true)
	{
		var key = e.keyCode;
	} 
	else 
	{
		var key = e.which;
	}
 
	switch (key) // On regarde la deuxième touche pressée par l'utilisateur
	{
		// G + I
		case 73:
			$("#dialog-item").dialog("open"); //Ouvre la fenetre
			/*
			<div id="dialog-item" title="Capturer un item">
			<p>Formulaire ici pour capturer un item</p>
			</div>
			*/
			return false;
			break;
		// G + A
		case 65:
			$("#dialog-action").dialog("open"); //Ouvre la fenetre
			/*
			<div id="dialog-action" title="Capturer une action">
			<p>Formulaire ici pour capturer une action</p>
			</div>
			*/
			return false;
			break;
		// G + P
		case 80:
			$("#dialog-project").dialog("open"); //Ouvre la fenetre
			/*
			<div id="dialog-project" title="Capturer un projet">
			<p>Formulaire ici pour capturer un projet</p>
			</div>
			*/
			return false;
			break;
		// G + T
		case 84:
			$("#dialog-tension").dialog("open"); //Ouvre la fenetre
			/*
			<div id="dialog-tension" title="Capturer une tension">
			<p>Formulaire ici pour capturer une tension</p>
			</div>
			*/
			return false;
			break;
	}
	
	isG = false; // On réinitialise le booléen
});

}); 