<?php
	namespace widget;
	
// Cette classe affiche un browser HTML permettant de parcourir un ou plusieurs objet de type "rorganisation" dans son
// intégralité : redevabilites, perimetres, raison d'etre, etc...
class wg_GouvernanceEditor 
{
	// l'élément gouvernance à éditer
	private $_gouvernance;
	
	// Constructeur nécessitant le role à afficher
	// Entrée : le role à afficher
	// Sortie : un objet de type wg_circleBrowser
	public function __construct($gouvernance) 
	{
		$this->_gouvernance=$gouvernance;
	}
		
	public function display() {
?>
<script>

jQuery.fn.extend({
    param: function( a ) { 
        var s = []; 
 
        // If an array was passed in, assume that it is an array 
        // of form elements 
        if ( a.constructor == Array || a.jquery ){
            // Serialize the form elements 
            jQuery.each( a, function(){ 
                s.push(unescape(encodeURIComponent(escape(this.name))) + "=" + unescape(encodeURIComponent(escape(this.value)))); 
            }); 
        } 
        // Otherwise, assume that it's an object of key/value pairs 
        else{ 
            // Serialize the key/values 
            for ( var j in a ) 
                // If the value is an array then the key names need to be repeated 
                if ( a[j] && a[j].constructor == Array ) 
                    jQuery.each( a[j], function(){ 
                        s.push(unescape(encodeURIComponent(escape(j)) + "=" + encodeURIComponent(escape(this)))); 
                    }); 
                else 
                    s.push(unescape(encodeURIComponent(escape(j)) + "=" + encodeURIComponent(escape(a[j])))); 
        } 
        // Return the resulting serialization 
        return s.join("&").replace(/ /g, "+"); 
    },

    serialize: function() { 
        return this.param(this.serializeArray()); 
    }
    

});  

	$("#gouvernanceEditor").ready(function(){


    $( "#dialog-modal" ).dialog({
      autoOpen: false,
      height: 500,
      width: 600,
      modal: true,
      close: function() {
      	  // Enlève tous les boutons
	      $( this ).dialog({ buttons: [ ] });
        //allFields.val( "" ).removeClass( "ui-state-error" );
      }
    });

	    $( ".create-user" )
	      .button()
	      .click(function() {
	      	// Restore le formulaire
	      	str="formulaires/form_action_select.php?tid="+$(this).attr("value")
	      	$( "#formulaire" ).text("Loading...");
	      	$( "#formulaire" ).load(str);
	        $( "#dialog-modal" ).dialog( "open" );
	      });
	


 $("#formulaire").submit(function() {
	// Envoie le formulaire en AJAX (méthode POST), la destination étant définie par l'élément à l'ID form_target 
	data=$("#formulaire").serialize();
 	$.post("formulaires/"+$("#form_target")[0].value, data) 
        .done(function(data, textStatus, jqXHR) {
            if (textStatus="success")
            {
            	// Affiche les données en retour en remplacement du contenu du formulaire (le contenant reste) 
                $("#formulaire")[0].innerHTML=data;
                // Intérprète les scripts retournés (à vérifier si ça fonctionne)
                eval($("#formulaire").find("script").text());
			}
            else {
            	// Problème d'envoi
            	alert("Echec!");
            
            }
        });
        // Bloque la procédure standard d'envoi
        return false;

});
	
	})
</script>
  <style>
    body { font-size: 62.5%; }
    label, input { display:block; }
    input.text { margin-bottom:12px; width:95%; padding: .4em; }
    fieldset { padding:0; border:0; margin-top:25px; }
    h1 { font-size: 1.2em; margin: .6em 0; }
    div#users-contain { width: 350px; margin: 20px 0; }
    div#users-contain table { margin: 1em 0; border-collapse: collapse; width: 100%; }
    div#users-contain table td, div#users-contain table th { border: 1px solid #eee; padding: .6em 10px; text-align: left; }
    .ui-dialog .ui-state-error { padding: .3em; }
    .validateTips { border: 1px solid transparent; padding: 0.3em; }
  </style>

<?php

		echo "<div id='gouvernanceEditor' class='ui-widget'>";
		echo "<h1>Réunion de Gouvernance pour le cercle ".$this->_gouvernance->getCircle()->getName()."</h1>";
		
		// Affiche la liste des tensions
		foreach ($this->_gouvernance->getTension() as $tension) {
			echo "<div>".$tension->getDescription();
			echo "<button class='create-user' value='".$tension->getId()."'>Ajouter une action</button>";
			echo "</div>";
		}

?>
<div id="dialog-modal" title="Ajouter une action">
<form id='formulaire'>

</form>
</div>
<?php
			echo "</div>";	
	}

}

?>